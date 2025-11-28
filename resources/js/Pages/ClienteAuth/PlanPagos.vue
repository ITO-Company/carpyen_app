<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    planPago: {
        type: Object,
        required: true,
    },
    proyecto: {
        type: Object,
        required: true,
    },
    pagos: {
        type: Array,
        required: true,
    },
});

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'pendiente': 'bg-yellow-500/20 text-yellow-300 border-yellow-400/30',
        'completado': 'bg-green-500/20 text-green-300 border-green-400/30',
        'fallido': 'bg-red-500/20 text-red-300 border-red-400/30',
    };
    return classes[estado] || 'bg-gray-500/20 text-gray-300 border-gray-400/30';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'pendiente': 'Pendiente',
        'completado': 'Completado',
        'fallido': 'Fallido',
    };
    return labels[estado] || estado;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const saldoPendiente = props.planPago.deuda_total - props.planPago.pagado_total;
const porcentajePagado = (props.planPago.pagado_total / props.planPago.deuda_total) * 100;
</script>

<template>
    <Head :title="`Pagos - ${proyecto.nombre}`" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Header -->
        <header class="backdrop-blur-xl bg-white/5 border-b border-white/10 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <Link
                            :href="route('cliente.proyectos.show', proyecto.id)"
                            class="flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg border border-white/20 transition-all duration-200"
                        >
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <div>
                            <h1 class="text-xl font-bold text-white">Detalle de Pagos</h1>
                            <p class="text-sm text-purple-200">{{ proyecto.nombre }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Payment Plan Summary -->
            <div class="mb-8 backdrop-blur-xl bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl p-6 border border-white/20 shadow-xl">
                <h2 class="text-2xl font-bold text-white mb-6">Resumen del Plan de Pago</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="backdrop-blur-sm bg-white/5 rounded-xl p-4 border border-white/10">
                        <p class="text-purple-200 text-sm mb-1">Deuda Total</p>
                        <p class="text-white text-2xl font-bold">{{ formatCurrency(planPago.deuda_total) }}</p>
                    </div>
                    <div class="backdrop-blur-sm bg-green-500/10 rounded-xl p-4 border border-green-400/20">
                        <p class="text-green-200 text-sm mb-1">Total Pagado</p>
                        <p class="text-green-100 text-2xl font-bold">{{ formatCurrency(planPago.pagado_total) }}</p>
                    </div>
                    <div class="backdrop-blur-sm bg-yellow-500/10 rounded-xl p-4 border border-yellow-400/20">
                        <p class="text-yellow-200 text-sm mb-1">Saldo Pendiente</p>
                        <p class="text-yellow-100 text-2xl font-bold">{{ formatCurrency(saldoPendiente) }}</p>
                    </div>
                    <div class="backdrop-blur-sm bg-blue-500/10 rounded-xl p-4 border border-blue-400/20">
                        <p class="text-blue-200 text-sm mb-1">Progreso</p>
                        <p class="text-blue-100 text-2xl font-bold">{{ porcentajePagado.toFixed(1) }}%</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-white/10 rounded-full h-4 overflow-hidden border border-white/20">
                    <div
                        class="h-full bg-gradient-to-r from-green-400 to-emerald-500 transition-all duration-500 rounded-full shadow-lg"
                        :style="{ width: `${porcentajePagado}%` }"
                    ></div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 bg-white/5 border-b border-white/10">
                    <h3 class="text-lg font-semibold text-white">Historial de Pagos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/10">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Monto
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Método de Pago
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    ID Transacción
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr
                                v-for="pago in pagos"
                                :key="pago.id"
                                class="hover:bg-white/5 transition-colors duration-150"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-purple-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-white">{{ formatDate(pago.fecha) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-white">
                                        {{ formatCurrency(pago.total) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-purple-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span class="text-sm text-purple-100">{{ pago.metodo_pago || 'No especificado' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="getEstadoBadgeClass(pago.estado)"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border backdrop-blur-sm"
                                    >
                                        <span
                                            class="w-2 h-2 rounded-full mr-2"
                                            :class="{
                                                'bg-green-400': pago.estado === 'completado',
                                                'bg-yellow-400': pago.estado === 'pendiente',
                                                'bg-red-400': pago.estado === 'fallido',
                                            }"
                                        ></span>
                                        {{ getEstadoLabel(pago.estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-purple-100 font-mono">
                                        {{ pago.transaccion_id || 'N/A' }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="pagos.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-purple-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        <p class="text-purple-200 text-lg font-medium">No hay pagos registrados</p>
                                        <p class="text-purple-300 text-sm mt-2">Este plan de pago aún no tiene pagos asociados</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Back to Dashboard Button -->
            <div class="mt-8 flex justify-center">
                <Link
                    :href="route('cliente.dashboard')"
                    class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-xl border border-white/20 transition-all duration-200 hover:scale-105 active:scale-95"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Volver al Dashboard
                </Link>
            </div>
        </main>
    </div>
</template>
