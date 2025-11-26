<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    href="/pagos"
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
                    Volver a Pagos
                </Link>
                <h1 class="text-4xl font-bold text-gray-900">
                    Detalles del Pago
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Información Principal -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2
                                    class="text-3xl font-bold text-gray-900 mb-2"
                                >
                                    Bs {{ parseFloat(pago.total).toFixed(2) }}
                                </h2>
                                <p class="text-gray-600">Pago #{{ pago.id }}</p>
                            </div>
                            <span
                                :class="[
                                    'px-4 py-2 rounded-full text-sm font-semibold',
                                    pago.estado === 'pendiente'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : pago.estado === 'completado'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800',
                                ]"
                            >
                                {{
                                    pago.estado.charAt(0).toUpperCase() +
                                    pago.estado.slice(1)
                                }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Proyecto
                                </p>
                                <p class="text-lg text-gray-900 font-semibold">
                                    {{ pago.plan_pago.proyecto.nombre }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Cliente
                                </p>
                                <p class="text-lg text-gray-900 font-semibold">
                                    {{ pago.plan_pago.proyecto.cliente.nombre }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Fecha de Pago
                                </p>
                                <p class="text-lg text-gray-900 font-semibold">
                                    {{ formatearFecha(pago.fecha) }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Método de Pago
                                </p>
                                <p class="text-lg text-gray-900 font-semibold">
                                    {{ pago.metodo_pago || "No especificado" }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    Fecha de Registro
                                </p>
                                <p class="text-lg text-gray-900 font-semibold">
                                    {{ formatearFecha(pago.created_at) }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-600 font-semibold uppercase mb-1"
                                >
                                    ID de Transacción
                                </p>
                                <p class="text-lg text-gray-900 font-semibold">
                                    {{ pago.transaccion_id || "No asignado" }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Plan -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">
                            Información del Plan de Pago
                        </h3>

                        <div
                            class="bg-indigo-50 p-6 rounded-lg border border-indigo-200 mb-6"
                        >
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p
                                        class="text-sm text-gray-600 font-semibold uppercase mb-2"
                                    >
                                        Deuda Total
                                    </p>
                                    <p class="text-2xl font-bold text-blue-600">
                                        Bs
                                        {{
                                            parseFloat(
                                                pago.plan_pago.deuda_total
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-sm text-gray-600 font-semibold uppercase mb-2"
                                    >
                                        Pagado
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-green-600"
                                    >
                                        Bs
                                        {{
                                            parseFloat(
                                                pago.plan_pago.pagado_total
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-sm text-gray-600 font-semibold uppercase mb-2"
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
                                                    pago.plan_pago.deuda_total
                                                ) -
                                                parseFloat(
                                                    pago.plan_pago.pagado_total
                                                )
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <Link
                            :href="`/planesPago/${pago.plan_pago.id}`"
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
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                />
                            </svg>
                            Ver Plan Completo
                        </Link>
                    </div>
                </div>

                <!-- Acciones -->
                <div>
                    <div class="bg-white rounded-lg shadow-lg p-8 sticky top-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            Acciones
                        </h3>

                        <div class="space-y-3">
                            <Link
                                :href="`/pagos/${pago.id}/edit`"
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
                                Editar
                            </Link>

                            <button
                                @click="marcarCompletado"
                                v-if="pago.estado !== 'completado'"
                                class="flex items-center justify-center w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold"
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
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                                Marcar Completado
                            </button>

                            <button
                                @click="eliminarPago"
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
                                Eliminar
                            </button>
                        </div>

                        <!-- Estado del Pago -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <p
                                class="text-xs text-gray-600 font-semibold uppercase mb-4"
                            >
                                Estado
                            </p>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <svg
                                        :class="[
                                            'w-5 h-5 mr-2',
                                            pago.estado === 'pendiente'
                                                ? 'text-yellow-500'
                                                : 'text-gray-300',
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span class="text-sm text-gray-600"
                                        >Pendiente</span
                                    >
                                </div>
                                <div class="flex items-center">
                                    <svg
                                        :class="[
                                            'w-5 h-5 mr-2',
                                            pago.estado === 'completado'
                                                ? 'text-green-500'
                                                : 'text-gray-300',
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span class="text-sm text-gray-600"
                                        >Completado</span
                                    >
                                </div>
                                <div class="flex items-center">
                                    <svg
                                        :class="[
                                            'w-5 h-5 mr-2',
                                            pago.estado === 'fallido'
                                                ? 'text-red-500'
                                                : 'text-gray-300',
                                        ]"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span class="text-sm text-gray-600"
                                        >Fallido</span
                                    >
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
    pago: Object,
});

const formatearFecha = (fecha) => {
    if (!fecha) return "";
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const marcarCompletado = () => {
    useForm({ estado: "completado" }).patch(`/pagos/${props.pago.id}`);
};

const eliminarPago = () => {
    if (confirm("¿Está seguro de que desea eliminar este pago?")) {
        useForm({}).delete(`/pagos/${props.pago.id}`);
    }
};
</script>

<style scoped></style>
