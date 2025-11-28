<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    planPago: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    proyecto_id: props.planPago.proyecto_id,
    deuda_total: props.planPago.deuda_total,
    numero_deudas: props.planPago.numero_deudas,
    estado: props.planPago.estado,
    pagos: props.planPago.pagos || [],
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
    form.put(`/planesPago/${props.planPago.id}`, {
        onSuccess: () => {
        },
    });
};
</script>

<template>
    <Head :title="`Editar Plan de Pago - ${planPago.proyecto?.nombre}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl"
                style="color: var(--theme-text-primary)"
            >
                Editar Plan de Pago
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="rounded-lg shadow-lg p-8" style="background-color: var(--theme-bg-secondary)">
                    <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                style="background-color: var(--theme-primary)"
                            >
                                1
                            </div>
                            <h3
                                class="text-xl font-bold"
                                style="color: var(--theme-text-primary)"
                            >
                                Información Básica
                            </h3>
                        </div>

                        <!-- Proyecto y Cliente (Solo lectura) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label
                                    class="form-label"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Proyecto
                                </label>
                                <div
                                    class="px-4 py-2 rounded-lg"
                                    style="background-color: var(--theme-bg-tertiary); color: var(--theme-text-primary)"
                                >
                                    {{ planPago.proyecto?.nombre }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="form-label"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Cliente
                                </label>
                                <div
                                    class="px-4 py-2 rounded-lg"
                                    style="background-color: var(--theme-bg-tertiary); color: var(--theme-text-primary)"
                                >
                                    {{ planPago.proyecto?.cliente?.nombre }}
                                </div>
                            </div>
                        </div>

                        <!-- Deuda Total, Número de Deudas, Estado -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label
                                    class="form-label"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Deuda Total (Bs)
                                </label>
                                <input
                                    v-model="form.deuda_total"
                                    type="number"
                                    step="0.01"
                                    class="input theme-input w-full"
                                    style="background-color: var(--theme-bg-primary); color: var(--theme-text-primary); border: 1px solid var(--theme-border)"
                                />
                            </div>

                            <div>
                                <label
                                    class="form-label"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Número de Pagos
                                </label>
                                <input
                                    v-model.number="form.numero_deudas"
                                    type="number"
                                    min="1"
                                    class="input theme-input w-full"
                                    style="background-color: var(--theme-bg-primary); color: var(--theme-text-primary); border: 1px solid var(--theme-border)"
                                />
                            </div>

                            <div>
                                <label
                                    class="form-label"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Estado del Plan
                                </label>
                                <select
                                    v-model="form.estado"
                                    class="input theme-input w-full"
                                    style="background-color: var(--theme-bg-primary); color: var(--theme-text-primary); border: 1px solid var(--theme-border)"
                                >
                                    <option value="activo">Activo</option>
                                    <option value="completado">Completado</option>
                                    <option value="mora">Mora</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: DEFINIR PAGOS -->
                    <div v-if="esValido">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                style="background-color: var(--theme-primary)"
                            >
                                2
                            </div>
                            <h3
                                class="text-xl font-bold"
                                style="color: var(--theme-text-primary)"
                            >
                                Definir Pagos
                            </h3>
                        </div>

                        <!-- Botón Auto-completar -->
                        <div class="mb-6">
                            <button
                                type="button"
                                @click="autoCompletarPagos"
                                class="btn btn-primary px-4 py-2 rounded-lg font-semibold text-white"
                                style="background-color: var(--theme-success)"
                            >
                                Auto-completar Cronograma
                            </button>
                        </div>

                        <!-- Formulario para agregar pago -->
                        <div
                            class="p-6 rounded-lg mb-6"
                            style="background: linear-gradient(135deg, var(--theme-bg-secondary) 0%, rgba(0,0,0,0.02) 100%); border: 1px solid var(--theme-border)"
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
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <h4
                                    class="font-semibold text-base"
                                    style="color: var(--theme-text-primary)"
                                >
                                    Agregar Nuevo Pago
                                </h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="form-group">
                                    <label
                                        for="fecha_actual"
                                        class="form-label"
                                        style="color: var(--theme-text-secondary)"
                                    >
                                        Fecha de Pago
                                    </label>
                                    <input
                                        id="fecha_actual"
                                        v-model="fechaActual"
                                        type="date"
                                        class="input theme-input w-full"
                                        style="background-color: var(--theme-bg-primary); color: var(--theme-text-primary); border: 1px solid var(--theme-border)"
                                    />
                                </div>

                                <div class="form-group">
                                    <label
                                        for="monto_actual"
                                        class="form-label"
                                        style="color: var(--theme-text-secondary)"
                                    >
                                        Monto (Bs)
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-0 top-0 bottom-0 flex items-center px-3"
                                            style="color: var(--theme-text-secondary)"
                                        >
                                            Bs.
                                        </span>
                                        <input
                                            id="monto_actual"
                                            v-model="montoActual"
                                            type="number"
                                            step="0.01"
                                            placeholder="0.00"
                                            class="input theme-input w-full pl-10"
                                            style="background-color: var(--theme-bg-primary); color: var(--theme-text-primary); border: 1px solid var(--theme-border)"
                                        />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label
                                        for="metodo_actual"
                                        class="form-label"
                                        style="color: var(--theme-text-secondary)"
                                    >
                                        Método de Pago
                                    </label>
                                    <input
                                        id="metodo_actual"
                                        v-model="metodoActual"
                                        type="text"
                                        class="input theme-input w-full"
                                        placeholder="Ej: transferencia, QR, efectivo"
                                        style="background-color: var(--theme-bg-primary); color: var(--theme-text-primary); border: 1px solid var(--theme-border)"
                                    />
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="agregarPago"
                                class="btn btn-primary w-full mt-6 py-3"
                                style="font-weight: 600; font-size: 15px; background-color: var(--theme-primary); color: white"
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
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Agregar Pago
                            </button>
                        </div>

                        <!-- Tabla de Pagos -->
                        <div
                            class="overflow-x-auto rounded-lg mb-6"
                            style="border: 1px solid var(--theme-border)"
                        >
                            <table class="w-full text-sm">
                                <thead
                                    style="background-color: var(--theme-bg-tertiary); border-bottom: 1px solid var(--theme-border)"
                                >
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left font-semibold"
                                            style="color: var(--theme-text-primary)"
                                        >
                                            #
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left font-semibold"
                                            style="color: var(--theme-text-primary)"
                                        >
                                            Fecha
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right font-semibold"
                                            style="color: var(--theme-text-primary)"
                                        >
                                            Monto
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left font-semibold"
                                            style="color: var(--theme-text-primary)"
                                        >
                                            Método
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center font-semibold"
                                            style="color: var(--theme-text-primary)"
                                        >
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-if="form.pagos.length === 0"
                                        style="border-bottom: 1px solid var(--theme-border)"
                                    >
                                        <td
                                            colspan="5"
                                            class="px-4 py-8 text-center"
                                            style="color: var(--theme-text-secondary)"
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
                                            <p>No hay pagos agregados aún</p>
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="(pago, index) in form.pagos"
                                        :key="index"
                                        class="transition duration-200"
                                        :style="{
                                            borderBottom: '1px solid var(--theme-border)',
                                            backgroundColor: 'var(--theme-bg-primary)',
                                            cursor: 'default',
                                        }"
                                        @mouseenter="$event.target.closest('tr').style.backgroundColor = 'var(--theme-bg-secondary)'"
                                        @mouseleave="$event.target.closest('tr').style.backgroundColor = 'var(--theme-bg-primary)'"
                                    >
                                        <td
                                            class="px-4 py-3 font-semibold"
                                            style="color: var(--theme-text-primary)"
                                        >
                                            <span
                                                class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                                                style="background-color: var(--theme-primary); color: white"
                                            >
                                                {{ index + 1 }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-4 py-3"
                                            style="color: var(--theme-text-primary)"
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
                                                style="color: var(--theme-primary)"
                                            >
                                                <rect
                                                    x="3"
                                                    y="4"
                                                    width="18"
                                                    height="18"
                                                    rx="2"
                                                    ry="2"
                                                ></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            {{ formatearFecha(pago.fecha) }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-right font-bold"
                                            style="color: var(--theme-primary)"
                                        >
                                            Bs. {{ formatearMoneda(pago.total) }}
                                        </td>
                                        <td
                                            class="px-4 py-3"
                                            style="color: var(--theme-text-secondary)"
                                        >
                                            <span
                                                class="inline-block px-2 py-1 rounded text-xs font-medium"
                                                style="background-color: var(--theme-bg-secondary); color: var(--theme-text-primary)"
                                            >
                                                {{ pago.metodo_pago }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button
                                                type="button"
                                                @click="eliminarPago(index)"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded transition duration-200"
                                                style="color: var(--theme-error); background-color: rgba(255,0,0,0.05)"
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
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                                    ></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen de Pagos -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <!-- Total a Cobrar -->
                            <div
                                class="p-4 rounded-lg"
                                style="background-color: var(--theme-bg-primary); border-left: 4px solid var(--theme-primary); border: 1px solid var(--theme-border)"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-xs font-semibold"
                                            style="color: var(--theme-text-secondary); text-transform: uppercase; letter-spacing: 0.5px"
                                        >
                                            Total a Cobrar
                                        </p>
                                        <p
                                            class="text-2xl font-bold mt-2"
                                            style="color: var(--theme-primary)"
                                        >
                                            Bs. {{ formatearMoneda(form.deuda_total) }}
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
                                        style="color: var(--theme-primary); opacity: 0.2"
                                    >
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M12 6v6l4 2"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Total de Pagos -->
                            <div
                                class="p-4 rounded-lg"
                                style="background-color: var(--theme-bg-primary); border-left: 4px solid var(--theme-success); border: 1px solid var(--theme-border)"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-xs font-semibold"
                                            style="color: var(--theme-text-secondary); text-transform: uppercase; letter-spacing: 0.5px"
                                        >
                                            Total de Pagos
                                        </p>
                                        <p
                                            class="text-2xl font-bold mt-2"
                                            style="color: var(--theme-success)"
                                        >
                                            Bs. {{ formatearMoneda(totalPagos) }}
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
                                        style="color: var(--theme-success); opacity: 0.2"
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
                                    backgroundColor: 'var(--theme-bg-primary)',
                                    border: `2px solid ${Math.abs(diferenciaTotal) < 0.01 ? 'var(--theme-success)' : 'var(--theme-error)'}`,
                                    borderLeft: `4px solid ${Math.abs(diferenciaTotal) < 0.01 ? 'var(--theme-success)' : 'var(--theme-error)'}`,
                                }"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-xs font-semibold"
                                            style="color: var(--theme-text-secondary); text-transform: uppercase; letter-spacing: 0.5px"
                                        >
                                            {{ Math.abs(diferenciaTotal) < 0.01 ? '✓ Balanceado' : 'Diferencia' }}
                                        </p>
                                        <p
                                            class="text-2xl font-bold mt-2"
                                            :style="{ color: Math.abs(diferenciaTotal) < 0.01 ? 'var(--theme-success)' : 'var(--theme-error)' }"
                                        >
                                            Bs. {{ formatearMoneda(Math.abs(diferenciaTotal)) }}
                                        </p>
                                    </div>
                                    <svg
                                        v-if="Math.abs(diferenciaTotal) < 0.01"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="40"
                                        height="40"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        style="color: var(--theme-success); opacity: 0.2"
                                    >
                                        <path d="M9 16.17L4.83 12m0 0l-1.41 1.41L9 19 21 7m0 0l-1.41-1.41L9 16.17"></path>
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
                                        style="color: var(--theme-error); opacity: 0.2"
                                    >
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div
                        class="flex gap-4 justify-end mt-8"
                        style="border-top: 1px solid var(--theme-border); padding-top: 24px"
                    >
                        <Link
                            :href="`/planesPago/${planPago.id}`"
                            class="px-6 py-3 rounded-lg transition font-semibold"
                            style="background-color: var(--theme-bg-tertiary); color: var(--theme-text-primary)"
                        >
                            Cancelar
                        </Link>
                        <button
                            @click="enviar"
                            :disabled="form.processing || !esValido"
                            class="px-6 py-3 text-white rounded-lg transition font-semibold disabled:opacity-50"
                            style="background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-primary-dark) 100%)"
                        >
                            {{
                                form.processing
                                    ? "Guardando..."
                                    : "Actualizar Plan"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
