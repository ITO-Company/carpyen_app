<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">
                        Gestión de Pagos
                    </h1>
                    <p class="text-gray-600 mt-2">
                        Visualiza y gestiona todos los pagos de tus planes
                    </p>
                </div>
                <Link
                    href="/planesPago/create"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Nuevo Plan de Pago
                </Link>
            </div>

            <!-- Mensaje de éxito si existe -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg"
            >
                ✓ {{ $page.props.flash.success }}
            </div>

            <!-- VISTA DE PLANES DE PAGO -->
                <div v-if="planesPago.length > 0" class="grid gap-6">
                    <div
                        v-for="plan in planesPago"
                        :key="plan.id"
                        class="bg-white rounded-lg shadow-lg hover:shadow-xl transition overflow-hidden"
                    >
                        <div class="flex flex-col md:flex-row">
                            <!-- Contenido Principal -->
                            <div class="flex-1 p-6">
                                <!-- Encabezado -->
                                <div
                                    class="flex justify-between items-start mb-4"
                                >
                                    <div>
                                        <h3
                                            class="text-2xl font-bold text-gray-900"
                                        >
                                            {{ plan.proyecto.nombre }}
                                        </h3>
                                        <p class="text-gray-600 text-sm mt-1">
                                            Cliente:
                                            <span class="font-semibold">{{
                                                plan.proyecto.cliente.nombre
                                            }}</span>
                                        </p>
                                    </div>
                                    <span
                                        :class="[
                                            'px-4 py-2 rounded-full text-sm font-semibold',
                                            plan.estado === 'activo'
                                                ? 'bg-blue-100 text-blue-800'
                                                : plan.estado === 'completado'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800',
                                        ]"
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
                                    class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4"
                                >
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <p
                                            class="text-gray-600 text-xs font-semibold uppercase"
                                        >
                                            Deuda Total
                                        </p>
                                        <p
                                            class="text-2xl font-bold text-blue-600"
                                        >
                                            Bs
                                            {{
                                                parseFloat(
                                                    plan.deuda_total
                                                ).toFixed(2)
                                            }}
                                        </p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <p
                                            class="text-gray-600 text-xs font-semibold uppercase"
                                        >
                                            Pagado
                                        </p>
                                        <p
                                            class="text-2xl font-bold text-green-600"
                                        >
                                            Bs
                                            {{
                                                parseFloat(
                                                    plan.pagado_total
                                                ).toFixed(2)
                                            }}
                                        </p>
                                    </div>
                                    <div class="bg-orange-50 p-4 rounded-lg">
                                        <p
                                            class="text-gray-600 text-xs font-semibold uppercase"
                                        >
                                            Pendiente
                                        </p>
                                        <p
                                            class="text-2xl font-bold text-orange-600"
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
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <p
                                            class="text-gray-600 text-xs font-semibold uppercase"
                                        >
                                            Progreso
                                        </p>
                                        <p
                                            class="text-2xl font-bold text-purple-600"
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
                                <div class="mb-4">
                                    <div
                                        class="flex justify-between items-center mb-2"
                                    >
                                        <p
                                            class="text-sm font-semibold text-gray-700"
                                        >
                                            Progreso de Pago
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ plan.numero_pagos }} de
                                            {{ plan.pagos?.length || 0 }} pagos
                                            completados
                                        </p>
                                    </div>
                                    <div
                                        class="w-full bg-gray-200 rounded-full h-3 overflow-hidden"
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
                                            }"
                                            :class="[
                                                'h-full transition-all duration-300',
                                                parseFloat(plan.pagado_total) /
                                                    parseFloat(
                                                        plan.deuda_total
                                                    ) >=
                                                1
                                                    ? 'bg-green-500'
                                                    : parseFloat(
                                                          plan.pagado_total
                                                      ) /
                                                          parseFloat(
                                                              plan.deuda_total
                                                          ) >=
                                                      0.5
                                                    ? 'bg-blue-500'
                                                    : 'bg-yellow-500',
                                            ]"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div
                                class="bg-gray-50 p-6 flex flex-col justify-center gap-3 md:w-48"
                            >
                                <button
                                    @click="verPagos(plan)"
                                    class="flex items-center justify-center px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
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
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        />
                                    </svg>
                                    Ver Pagos
                                </button>
                                <Link
                                    :href="`/planesPago/${plan.id}/edit`"
                                    class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold"
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
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        />
                                    </svg>
                                    Editar
                                </Link>
                                <button
                                    @click="eliminar(plan)"
                                    class="flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold"
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
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div
                    v-if="planesPago.links && planesPago.links.length > 0"
                    class="flex justify-center gap-2 mt-8"
                >
                    <template v-for="link in planesPago.links">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-4 py-2 rounded-lg transition',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </div>

                <div
                    v-else
                    class="text-center py-16 bg-white rounded-lg shadow"
                >
                    <svg
                        class="w-16 h-16 mx-auto text-gray-400 mb-4"
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
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        No hay planes de pago disponibles
                    </h3>
                    <p class="text-gray-600">
                        Comienza creando un nuevo plan de pago
                    </p>
                </div>
            </div>

            <!-- MODAL DE PAGOS DEL PLAN -->
            <div
                v-if="mostrarModalPagos"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
                @click="cerrarModalPagos"
            >
                <div
                    class="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto"
                    @click.stop
                >
                    <!-- Header del Modal -->
                    <div
                        class="sticky top-0 bg-indigo-600 text-white p-6 flex justify-between items-center border-b border-indigo-700"
                    >
                        <div>
                            <h2 class="text-2xl font-bold">Pagos del Plan</h2>
                            <p class="text-indigo-100 text-sm mt-1">
                                {{ planSeleccionado?.proyecto?.nombre }}
                            </p>
                        </div>
                        <button
                            @click="cerrarModalPagos"
                            class="text-white hover:bg-indigo-700 p-2 rounded transition"
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

                    <div class="p-6">
                        <!-- Información del Plan -->
                        <div
                            class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200"
                        >
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                Resumen Financiero 1
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div
                                    class="bg-white p-4 rounded-lg border border-gray-200"
                                >
                                    <p
                                        class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                    >
                                        Deuda Total
                                    </p>
                                    <p class="text-2xl font-bold text-blue-600">
                                        Bs
                                        {{
                                            parseFloat(
                                                planSeleccionado?.deuda_total
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                                <div
                                    class="bg-white p-4 rounded-lg border border-gray-200"
                                >
                                    <p
                                        class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                    >
                                        Pagado
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-green-600"
                                    >
                                        Bs
                                        {{
                                            parseFloat(
                                                planSeleccionado?.pagado_total
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                                <div
                                    class="bg-white p-4 rounded-lg border border-gray-200"
                                >
                                    <p
                                        class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                    >
                                        Saldo Pendiente
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-orange-600"
                                    >
                                        Bs
                                        {{
                                            (
                                                parseFloat(
                                                    planSeleccionado?.deuda_total
                                                ) -
                                                parseFloat(
                                                    planSeleccionado?.pagado_total
                                                )
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                                <div
                                    class="bg-white p-4 rounded-lg border border-gray-200"
                                >
                                    <p
                                        class="text-gray-600 text-xs font-semibold uppercase mb-1"
                                    >
                                        Progreso
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-purple-600"
                                    >
                                        {{
                                            (
                                                (parseFloat(
                                                    planSeleccionado?.pagado_total
                                                ) /
                                                    parseFloat(
                                                        planSeleccionado?.deuda_total
                                                    )) *
                                                100
                                            ).toFixed(0)
                                        }}%
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Pagos -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead
                                    class="bg-gray-100 border-b-2 border-gray-300"
                                >
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            #
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Fecha Pago
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Monto
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Método
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Estado
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-700"
                                        >
                                            Fecha Registro 1
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr
                                        v-for="(
                                            pago, index
                                        ) in pagosSeleccionados"
                                        :key="pago.id"
                                        class="hover:bg-gray-50 transition"
                                    >
                                        <td
                                            class="px-6 py-3 text-sm text-gray-600 font-semibold"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td
                                            class="px-6 py-3 text-sm text-gray-600"
                                        >
                                            {{ formatearFecha(pago.fecha) }}
                                        </td>
                                        <td
                                            class="px-6 py-3 text-sm font-semibold text-gray-900"
                                        >
                                            Bs
                                            {{
                                                parseFloat(pago.total).toFixed(
                                                    2
                                                )
                                            }}
                                        </td>
                                        <td class="px-6 py-3 text-sm">
                                            <span
                                                class="bg-gray-100 px-3 py-1 rounded-full text-xs font-semibold"
                                            >
                                                {{
                                                    pago.metodo_pago ||
                                                    "No especificado"
                                                }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm">
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
                                            class="px-6 py-3 text-sm text-gray-600"
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
                            class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t border-gray-200"
                        >
                            <div class="text-center">
                                <p
                                    class="text-sm text-gray-600 font-semibold mb-2"
                                >
                                    Pendientes
                                </p>
                                <p class="text-3xl font-bold text-yellow-600">
                                    {{
                                        pagosSeleccionados.filter(
                                            (p) => p.estado === "pendiente"
                                        ).length
                                    }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p
                                    class="text-sm text-gray-600 font-semibold mb-2"
                                >
                                    Completados
                                </p>
                                <p class="text-3xl font-bold text-green-600">
                                    {{
                                        pagosSeleccionados.filter(
                                            (p) => p.estado === "completado"
                                        ).length
                                    }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p
                                    class="text-sm text-gray-600 font-semibold mb-2"
                                >
                                    Fallidos
                                </p>
                                <p class="text-3xl font-bold text-red-600">
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
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    planesPago: Object,
});

const mostrarModalPagos = ref(false);
const planSeleccionado = ref(null);
const pagosSeleccionados = ref([]);

const verPagos = (plan) => {
    planSeleccionado.value = plan;
    pagosSeleccionados.value = plan.pagos || [];
    mostrarModalPagos.value = true;
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
    if (confirm(`¿Está seguro de que desea eliminar el plan de pago para "${plan.proyecto.nombre}"?`)) {
        useForm({}).delete(`/planesPago/${plan.id}`);
    }
};
</script>

<style scoped></style>
