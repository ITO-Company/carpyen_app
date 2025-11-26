<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";

const props = defineProps({
    planesPago: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
});

const mostrarModalPagos = ref(false); // Variable legada, no se usa más
const vistaActual = ref("planes"); // 'planes' o 'pagos'
const planSeleccionado = ref(null);
const pagosSeleccionados = ref([]);
const mostrarModalPago = ref(false);
const pagoSeleccionado = ref(null);
const qrImage = ref(null);
const transaccionId = ref(null);
const generandoQR = ref(false);
const errorQR = ref(null);
const pagoCompletado = ref(false);

const verPagos = (plan) => {
    planSeleccionado.value = plan;
    pagosSeleccionados.value = plan.pagos || [];
    vistaActual.value = "pagos";
};

const volverAPlanes = () => {
    vistaActual.value = "planes";
    planSeleccionado.value = null;
    pagosSeleccionados.value = [];
};

const abrirModalPago = (pago) => {
    pagoSeleccionado.value = pago;
    qrImage.value = null;
    transaccionId.value = null;
    errorQR.value = null;
    pagoCompletado.value = false;
    mostrarModalPago.value = true;
};

const cerrarModalPago = () => {
    mostrarModalPago.value = false;
    pagoSeleccionado.value = null;
    qrImage.value = null;
    transaccionId.value = null;
    errorQR.value = null;
    pagoCompletado.value = false;
};

const generarQRPago = async () => {
    if (!pagoSeleccionado.value) return;

    generandoQR.value = true;
    errorQR.value = null;

    try {
        const response = await window.axios.post("/pagos/generar-qr", {
            monto: parseFloat(pagoSeleccionado.value.total),
            glosa: `Pago del plan ${planSeleccionado.value?.proyecto?.nombre}`,
            email: "",
        });

        if (response.data.success) {
            qrImage.value = response.data.qr_image;
            transaccionId.value = response.data.transaction_id;
        } else {
            errorQR.value = response.data.message || "Error al generar código QR";
        }
    } catch (error) {
        console.error("Error:", error);
        const errorMsg = error.response?.data?.message || 
                         error.message || 
                         "Error de conexión al generar el QR";
        errorQR.value = errorMsg;
    } finally {
        generandoQR.value = false;
    }
};

const cerrarModalPagos = () => {
    mostrarModalPagos.value = false;
    planSeleccionado.value = null;
    pagosSeleccionados.value = [];
};

