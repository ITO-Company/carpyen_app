<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    href="/planesPago"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-4"
                >
                    <svg
                        class="w-4 h-4 mr-2"
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
                    Volver a Planes de Pago
                </Link>
            </div>

            <!-- Grid Principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna Izquierda - Información del Plan -->
                <div class="lg:col-span-2">
                    <!-- Información del Proyecto -->
                    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">
                            {{ planPago.proyecto.nombre }}
                        </h1>
                        <p class="text-gray-600 mb-6">
                            Plan de Pago #{{ planPago.id }}
                        </p>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Cliente
                                </p>
                                <p class="text-lg text-gray-900 font-semibold">
                                    {{ planPago.proyecto.cliente.nombre }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Estado del Plan
                                </p>
                                <span
                                    :class="[
                                        'inline-flex px-4 py-2 rounded-full text-sm font-semibold',
                                        planPago.estado === 'activo'
                                            ? 'bg-blue-100 text-blue-800'
                                            : planPago.estado === 'completado'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{
                                        planPago.estado
                                            .charAt(0)
                                            .toUpperCase() +
                                        planPago.estado.slice(1)
                                    }}
                                </span>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Estado del Proyecto
                                </p>
                                <span
                                    :class="[
                                        'inline-flex px-4 py-2 rounded-full text-sm font-semibold',
                                        planPago.proyecto.estado ===
                                        'en_proceso'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : planPago.proyecto.estado ===
                                              'completado'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800',
                                    ]"
                                >
                                    {{
                                        formatearEstado(
                                            planPago.proyecto.estado
                                        )
                                    }}
                                </span>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Ubicación
                                </p>
                                <p class="text-lg text-gray-900">
                                    {{
                                        planPago.proyecto.ubicacion ||
                                        "No especificada"
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información Financiera -->
                    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Información Financiera
                        </h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                            <div
                                class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500"
                            >
                                <p
                                    class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                >
                                    Deuda Total
                                </p>
                                <p class="text-3xl font-bold text-blue-600">
                                    Bs
                                    {{
                                        parseFloat(
                                            planPago.deuda_total
                                        ).toFixed(2)
                                    }}
                                </p>
                            </div>
                            <div
                                class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500"
                            >
                                <p
                                    class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                >
                                    Pagado
                                </p>
                                <p class="text-3xl font-bold text-green-600">
                                    Bs
                                    {{
                                        parseFloat(
                                            planPago.pagado_total
                                        ).toFixed(2)
                                    }}
                                </p>
                            </div>
                            <div
                                class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500"
                            >
                                <p
                                    class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                >
                                    Saldo Pendiente
                                </p>
                                <p class="text-3xl font-bold text-orange-600">
                                    Bs
                                    {{
                                        (
                                            parseFloat(planPago.deuda_total) -
                                            parseFloat(planPago.pagado_total)
                                        ).toFixed(2)
                                    }}
                                </p>
                            </div>
                            <div
                                class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500"
                            >
                                <p
                                    class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                >
                                    Progreso
                                </p>
                                <p class="text-3xl font-bold text-purple-600">
                                    {{
                                        (
                                            (parseFloat(planPago.pagado_total) /
                                                parseFloat(
                                                    planPago.deuda_total
                                                )) *
                                            100
                                        ).toFixed(0)
                                    }}%
                                </p>
                            </div>
                        </div>

                        <!-- Barra de Progreso -->
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <p class="text-sm font-semibold text-gray-700">
                                    Progreso de Pago
                                </p>
                                <p class="text-sm text-gray-600">
                                    Bs
                                    {{
                                        parseFloat(
                                            planPago.pagado_total
                                        ).toFixed(2)
                                    }}
                                    de Bs
                                    {{
                                        parseFloat(
                                            planPago.deuda_total
                                        ).toFixed(2)
                                    }}
                                </p>
                            </div>
                            <div
                                class="w-full bg-gray-200 rounded-full h-4 overflow-hidden"
                            >
                                <div
                                    :style="{
                                        width:
                                            (parseFloat(planPago.pagado_total) /
                                                parseFloat(
                                                    planPago.deuda_total
                                                )) *
                                                100 +
                                            '%',
                                    }"
                                    :class="[
                                        'h-full transition-all duration-300',
                                        parseFloat(planPago.pagado_total) /
                                            parseFloat(planPago.deuda_total) >=
                                        1
                                            ? 'bg-green-500'
                                            : parseFloat(
                                                  planPago.pagado_total
                                              ) /
                                                  parseFloat(
                                                      planPago.deuda_total
                                                  ) >=
                                              0.5
                                            ? 'bg-blue-500'
                                            : 'bg-yellow-500',
                                    ]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Pagos -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Detalle de Pagos ({{ planPago.pagos.length }})
                        </h2>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead
                                    class="bg-gray-100 border-b-2 border-gray-300"
                                >
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            #
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Fecha
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Monto
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Método de Pago
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Estado
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Fecha de Registro
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr
                                        v-for="(pago, index) in planPago.pagos"
                                        :key="pago.id"
                                        class="hover:bg-gray-50 transition"
                                    >
                                        <td
                                            class="px-4 py-3 text-sm text-gray-600 font-semibold"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-gray-600"
                                        >
                                            {{ formatearFecha(pago.fecha) }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm font-semibold text-gray-900"
                                        >
                                            Bs
                                            {{
                                                parseFloat(pago.total).toFixed(
                                                    2
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-gray-600"
                                        >
                                            <span
                                                class="bg-gray-100 px-3 py-1 rounded-full text-xs"
                                            >
                                                {{
                                                    pago.metodo_pago ||
                                                    "No especificado"
                                                }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span
                                                :class="[
                                                    'inline-flex px-3 py-1 rounded-full text-xs font-semibold',
                                                    pago.estado === 'pendiente'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : pago.estado ===
                                                          'completado'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800',
                                                ]"
                                            >
                                                {{
                                                    pago.estado
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    pago.estado.slice(1)
                                                }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-gray-600"
                                        >
                                            {{
                                                formatearFecha(pago.created_at)
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen de Estados -->
                        <div
                            class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-200"
                        >
                            <div class="text-center">
                                <p class="text-sm text-gray-600 font-semibold">
                                    Pendientes
                                </p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{
                                        planPago.pagos.filter(
                                            (p) => p.estado === "pendiente"
                                        ).length
                                    }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600 font-semibold">
                                    Completados
                                </p>
                                <p class="text-2xl font-bold text-green-600">
                                    {{
                                        planPago.pagos.filter(
                                            (p) => p.estado === "completado"
                                        ).length
                                    }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600 font-semibold">
                                    Fallidos
                                </p>
                                <p class="text-2xl font-bold text-red-600">
                                    {{
                                        planPago.pagos.filter(
                                            (p) => p.estado === "fallido"
                                        ).length
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Acciones -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-8 sticky top-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            Acciones
                        </h3>

                        <div class="space-y-3">
                            <Link
                                :href="`/planesPago/${planPago.id}/edit`"
                                class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold"
                            >
                                <svg
                                    class="w-5 h-5 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    />
                                </svg>
                                Editar Plan
                            </Link>

                            <Link
                                :href="`/proyectos/${planPago.proyecto.id}`"
                                class="flex items-center justify-center w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
                            >
                                <svg
                                    class="w-5 h-5 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                Ver Proyecto
                            </Link>

                            <button
                                @click="eliminarPlan"
                                class="flex items-center justify-center w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold"
                            >
                                <svg
                                    class="w-5 h-5 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                                Eliminar Plan
                            </button>
                        </div>

                        <!-- Resumen Quick -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <p
                                class="text-xs text-gray-600 font-semibold uppercase mb-4"
                            >
                                Resumen Rápido
                            </p>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600"
                                        >Total Deudas</span
                                    >
                                    <span class="font-semibold text-gray-900">{{
                                        planPago.numero_deudas
                                    }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600"
                                        >Total Pagos</span
                                    >
                                    <span class="font-semibold text-gray-900">{{
                                        planPago.pagos.length
                                    }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600"
                                        >Pagos Completados</span
                                    >
                                    <span class="font-semibold text-gray-900">{{
                                        planPago.numero_pagos
                                    }}</span>
                                </div>
                                <div
                                    class="flex justify-between items-center pt-3 border-t border-gray-200"
                                >
                                    <span class="text-sm text-gray-600"
                                        >Tasa de Compleción</span
                                    >
                                    <span class="font-semibold text-gray-900">
                                        {{
                                            (
                                                (planPago.numero_pagos /
                                                    planPago.pagos.length) *
                                                100
                                            ).toFixed(0)
                                        }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    planPago: Object,
});

const formatearFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatearEstado = (estado) => {
    const estados = {
        pendiente: "Pendiente",
        en_proceso: "En Proceso",
        completado: "Completado",
        cancelado: "Cancelado",
    };
    return estados[estado] || estado;
};

const eliminarPlan = () => {
    if (
        confirm(
            "¿Está seguro de que desea eliminar este plan de pago? Esta acción no se puede deshacer."
        )
    ) {
        useForm({}).delete(`/planesPago/${props.planPago.id}`);
    }
};
</script>

<style scoped></style>
