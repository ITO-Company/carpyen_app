<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    proyectos: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    proyecto_id: "",
    deuda_total: "",
    numero_deudas: 1,
    pagos: [],
});

const montoActual = ref("");
const fechaActual = ref("");
const metodoActual = ref("");

const deudaPorPago = computed(() => {
    if (!form.deuda_total || !form.numero_deudas) return 0;
    const deuda = parseFloat(form.deuda_total) || 0;
    const deudas = parseFloat(form.numero_deudas) || 1;
    return (deuda / deudas).toFixed(2);
});

const totalPagos = computed(() => {
    return form.pagos.reduce(
        (sum, pago) => sum + (parseFloat(pago.total) || 0),
        0
    );
});

const diferenciaTotal = computed(() => {
    const deuda = parseFloat(form.deuda_total) || 0;
    const total = totalPagos.value || 0;
    return (deuda - total).toFixed(2);
});

const esValido = computed(() => {
    return form.proyecto_id && form.deuda_total && form.numero_deudas;
});

const agregarPago = () => {
    if (!fechaActual.value || !montoActual.value) {
        return;
    }

    form.pagos.push({
        fecha: fechaActual.value,
        total: parseFloat(montoActual.value),
        metodo_pago: metodoActual.value || "Pendiente",
    });

    montoActual.value = "";
    fechaActual.value = "";
    metodoActual.value = "";
};

const eliminarPago = (index) => {
    form.pagos.splice(index, 1);
};

const autoCompletarPagos = () => {
    if (!form.numero_deudas || !form.deuda_total) {
        return;
    }

    form.pagos = [];
    const monto = form.deuda_total / form.numero_deudas;
    const fechaInicio = new Date();

    for (let i = 0; i < form.numero_deudas; i++) {
        const fecha = new Date(fechaInicio);
        fecha.setMonth(fecha.getMonth() + i);
        form.pagos.push({
            fecha: fecha.toISOString().split("T")[0],
            total: parseFloat(monto.toFixed(2)),
            metodo_pago: "Pendiente",
        });
    }
};

const formatearFecha = (fecha) => {
    if (!fecha) return "";
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });
};

const formatearMoneda = (valor) => {
    const numValue = parseFloat(valor) || 0;
    if (isNaN(numValue)) return "Bs. 0,00";
    return new Intl.NumberFormat("es-BO", {
        style: "currency",
        currency: "BOB",
    }).format(numValue);
};

const enviar = () => {
    if (!form.proyecto_id || !form.deuda_total) {
        return;
    }

    if (form.pagos.length === 0) {
        return;
    }

    if (Math.abs(diferenciaTotal.value) > 0.01) {
        return;
    }

    form.post("/planesPago");
};
</script>

