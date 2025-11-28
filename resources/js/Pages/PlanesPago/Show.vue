<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";

const props = defineProps({
    planPago: {
        type: Object,
        required: true,
    },
});

const expandirPagos = ref(true);

const formatearFecha = (fecha) => {
    if (!fecha) return "";
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatearMoneda = (valor) => {
    return new Intl.NumberFormat("es-BO", {
        style: "currency",
        currency: "BOB",
    }).format(valor);
};

const porcentajePago = () => {
    if (!props.planPago.deuda_total) return 0;
    return Math.round(
        (props.planPago.pagado_total / props.planPago.deuda_total) * 100
    );
};

const obtenerColorEstado = (estado) => {
    const colores = {
        activo: "bg-green-100 text-green-800",
        completado: "bg-blue-100 text-blue-800",
        mora: "bg-red-100 text-red-800",
    };
    return colores[estado] || "bg-gray-100 text-gray-800";
};

const estadoPago = (pago) => {
    const colores = {
        pendiente: "bg-yellow-100 text-yellow-800",
        completado: "bg-green-100 text-green-800",
        fallido: "bg-red-100 text-red-800",
    };
    return colores[pago.estado] || "bg-gray-100 text-gray-800";
};
</script>

<template>
    <Head :title="`Plan de Pago - ${planPago.proyecto?.nombre}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl"
                    style="color: var(--theme-text-primary)"
                >
                    Detalles del Plan de Pago
                </h2>
                <div class="space-x-2">
                    <Link
                        :href="`/planesPago/${planPago.id}/edit`"
                        class="inline-block px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition"
                    >
                        Editar
                    </Link>
                    <Link
                        href="/planesPago"
                        class="inline-block px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                    >
                        Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Tarjeta Principal -->
                <div
                    class="rounded-lg shadow-md overflow-hidden mb-6"
                    style="background-color: var(--theme-bg-secondary)"
                >
                    <!-- Encabezado con datos del proyecto -->
                    <div
                        class="text-white p-6"
                        style="
                            background: linear-gradient(
                                135deg,
                                var(--theme-primary) 0%,
                                var(--theme-primary-dark) 100%
                            );
                        "
                    >
                        <h3 class="text-2xl font-bold mb-2">
                            {{ planPago.proyecto?.nombre }}
                        </h3>
                        <p class="mb-4" style="color: rgba(255, 255, 255, 0.9)">
                            Cliente:
                            <span class="font-semibold">{{
                                planPago.proyecto?.cliente?.nombre
                            }}</span>
                        </p>

                        <!-- Estado -->
                        <div class="flex items-center gap-2">
                            <span
                                class="text-sm"
                                style="color: rgba(255, 255, 255, 0.9)"
                                >Estado:</span
                            >
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold"
                                style="
                                    background-color: rgba(255, 255, 255, 0.2);
                                    color: white;
                                "
                            >
                                {{ planPago.estado?.toUpperCase() }}
                            </span>
                        </div>
                    </div>

                    <!-- Estadísticas principales -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <!-- Deuda Total -->
                            <div
                                class="rounded-lg p-4 border-l-4"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    border-left-color: var(--theme-primary);
                                    border: 1px solid var(--theme-border);
                                "
                            >
                                <p
                                    class="text-xs uppercase tracking-wide font-semibold"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Deuda Total
                                </p>
                                <p
                                    class="text-2xl font-bold mt-1"
                                    style="color: var(--theme-primary)"
                                >
                                    {{ formatearMoneda(planPago.deuda_total) }}
                                </p>
                            </div>

                            <!-- Pagado -->
                            <div
                                class="rounded-lg p-4 border-l-4"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    border-left-color: var(--theme-success);
                                    border: 1px solid var(--theme-border);
                                "
                            >
                                <p
                                    class="text-xs uppercase tracking-wide font-semibold"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Pagado
                                </p>
                                <p
                                    class="text-2xl font-bold mt-1"
                                    style="color: var(--theme-success)"
                                >
                                    {{ formatearMoneda(planPago.pagado_total) }}
                                </p>
                            </div>

                            <!-- Saldo Pendiente -->
                            <div
                                class="rounded-lg p-4 border-l-4"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    border-left-color: var(--theme-warning);
                                    border: 1px solid var(--theme-border);
                                "
                            >
                                <p
                                    class="text-xs uppercase tracking-wide font-semibold"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Saldo Pendiente
                                </p>
                                <p
                                    class="text-2xl font-bold mt-1"
                                    style="color: var(--theme-warning)"
                                >
                                    {{
                                        formatearMoneda(
                                            planPago.deuda_total -
                                                planPago.pagado_total
                                        )
                                    }}
                                </p>
                            </div>

                            <!-- Porcentaje -->
                            <div
                                class="rounded-lg p-4 border-l-4"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    border-left-color: var(--theme-info);
                                    border: 1px solid var(--theme-border);
                                "
                            >
                                <p
                                    class="text-xs uppercase tracking-wide font-semibold"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Progreso
                                </p>
                                <p
                                    class="text-2xl font-bold mt-1"
                                    style="color: var(--theme-info)"
                                >
                                    {{ porcentajePago() }}%
                                </p>
                            </div>
                        </div>

                        <!-- Barra de Progreso -->
                        <div class="mb-6">
                            <div
                                class="rounded-full h-4 overflow-hidden"
                                style="
                                    background-color: var(--theme-bg-tertiary);
                                "
                            >
                                <div
                                    class="h-full transition-all duration-500"
                                    style="
                                        background: linear-gradient(
                                            90deg,
                                            var(--theme-success) 0%,
                                            var(--theme-success-dark) 100%
                                        );
                                    "
                                    :style="{ width: porcentajePago() + '%' }"
                                ></div>
                            </div>
                            <p
                                class="text-xs mt-2 text-center"
                                style="color: var(--theme-text-secondary)"
                            >
                                {{ porcentajePago() }}% pagado de
                                {{ formatearMoneda(planPago.deuda_total) }}
                            </p>
                        </div>

                        <!-- Información adicional -->
                        <div
                            class="grid grid-cols-2 md:grid-cols-3 gap-4 rounded-lg p-4"
                            style="background-color: var(--theme-bg-tertiary)"
                        >
                            <div>
                                <p
                                    class="text-xs font-semibold"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Número de Pagos
                                </p>
                                <p
                                    class="text-xl font-bold mt-1"
                                    style="color: var(--theme-text-primary)"
                                >
                                    {{ planPago.numero_deudas }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-semibold"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Pagos Completados
                                </p>
                                <p
                                    class="text-xl font-bold mt-1"
                                    style="color: var(--theme-success)"
                                >
                                    {{ planPago.numero_pagos }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs font-semibold"
                                    style="color: var(--theme-text-secondary)"
                                >
                                    Fecha de Creación
                                </p>
                                <p
                                    class="text-lg font-bold mt-1"
                                    style="color: var(--theme-text-primary)"
                                >
                                    {{ formatearFecha(planPago.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Pagos -->
                <div
                    class="rounded-lg shadow-md overflow-hidden"
                    style="background-color: var(--theme-bg-secondary)"
                >
                    <div
                        class="px-6 py-4 flex justify-between items-center border-b"
                        style="
                            background-color: var(--theme-bg-tertiary);
                            border-color: var(--theme-border);
                        "
                    >
                        <h3
                            class="font-semibold text-lg"
                            style="color: var(--theme-text-primary)"
                        >
                            Pagos Asociados
                        </h3>
                        <button
                            @click="expandirPagos = !expandirPagos"
                            class="px-3 py-1 rounded-lg transition text-sm font-semibold"
                            style="
                                background-color: var(--theme-primary);
                                color: white;
                            "
                        >
                            {{ expandirPagos ? "Contraer" : "Expandir" }}
                        </button>
                    </div>

                    <div v-if="expandirPagos" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead
                                style="
                                    background-color: var(--theme-bg-tertiary);
                                    border-bottom: 1px solid var(--theme-border);
                                "
                            >
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left font-semibold"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        #
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-semibold"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        Fecha Programada
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        Monto
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center font-semibold"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-semibold"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        Método de Pago
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center font-semibold"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="
                                        !planPago.pagos ||
                                        planPago.pagos.length === 0
                                    "
                                    class="border-b"
                                    style="border-color: var(--theme-border)"
                                >
                                    <td
                                        colspan="6"
                                        class="px-6 py-8 text-center"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        No hay pagos registrados
                                    </td>
                                </tr>
                                <tr
                                    v-for="(pago, index) in planPago.pagos"
                                    :key="pago.id"
                                    class="border-b transition duration-200"
                                    style="
                                        border-color: var(--theme-border);
                                        background-color: var(
                                            --theme-bg-primary
                                        );
                                    "
                                    @mouseenter="
                                        $event.target.closest(
                                            'tr'
                                        ).style.backgroundColor =
                                            'var(--theme-bg-tertiary)'
                                    "
                                    @mouseleave="
                                        $event.target.closest(
                                            'tr'
                                        ).style.backgroundColor =
                                            'var(--theme-bg-primary)'
                                    "
                                >
                                    <td
                                        class="px-6 py-4 font-semibold"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        {{ index + 1 }}
                                    </td>
                                    <td
                                        class="px-6 py-4"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        {{ formatearFecha(pago.fecha) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-bold"
                                        style="color: var(--theme-primary)"
                                    >
                                        {{ formatearMoneda(pago.total) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                            style="
                                                background-color: rgba(
                                                    79,
                                                    172,
                                                    254,
                                                    0.1
                                                );
                                                color: #4facfe;
                                            "
                                        >
                                            {{ pago.estado?.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4"
                                        style="color: var(--theme-text-primary)"
                                    >
                                        {{ pago.metodo_pago || "---" }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <Link
                                            :href="`/pagos/${pago.id}`"
                                            class="font-semibold transition"
                                            style="color: var(--theme-primary)"
                                        >
                                            Ver Detalles
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else
                        class="px-6 py-8 text-center"
                        style="color: var(--theme-text-secondary)"
                    >
                        {{ planPago.pagos?.length || 0 }} pago(s) registrado(s)
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