const formatearFecha = (fecha) => {
    if (!fecha) return "";
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const eliminar = (plan) => {
    if (plan && plan.proyecto && plan.proyecto.nombre) {
        if (
            confirm(
                `¿Está seguro de que desea eliminar el plan de pago para "${plan.proyecto.nombre}"?`
            )
        ) {
            useForm({}).delete(`/planesPago/${plan.id}`);
        }
    }
};
</script>

<template>
    <Head title="Pagos" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl leading-tight"
                style="color: var(--theme-text-primary)"
            >
                Gestión de Pagos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- VISTA DE PLANES DE PAGO -->
                <template v-if="vistaActual === 'planes'">
                    <div class="mb-6 flex justify-between items-center gap-4">
                        <div></div>
                        <Link
                            :href="route('planesPago.create')"
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
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Nuevo Plan de Pago
                        </Link>
                    </div>

                    <!-- LISTA DE PLANES DE PAGO -->
                    <template
                        v-if="
                            planesPago &&
                            planesPago.data &&
                            planesPago.data.length > 0
                        "
                    >
                        <div class="grid gap-6">
                            <div
                                v-for="plan in planesPago.data"
                                :key="plan.id"
                                class="card"
                            >
                                <div class="card-body p-6">
                                    <!-- Encabezado -->
                                    <div
                                        class="flex justify-between items-start mb-4"
                                    >
                                        <div>
                                            <h3 class="text-xl font-bold">
                                                {{ plan.proyecto.nombre }}
                                            </h3>
                                            <p
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                    font-size: 0.875rem;
                                                    margin-top: 0.25rem;
                                                "
                                            >
                                                Cliente:
                                                <span class="font-semibold">{{
                                                    plan.proyecto.cliente.nombre
                                                }}</span>
                                            </p>
                                        </div>
                                        <span
                                            class="badge"
                                            :class="{
                                                'badge-info':
                                                    plan.estado === 'activo',
                                                'badge-success':
                                                    plan.estado ===
                                                    'completado',
                                                'badge-warning':
                                                    plan.estado === 'mora',
                                            }"
                                        >
                                            {{
                                                plan.estado
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                plan.estado.slice(1)
                                            }}
                                        </span>
                                    </div>

                                    <!-- Información Financiera -->
                                    <div
                                        class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6"
                                    >
                                        <div class="stat-box">
                                            <p class="stat-label">
                                                Deuda Total
                                            </p>
                                            <p class="stat-value">
                                                Bs
                                                {{
                                                    parseFloat(
                                                        plan.deuda_total
                                                    ).toFixed(2)
                                                }}
                                            </p>
                                        </div>
                                        <div class="stat-box">
                                            <p class="stat-label">Pagado</p>
                                            <p
                                                class="stat-value"
                                                style="
                                                    color: var(--theme-success);
                                                "
                                            >
                                                Bs
                                                {{
                                                    parseFloat(
                                                        plan.pagado_total
                                                    ).toFixed(2)
                                                }}
                                            </p>
                                        </div>
                                        <div class="stat-box">
                                            <p class="stat-label">Saldo</p>
                                            <p
                                                class="stat-value"
                                                style="
                                                    color: var(--theme-warning);
                                                "
                                            >
                                                Bs
                                                {{
                                                    (
                                                        parseFloat(
                                                            plan.deuda_total
                                                        ) -
                                                        parseFloat(
                                                            plan.pagado_total
                                                        )
                                                    ).toFixed(2)
                                                }}
                                            </p>
                                        </div>
                                        <div class="stat-box">
                                            <p class="stat-label">Progreso</p>
                                            <p
                                                class="stat-value"
                                                style="
                                                    color: var(--theme-primary);
                                                "
                                            >
                                                {{
                                                    (
                                                        (parseFloat(
                                                            plan.pagado_total
                                                        ) /
                                                            parseFloat(
                                                                plan.deuda_total
                                                            )) *
                                                        100
                                                    ).toFixed(0)
                                                }}%
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Barra de Progreso -->
                                    <div class="mb-6">
                                        <div
                                            class="flex justify-between items-center mb-2"
                                        >
                                            <p
                                                class="text-sm font-semibold"
                                                style="
                                                    color: var(
                                                        --theme-text-primary
                                                    );
                                                "
                                            >
                                                Progreso de Pago
                                            </p>
                                            <p
                                                class="text-sm"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                "
                                            >
                                                {{
                                                    (
                                                        (parseFloat(
                                                            plan.pagado_total
                                                        ) /
                                                            parseFloat(
                                                                plan.deuda_total
                                                            )) *
                                                        100
                                                    ).toFixed(0)
                                                }}%
                                            </p>
                                        </div>
                                        <div
                                            class="w-full bg-gray-300 rounded-full h-2 overflow-hidden"
                                        >
                                            <div
                                                :style="{
                                                    width:
                                                        (parseFloat(
                                                            plan.pagado_total
                                                        ) /
                                                            parseFloat(
                                                                plan.deuda_total
                                                            )) *
                                                            100 +
                                                        '%',
                                                    backgroundColor:
                                                        parseFloat(
                                                            plan.pagado_total
                                                        ) /
                                                            parseFloat(
                                                                plan.deuda_total
                                                            ) >=
                                                        1
                                                            ? 'var(--theme-success)'
                                                            : parseFloat(
                                                                  plan.pagado_total
                                                              ) /
                                                                  parseFloat(
                                                                      plan.deuda_total
                                                                  ) >=
                                                              0.5
                                                            ? 'var(--theme-primary)'
                                                            : 'var(--theme-warning)',
                                                }"
                                                class="h-full transition-all duration-300"
                                            />
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="flex gap-2 flex-wrap">
                                        <button
                                            @click="verPagos(plan)"
                                            class="btn btn-primary btn-sm"
                                        >
                                            Ver Pagos
                                        </button>
                                        <Link
                                            :href="
                                                route(
                                                    'planesPago.edit',
                                                    plan.id
                                                )
                                            "
                                            class="btn btn-secondary btn-sm"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="eliminar(plan)"
                                            class="btn btn-error btn-sm"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div
                            v-if="
                                planesPago &&
                                planesPago.links &&
                                planesPago.links.length > 0
                            "
                            class="mt-8 flex justify-center gap-2"
                        >
                            <template v-for="link in planesPago.links">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'btn',
                                        link.active
                                            ? 'btn-primary'
                                            : 'btn-ghost',
                                    ]"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </template>

                    <!-- Sin planes de pago -->
                    <div v-else class="card text-center py-12">
                        <div class="card-body">
                            <svg
                                class="w-16 h-16 mx-auto mb-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                style="color: var(--theme-text-secondary)"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <h3 class="text-xl font-semibold">
                                No hay planes de pago
                            </h3>
                            <p
                                style="
                                    color: var(--theme-text-secondary);
                                    margin-bottom: 1.5rem;
                                "
                            >
                                Comienza creando un nuevo plan de pago para tus
                                proyectos
                            </p>
                            <Link
                                :href="route('planesPago.create')"
                                class="btn btn-primary"
                            >
                                Crear Plan de Pago
                            </Link>
                        </div>
                    </div>
                </template>
                <template v-if="vistaActual === 'pagos' && planSeleccionado">
                    <!-- Header con botón de volver atrás -->
                    <div class="mb-8 flex items-center gap-4">
                        <button
                            @click="volverAPlanes"
                            class="btn btn-ghost btn-circle"
                            title="Volver atrás"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-3xl font-bold">Pagos del Plan</h2>
                            <p
                                style="
                                    color: var(--theme-text-secondary);
                                    margin-top: 0.5rem;
                                "
                            >
                                {{ planSeleccionado?.proyecto?.nombre }}
                            </p>
                        </div>
                    </div>

                    <!-- Resumen Financiero -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="card">
                            <div class="card-body p-6 text-center">
                                <p
                                    class="text-sm font-semibold uppercase"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Deuda Total
                                </p>
                                <p
                                    class="text-3xl font-bold"
                                    style="
                                        color: var(--theme-primary);
                                        margin-top: 0.5rem;
                                    "
                                >
                                    Bs
                                    {{
                                        parseFloat(
                                            planSeleccionado?.deuda_total || 0
                                        ).toFixed(2)
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body p-6 text-center">
                                <p
                                    class="text-sm font-semibold uppercase"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Pagado
                                </p>
                                <p
                                    class="text-3xl font-bold"
                                    style="
                                        color: var(--theme-success);
                                        margin-top: 0.5rem;
                                    "
                                >
                                    Bs
                                    {{
                                        parseFloat(
                                            planSeleccionado?.pagado_total || 0
                                        ).toFixed(2)
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body p-6 text-center">
                                <p
                                    class="text-sm font-semibold uppercase"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Saldo Pendiente
                                </p>
                                <p
                                    class="text-3xl font-bold"
                                    style="
                                        color: var(--theme-warning);
                                        margin-top: 0.5rem;
                                    "
                                >
                                    Bs
                                    {{
                                        (
                                            parseFloat(
                                                planSeleccionado?.deuda_total ||
                                                    0
                                            ) -
                                            parseFloat(
                                                planSeleccionado?.pagado_total ||
                                                    0
                                            )
                                        ).toFixed(2)
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body p-6 text-center">
                                <p
                                    class="text-sm font-semibold uppercase"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Progreso
                                </p>
                                <p
                                    class="text-3xl font-bold"
                                    style="
                                        color: var(--theme-primary);
                                        margin-top: 0.5rem;
                                    "
                                >
                                    {{
                                        (
                                            (parseFloat(
                                                planSeleccionado?.pagado_total ||
                                                    0
                                            ) /
                                                parseFloat(
                                                    planSeleccionado?.deuda_total ||
                                                        1
                                                )) *
                                            100
                                        ).toFixed(0)
                                    }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Pagos -->
                    <div class="card">
                        <div class="card-body p-6">
                            <h3 class="text-2xl font-bold mb-6">
                                Detalle de Pagos
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="table table-compact w-full">
                                    <thead>
                                        <tr
                                            style="
                                                background-color: var(
                                                    --theme-bg-secondary
                                                );
                                            "
                                        >
                                            <th class="text-sm">#</th>
                                            <th class="text-sm">
                                                Fecha de Pago
                                            </th>
                                            <th class="text-sm">Monto</th>
                                            <th class="text-sm">
                                                Método de Pago
                                            </th>
                                            <th class="text-sm">Estado</th>
                                            <th class="text-sm">
                                                Fecha de Registro
                                            </th>
                                            <th class="text-sm">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                pago, index
                                            ) in pagosSeleccionados"
                                            :key="pago.id"
                                        >
                                            <td class="text-sm font-semibold">
                                                {{ index + 1 }}
                                            </td>
                                            <td class="text-sm">
                                                {{ formatearFecha(pago.fecha) }}
                                            </td>
                                            <td class="text-sm font-semibold">
                                                Bs
                                                {{
                                                    parseFloat(
                                                        pago.total || 0
                                                    ).toFixed(2)
                                                }}
                                            </td>
                                            <td class="text-sm">
                                                <span
                                                    class="badge badge-sm"
                                                    style="
                                                        background-color: var(
                                                            --theme-bg-secondary
                                                        );
                                                    "
                                                >
                                                    {{
                                                        pago.metodo_pago ||
                                                        "No especificado"
                                                    }}
                                                </span>
                                            </td>
                                            <td class="text-sm">
                                                <span
                                                    class="badge badge-sm"
                                                    :class="{
                                                        'badge-warning':
                                                            pago.estado ===
                                                            'pendiente',
                                                        'badge-success':
                                                            pago.estado ===
                                                            'completado',
                                                        'badge-error':
                                                            pago.estado ===
                                                            'fallido',
                                                    }"
                                                >
                                                    {{
                                                        pago.estado
                                                            .charAt(0)
                                                            .toUpperCase() +
                                                        pago.estado.slice(1)
                                                    }}
                                                </span>
                                            </td>
                                            <td class="text-sm">
                                                {{
                                                    formatearFecha(
                                                        pago.created_at
                                                    )
                                                }}
                                            </td>
                                            <td class="text-sm">
                                                <button
                                                    v-if="
                                                        pago.estado ===
                                                        'pendiente'
                                                    "
                                                    @click="
                                                        abrirModalPago(pago)
                                                    "
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Pagar
                                                </button>
                                                <span
                                                    v-else
                                                    class="text-xs"
                                                    style="
                                                        color: var(
                                                            --theme-text-secondary
                                                        );
                                                    "
                                                >
                                                    —
                                                </span>
                                            </td>
                                        </tr>
                                        <tr
                                            v-if="
                                                !pagosSeleccionados ||
                                                pagosSeleccionados.length === 0
                                            "
                                        >
                                            <td
                                                colspan="6"
                                                class="text-center py-8"
                                                style="
                                                    color: var(
                                                        --theme-text-secondary
                                                    );
                                                "
                                            >
                                                No hay pagos registrados
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Resumen de Estados -->
                            <div
                                class="grid grid-cols-3 gap-6 mt-8 pt-6 border-t"
                                style="border-color: var(--theme-border)"
                            >
                                <div class="text-center">
                                    <p
                                        class="text-sm font-semibold"
                                        style="
                                            color: var(--theme-text-secondary);
                                            margin-bottom: 0.5rem;
                                        "
                                    >
                                        Pendientes
                                    </p>
                                    <p
                                        class="text-3xl font-bold"
                                        style="color: var(--theme-warning)"
                                    >
                                        {{
                                            pagosSeleccionados.filter(
                                                (p) => p.estado === "pendiente"
                                            ).length
                                        }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p
                                        class="text-sm font-semibold"
                                        style="
                                            color: var(--theme-text-secondary);
                                            margin-bottom: 0.5rem;
                                        "
                                    >
                                        Completados
                                    </p>
                                    <p
                                        class="text-3xl font-bold"
                                        style="color: var(--theme-success)"
                                    >
                                        {{
                                            pagosSeleccionados.filter(
                                                (p) => p.estado === "completado"
                                            ).length
                                        }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p
                                        class="text-sm font-semibold"
                                        style="
                                            color: var(--theme-text-secondary);
                                            margin-bottom: 0.5rem;
                                        "
                                    >
                                        Fallidos
                                    </p>
                                    <p
                                        class="text-3xl font-bold"
                                        style="color: var(--theme-error)"
                                    >
                                        {{
                                            pagosSeleccionados.filter(
                                                (p) => p.estado === "fallido"
                                            ).length
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- MODAL DE PAGO -->
                <div
                    v-if="mostrarModalPago"
                    class="fixed inset-0 z-50 overflow-y-auto"
                >
                    <div
                        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                        @click="cerrarModalPago"
                    ></div>
                    <div
                        class="flex min-h-screen items-center justify-center p-4"
                    >
                        <div
                            class="relative w-full max-w-2xl rounded-lg bg-white p-8 shadow-xl"
                            style="background-color: var(--theme-bg-primary)"
                            @click.stop
                        >
                            <!-- Header del Modal -->
                            <div
                                class="mb-6 flex items-center justify-between border-b"
                                style="border-color: var(--theme-border)"
                            >
                                <h3 class="text-2xl font-bold">
                                    {{
                                        qrImage
                                            ? "Código de Pago QR"
                                            : "Detalles del Pago"
                                    }}
                                </h3>
                                <button
                                    @click="cerrarModalPago"
                                    class="btn btn-ghost btn-circle btn-sm"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <!-- Contenido del Modal - Vista de Detalles -->
                            <div
                                v-if="!qrImage && pagoSeleccionado"
                                class="space-y-6"
                            >
                                <!-- Información del Plan -->
                                <div
                                    class="rounded-lg p-4"
                                    style="
                                        background-color: var(
                                            --theme-bg-secondary
                                        );
                                    "
                                >
                                    <p
                                        class="text-sm font-semibold uppercase"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        Plan de Pago
                                    </p>
                                    <p class="text-lg font-bold mt-1">
                                        {{ planSeleccionado?.proyecto?.nombre }}
                                    </p>
                                </div>

                                <!-- Detalle del Pago -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div
                                        class="rounded-lg p-4"
                                        style="
                                            background-color: var(
                                                --theme-bg-secondary
                                            );
                                        "
                                    >
                                        <p
                                            class="text-sm font-semibold uppercase"
                                            style="
                                                color: var(
                                                    --theme-text-secondary
                                                );
                                            "
                                        >
                                            Monto a Pagar
                                        </p>
                                        <p
                                            class="text-3xl font-bold mt-2"
                                            style="color: var(--theme-primary)"
                                        >
                                            Bs
                                            {{
                                                parseFloat(
                                                    pagoSeleccionado.total || 0
                                                ).toFixed(2)
                                            }}
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg p-4"
                                        style="
                                            background-color: var(
                                                --theme-bg-secondary
                                            );
                                        "
                                    >
                                        <p
                                            class="text-sm font-semibold uppercase"
                                            style="
                                                color: var(
                                                    --theme-text-secondary
                                                );
                                            "
                                        >
                                            Fecha de Vencimiento
                                        </p>
                                        <p class="text-xl font-bold mt-2">
                                            {{
                                                formatearFecha(
                                                    pagoSeleccionado.fecha
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Información Adicional -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p
                                            class="text-sm font-semibold mb-2"
                                            style="
                                                color: var(
                                                    --theme-text-secondary
                                                );
                                            "
                                        >
                                            Método de Pago Registrado
                                        </p>
                                        <p class="font-semibold">
                                            {{
                                                pagoSeleccionado.metodo_pago ||
                                                "No especificado"
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-semibold mb-2"
                                            style="
                                                color: var(
                                                    --theme-text-secondary
                                                );
                                            "
                                        >
                                            Estado Actual
                                        </p>
                                        <span
                                            class="badge badge-lg"
                                            :class="{
                                                'badge-warning':
                                                    pagoSeleccionado.estado ===
                                                    'pendiente',
                                                'badge-success':
                                                    pagoSeleccionado.estado ===
                                                    'completado',
                                                'badge-error':
                                                    pagoSeleccionado.estado ===
                                                    'fallido',
                                            }"
                                        >
                                            {{
                                                pagoSeleccionado.estado
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                pagoSeleccionado.estado.slice(1)
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Descripción o Concepto -->
                                <div
                                    class="rounded-lg p-4"
                                    style="
                                        background-color: var(
                                            --theme-bg-secondary
                                        );
                                    "
                                >
                                    <p
                                        class="text-sm font-semibold mb-2"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        Concepto
                                    </p>
                                    <p class="text-sm">
                                        Pago por cuota del plan de pago para el
                                        proyecto
                                        <strong>{{
                                            planSeleccionado?.proyecto?.nombre
                                        }}</strong>
                                    </p>
                                </div>

                                <!-- Mensaje de Error -->
                                <div
                                    v-if="errorQR"
                                    class="rounded-lg p-4"
                                    style="background-color: var(--theme-error)"
                                >
                                    <p class="text-sm text-white">
                                        {{ errorQR }}
                                    </p>
                                </div>

                                <!-- Botones de Acción -->
                                <div
                                    class="flex gap-3 border-t pt-6"
                                    style="border-color: var(--theme-border)"
                                >
                                    <button
                                        @click="cerrarModalPago"
                                        class="btn btn-ghost flex-1"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        @click="generarQRPago"
                                        :disabled="generandoQR"
                                        class="btn btn-primary flex-1"
                                    >
                                        <svg
                                            v-if="!generandoQR"
                                            class="w-5 h-5 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <span
                                            v-if="generandoQR"
                                            class="loading loading-spinner loading-sm mr-2"
                                        ></span>
                                        {{
                                            generandoQR
                                                ? "Generando..."
                                                : "Proceder al Pago"
                                        }}
                                    </button>
                                </div>
                            </div>

                            <!-- Contenido del Modal - Vista de QR -->
                            <div
                                v-else-if="qrImage"
                                class="space-y-6 text-center"
                            >
                                <!-- Información del Pago en QR -->
                                <div class="space-y-2">
                                    <p
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        Escanea el código QR con tu teléfono
                                    </p>
                                    <p class="text-2xl font-bold">
                                        Bs
                                        {{
                                            parseFloat(
                                                pagoSeleccionado.total || 0
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>

                                <!-- QR Image -->
                                <div class="flex justify-center py-6">
                                    <img
                                        :src="qrImage"
                                        alt="Código QR de pago"
                                        class="w-64 h-64 object-contain"
                                    />
                                </div>

                                <!-- ID de Transacción -->
                                <div
                                    class="rounded-lg p-3"
                                    style="
                                        background-color: var(
                                            --theme-bg-secondary
                                        );
                                    "
                                >
                                    <p
                                        class="text-xs"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        ID de Transacción
                                    </p>
                                    <p
                                        class="text-sm font-mono break-all"
                                        style="color: var(--theme-primary)"
                                    >
                                        {{ transaccionId }}
                                    </p>
                                </div>

                                <!-- Instrucciones -->
                                <div
                                    class="rounded-lg p-4"
                                    style="
                                        background-color: var(
                                            --theme-bg-secondary
                                        );
                                    "
                                >
                                    <p
                                        class="text-sm font-semibold mb-2"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        Instrucciones de Pago:
                                    </p>
                                    <ol class="text-sm text-left space-y-1">
                                        <li>
                                            1. Abre tu aplicación de billetera
                                            digital o banco
                                        </li>
                                        <li>
                                            2. Selecciona escanear código QR
                                        </li>
                                        <li>3. Escanea el código de arriba</li>
                                        <li>4. Confirma el pago</li>
                                        <li>
                                            5. Recibirás confirmación
                                            automáticamente
                                        </li>
                                    </ol>
                                </div>

                                <!-- Botones de Acción QR -->
                                <div
                                    class="flex gap-3 border-t pt-6"
                                    style="border-color: var(--theme-border)"
                                >
                                    <button
                                        @click="
                                            qrImage = null;
                                            errorQR = null;
                                        "
                                        class="btn btn-ghost flex-1"
                                    >
                                        Atrás
                                    </button>
                                    <button
                                        @click="cerrarModalPago"
                                        class="btn btn-primary flex-1"
                                    >
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.grid {
    display: grid;
}

.gap-6 {
    gap: var(--spacing-6);
}

.gap-4 {
    gap: var(--spacing-4);
}

.gap-2 {
    gap: var(--spacing-2);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.mb-4 {
    margin-bottom: var(--spacing-4);
}

.mb-2 {
    margin-bottom: var(--spacing-2);
}

.mt-8 {
    margin-top: var(--spacing-8);
}

.mt-4 {
    margin-top: var(--spacing-4);
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.px-6 {
    padding-left: var(--spacing-6);
    padding-right: var(--spacing-6);
}

.p-4 {
    padding: var(--spacing-4);
}

.p-6 {
    padding: var(--spacing-6);
}

.max-w-7xl {
    max-width: 80rem;
}

.max-w-4xl {
    max-width: 56rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.flex {
    display: flex;
}

.justify-between {
    justify-content: space-between;
}

.justify-center {
    justify-content: center;
}

.items-start {
    align-items: flex-start;
}

.items-center {
    align-items: center;
}

.flex-wrap {
    flex-wrap: wrap;
}

.grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.rounded-full {
    border-radius: 9999px;
}

.rounded-lg {
    border-radius: var(--radius-lg);
}

.overflow-x-auto {
    overflow-x: auto;
}

.w-full {
    width: 100%;
}

.w-16 {
    width: 4rem;
}

.h-2 {
    height: 0.5rem;
}

.h-6 {
    height: 1.5rem;
}

.h-16 {
    height: 4rem;
}

.text-center {
    text-align: center;
}

.text-sm {
    font-size: var(--font-size-sm);
}

.text-lg {
    font-size: var(--font-size-lg);
}

.text-xl {
    font-size: var(--font-size-xl);
}

.text-2xl {
    font-size: var(--font-size-2xl);
}

.font-bold {
    font-weight: 700;
}

.font-semibold {
    font-weight: 600;
}

.leading-tight {
    line-height: 1.25;
}

/* Stat Box */
.stat-box {
    padding: var(--spacing-4);
    border-radius: var(--radius-md);
    background-color: var(--theme-bg-secondary);
}

.stat-label {
    font-size: var(--font-size-xs);
    font-weight: 600;
    text-transform: uppercase;
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-1);
}

.stat-value {
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--theme-primary);
}

/* Transiciones suaves */
.transition-all {
    transition: all 0.3s ease;
}

/* Modal personalizado */
.modal-open {
    display: flex;
}

.modal-box {
    border-radius: var(--radius-lg);
    background-color: var(--theme-bg-primary);
    color: var(--theme-text-primary);
}

.pb-4 {
    padding-bottom: var(--spacing-4);
}

.pt-4 {
    padding-top: var(--spacing-4);
}

/* Mejoras visuales */
.btn-sm {
    height: 2rem;
    padding: 0 0.75rem;
    font-size: var(--font-size-sm);
}

.btn-circle {
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
}

.btn svg {
    margin-right: var(--spacing-2);
}
</style>