<template>
    <Head title="Crear Plan de Pago" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl leading-tight"
                    style="color: var(--theme-text-primary)"
                >
                    Nuevo Plan de Pago
                </h2>
                <Link href="/planesPago" class="btn btn-secondary">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <form @submit.prevent="enviar" class="space-y-8">
                        <!-- Sección 1: Información Básica del Plan -->
                        <div class="space-y-6">
                            <div
                                class="flex items-center gap-3 pb-4 border-b"
                                style="border-color: var(--theme-border)"
                            >
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg"
                                    style="
                                        background-color: var(
                                            --theme-primary-alpha
                                        );
                                        color: var(--theme-primary);
                                    "
                                >
                                    <span class="font-bold text-sm">1</span>
                                </div>
                                <h3
                                    class="text-lg font-semibold"
                                    style="color: var(--theme-text-primary)"
                                >
                                    Información Básica del Plan
                                </h3>
                            </div>

                            <div class="form-grid">
                                <!-- Proyecto -->
                                <div class="form-group">
                                    <label for="proyecto_id" class="form-label">
                                        Proyecto
                                        <span class="text-error">*</span>
                                    </label>
                                    <select
                                        id="proyecto_id"
                                        v-model="form.proyecto_id"
                                        class="input theme-input"
                                        required
                                    >
                                        <option value="">
                                            -- Selecciona un proyecto --
                                        </option>
                                        <option
                                            v-for="proyecto in proyectos"
                                            :key="proyecto.id"
                                            :value="proyecto.id"
                                        >
                                            {{ proyecto.nombre }} ({{
                                                proyecto.cliente?.nombre
                                            }})
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.proyecto_id"
                                        class="form-error"
                                    >
                                        {{ form.errors.proyecto_id }}
                                    </div>
                                </div>

                                <!-- Deuda Total -->
                                <div class="form-group">
                                    <label for="deuda_total" class="form-label">
                                        Deuda Total (Bs)
                                        <span class="text-error">*</span>
                                    </label>
                                    <input
                                        id="deuda_total"
                                        v-model="form.deuda_total"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        class="input theme-input"
                                        placeholder="0.00"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.deuda_total"
                                        class="form-error"
                                    >
                                        {{ form.errors.deuda_total }}
                                    </div>
                                </div>

                                <!-- Número de Pagos -->
                                <div class="form-group">
                                    <label
                                        for="numero_deudas"
                                        class="form-label"
                                    >
                                        Número de Pagos
                                        <span class="text-error">*</span>
                                    </label>
                                    <input
                                        id="numero_deudas"
                                        v-model.number="form.numero_deudas"
                                        type="number"
                                        min="1"
                                        class="input theme-input"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Monto Sugerido -->
                            <div
                                v-if="esValido"
                                class="p-4 rounded-lg"
                                style="
                                    background-color: var(
                                        --theme-primary-alpha
                                    );
                                    border: 1px solid var(--theme-primary);
                                "
                            >
                                <p
                                    class="text-sm"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    <span class="font-semibold"
                                        >Monto sugerido por pago:</span
                                    >
                                    <span
                                        class="font-bold ml-2"
                                        style="color: var(--theme-primary)"
                                        >{{
                                            formatearMoneda(deudaPorPago)
                                        }}</span
                                    >
                                </p>
                            </div>
                        </div>

                        <!-- Sección 2: Gestión de Pagos -->
                        <div v-if="esValido" class="space-y-6">
                            <div
                                class="flex items-center gap-3 pb-4 border-b"
                                style="border-color: var(--theme-border)"
                            >
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg"
                                    style="
                                        background-color: var(
                                            --theme-success-alpha
                                        );
                                        color: var(--theme-success);
                                    "
                                >
                                    <span class="font-bold text-sm">2</span>
                                </div>
                                <h3
                                    class="text-lg font-semibold"
                                    style="color: var(--theme-text-primary)"
                                >
                                    Definir Pagos
                                </h3>
                            </div>

                            <!-- Botón Auto-llenar -->
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    @click="autoCompletarPagos"
                                    class="btn btn-secondary"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <polyline
                                            points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"
                                        ></polyline>
                                        <line
                                            x1="12"
                                            y1="12"
                                            x2="20"
                                            y2="7.5"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="12"
                                            x2="12"
                                            y2="21"
                                        ></line>
                                        <line
                                            x1="12"
                                            y1="12"
                                            x2="4"
                                            y2="7.5"
                                        ></line>
                                    </svg>
                                    Auto-llenar {{ form.numero_deudas }} pagos
                                </button>
                            </div>

                            <!-- Formulario para agregar pago -->
                            <div
                                class="p-6 rounded-lg"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        var(--theme-bg-secondary) 0%,
                                        rgba(0, 0, 0, 0.02) 100%
                                    );
                                    border: 1px solid var(--theme-border);
                                "
                            >
                                <div class="flex items-center gap-2 mb-6">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        style="color: var(--theme-primary)"
                                    >
                                        <line
                                            x1="12"
                                            y1="5"
                                            x2="12"
                                            y2="19"
                                        ></line>
                                        <line
                                            x1="5"
                                            y1="12"
                                            x2="19"
                                            y2="12"
                                        ></line>
                                    </svg>
                                    <h4
                                        class="font-semibold text-base"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        Agregar Nuevo Pago
                                    </h4>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label
                                            for="fecha_actual"
                                            class="form-label"
                                        >
                                            <span
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                                >Fecha de Pago</span
                                            >
                                            <span class="text-error">*</span>
                                        </label>
                                        <div class="relative">
                                            <input
                                                id="fecha_actual"
                                                v-model="fechaActual"
                                                type="date"
                                                class="input theme-input w-full"
                                                style="
                                                    background-color: var(
                                                        --theme-bg-primary
                                                    );
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                    border: 1px solid
                                                        var(--theme-border);
                                                "
                                            />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            for="monto_actual"
                                            class="form-label"
                                        >
                                            <span
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                                >Monto (Bs)</span
                                            >
                                            <span class="text-error">*</span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <span
                                                class="absolute left-3 font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                "
                                                >Bs</span
                                            >
                                            <input
                                                id="monto_actual"
                                                v-model="montoActual"
                                                type="number"
                                                step="0.01"
                                                min="0.01"
                                                class="input theme-input w-full pl-10"
                                                placeholder="0.00"
                                                style="
                                                    background-color: var(
                                                        --theme-bg-primary
                                                    );
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                    border: 1px solid
                                                        var(--theme-border);
                                                "
                                            />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            for="metodo_actual"
                                            class="form-label"
                                        >
                                            <span
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                                >Método de Pago</span
                                            >
                                        </label>
                                        <input
                                            id="metodo_actual"
                                            v-model="metodoActual"
                                            type="text"
                                            class="input theme-input"
                                            placeholder="Ej: transferencia, QR, efectivo"
                                            style="
                                                background-color: var(
                                                    --theme-bg-primary
                                                );
                                                color: var(
                                                    --theme-text-primary
                                                );
                                                border: 1px solid
                                                    var(--theme-border);
                                            "
                                        />
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="agregarPago"
                                    class="btn btn-primary w-full mt-6 py-3"
                                    style="font-weight: 600; font-size: 15px"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <line
                                            x1="12"
                                            y1="5"
                                            x2="12"
                                            y2="19"
                                        ></line>
                                        <line
                                            x1="5"
                                            y1="12"
                                            x2="19"
                                            y2="12"
                                        ></line>
                                    </svg>
                                    Agregar Pago
                                </button>
                            </div>

                            <!-- Tabla de Pagos -->
                            <div
                                class="overflow-x-auto rounded-lg"
                                style="border: 1px solid var(--theme-border)"
                            >
                                <table class="w-full text-sm">
                                    <thead
                                        style="
                                            background-color: var(
                                                --theme-bg-tertiary
                                            );
                                            border-bottom: 1px solid
                                                var(--theme-border);
                                        "
                                    >
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                #
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                Fecha
                                            </th>
                                            <th
                                                class="px-4 py-3 text-right font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                Monto
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                Método
                                            </th>
                                            <th
                                                class="px-4 py-3 text-center font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-if="form.pagos.length === 0"
                                            style="
                                                border-bottom: 1px solid
                                                    var(--theme-border);
                                            "
                                        >
                                            <td
                                                colspan="5"
                                                class="px-4 py-8 text-center"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="32"
                                                    height="32"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    class="mx-auto mb-2 opacity-50"
                                                >
                                                    <path
                                                        d="M9 12h6m-6 4h6M21 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2"
                                                    ></path>
                                                </svg>
                                                <p>
                                                    No hay pagos agregados aún
                                                </p>
                                            </td>
                                        </tr>
                                        <tr
                                            v-for="(pago, index) in form.pagos"
                                            :key="index"
                                            class="transition duration-200"
                                            :style="{
                                                borderBottom:
                                                    '1px solid var(--theme-border)',
                                                backgroundColor:
                                                    'var(--theme-bg-primary)',
                                                cursor: 'default',
                                            }"
                                            @mouseenter="
                                                $event.target.closest(
                                                    'tr'
                                                ).style.backgroundColor =
                                                    'var(--theme-bg-secondary)'
                                            "
                                            @mouseleave="
                                                $event.target.closest(
                                                    'tr'
                                                ).style.backgroundColor =
                                                    'var(--theme-bg-primary)'
                                            "
                                        >
                                            <td
                                                class="px-4 py-3 font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                                                    style="
                                                        background-color: var(
                                                            --theme-primary
                                                        );
                                                        color: white;
                                                    "
                                                >
                                                    {{ index + 1 }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-3"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    class="inline mr-1"
                                                    style="
                                                        color: var(
                                                            --theme-primary
                                                        );
                                                    "
                                                >
                                                    <rect
                                                        x="3"
                                                        y="4"
                                                        width="18"
                                                        height="18"
                                                        rx="2"
                                                        ry="2"
                                                    ></rect>
                                                    <line
                                                        x1="16"
                                                        y1="2"
                                                        x2="16"
                                                        y2="6"
                                                    ></line>
                                                    <line
                                                        x1="8"
                                                        y1="2"
                                                        x2="8"
                                                        y2="6"
                                                    ></line>
                                                    <line
                                                        x1="3"
                                                        y1="10"
                                                        x2="21"
                                                        y2="10"
                                                    ></line>
                                                </svg>
                                                {{ formatearFecha(pago.fecha) }}
                                            </td>
                                            <td
                                                class="px-4 py-3 text-right font-bold"
                                                style="
                                                    color: var(--theme-primary);
                                                "
                                            >
                                                Bs.
                                                {{
                                                    formatearMoneda(pago.total)
                                                }}
                                            </td>
                                            <td
                                                class="px-4 py-3"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                "
                                            >
                                                <span
                                                    class="inline-block px-2 py-1 rounded text-xs font-medium"
                                                    style="
                                                        background-color: var(
                                                            --theme-bg-secondary
                                                        );
                                                        color: var(
                                                            --theme-text-primary
                                                        );
                                                    "
                                                >
                                                    {{ pago.metodo_pago }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    type="button"
                                                    @click="eliminarPago(index)"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded transition duration-200"
                                                    style="color: var(--theme-error); background-color: rgba(255,0,0,0.05); hover: background-color: rgba(255,0,0,0.1)"
                                                    title="Eliminar pago"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                    >
                                                        <polyline
                                                            points="3 6 5 6 21 6"
                                                        ></polyline>
                                                        <path
                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                                        ></path>
                                                        <line
                                                            x1="10"
                                                            y1="11"
                                                            x2="10"
                                                            y2="17"
                                                        ></line>
                                                        <line
                                                            x1="14"
                                                            y1="11"
                                                            x2="14"
                                                            y2="17"
                                                        ></line>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Resumen de Pagos -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6"
                            >
                                <!-- Total a Cobrar -->
                                <div
                                    class="p-4 rounded-lg"
                                    style="
                                        background-color: var(
                                            --theme-bg-secondary
                                        );
                                        border: 2px solid var(--theme-primary);
                                        border-left: 4px solid
                                            var(--theme-primary);
                                    "
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <p
                                                class="text-xs font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                "
                                            >
                                                Total a Cobrar
                                            </p>
                                            <p
                                                class="text-2xl font-bold mt-2"
                                                style="
                                                    color: var(--theme-primary);
                                                "
                                            >
                                                Bs.
                                                {{
                                                    formatearMoneda(
                                                        form.deuda_total
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="40"
                                            height="40"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            style="
                                                color: var(--theme-primary);
                                                opacity: 0.2;
                                            "
                                        >
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <path d="M12 6v6l4 2"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Total de Pagos -->
                                <div
                                    class="p-4 rounded-lg"
                                    style="
                                        background-color: var(
                                            --theme-bg-secondary
                                        );
                                        border: 2px solid var(--theme-success);
                                        border-left: 4px solid
                                            var(--theme-success);
                                    "
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <p
                                                class="text-xs font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                "
                                            >
                                                Total de Pagos
                                            </p>
                                            <p
                                                class="text-2xl font-bold mt-2"
                                                style="
                                                    color: var(--theme-success);
                                                "
                                            >
                                                Bs.
                                                {{
                                                    formatearMoneda(totalPagos)
                                                }}
                                            </p>
                                        </div>
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="40"
                                            height="40"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            style="
                                                color: var(--theme-success);
                                                opacity: 0.2;
                                            "
                                        >
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Diferencia -->
                                <div
                                    class="p-4 rounded-lg"
                                    :style="{
                                        backgroundColor:
                                            'var(--theme-bg-secondary)',
                                        border: `2px solid ${
                                            Math.abs(diferenciaTotal) < 0.01
                                                ? 'var(--theme-success)'
                                                : 'var(--theme-error)'
                                        }`,
                                        borderLeft: `4px solid ${
                                            Math.abs(diferenciaTotal) < 0.01
                                                ? 'var(--theme-success)'
                                                : 'var(--theme-error)'
                                        }`,
                                    }"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <p
                                                class="text-xs font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                "
                                            >
                                                {{
                                                    Math.abs(diferenciaTotal) <
                                                    0.01
                                                        ? "✓ Balanceado"
                                                        : "Diferencia"
                                                }}
                                            </p>
                                            <p
                                                class="text-2xl font-bold mt-2"
                                                :style="{
                                                    color:
                                                        Math.abs(
                                                            diferenciaTotal
                                                        ) < 0.01
                                                            ? 'var(--theme-success)'
                                                            : 'var(--theme-error)',
                                                }"
                                            >
                                                Bs.
                                                {{
                                                    formatearMoneda(
                                                        Math.abs(
                                                            diferenciaTotal
                                                        )
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <svg
                                            v-if="
                                                Math.abs(diferenciaTotal) < 0.01
                                            "
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="40"
                                            height="40"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            style="
                                                color: var(--theme-success);
                                                opacity: 0.2;
                                            "
                                        >
                                            <path
                                                d="M9 16.17L4.83 12m0 0l-1.41 1.41L9 19 21 7m0 0l-1.41-1.41L9 16.17"
                                            ></path>
                                        </svg>
                                        <svg
                                            v-else
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="40"
                                            height="40"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            style="
                                                color: var(--theme-error);
                                                opacity: 0.2;
                                            "
                                        >
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="10"
                                            ></circle>
                                            <line
                                                x1="15"
                                                y1="9"
                                                x2="9"
                                                y2="15"
                                            ></line>
                                            <line
                                                x1="9"
                                                y1="9"
                                                x2="15"
                                                y2="15"
                                            ></line>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div
                            class="flex gap-3 justify-end pt-6 border-t"
                            style="border-color: var(--theme-border)"
                        >
                            <Link href="/planesPago" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="
                                    form.processing ||
                                    !esValido ||
                                    form.pagos.length === 0 ||
                                    Math.abs(diferenciaTotal) > 0.01
                                "
                                class="btn btn-primary"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"
                                    ></path>
                                    <polyline
                                        points="17 21 17 13 7 13 7 21"
                                    ></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                {{
                                    form.processing
                                        ? "Guardando..."
                                        : "Crear Plan de Pago"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
