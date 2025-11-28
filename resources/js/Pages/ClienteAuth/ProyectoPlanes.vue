<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    proyecto: {
        type: Object,
        required: true,
    },
    planPagos: {
        type: Array,
        required: true,
    },
});

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'activo': 'bg-blue-500/20 text-blue-300 border-blue-400/30',
        'completado': 'bg-green-500/20 text-green-300 border-green-400/30',
        'mora': 'bg-red-500/20 text-red-300 border-red-400/30',
    };
    return classes[estado] || 'bg-gray-500/20 text-gray-300 border-gray-400/30';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'activo': 'Activo',
        'completado': 'Completado',
        'mora': 'En Mora',
    };
    return labels[estado] || estado;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value);
};
</script>

<template>
    <Head :title="`Planes de Pago - ${proyecto.nombre}`" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Header -->
        <header class="backdrop-blur-xl bg-white/5 border-b border-white/10 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <Link
                            :href="route('cliente.dashboard')"
                            class="flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg border border-white/20 transition-all duration-200"
                        >
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <div>
                            <h1 class="text-xl font-bold text-white">Planes de Pago</h1>
                            <p class="text-sm text-purple-200">{{ proyecto.nombre }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Project Info Card -->
            <div class="mb-8 backdrop-blur-xl bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl p-6 border border-white/20 shadow-xl">
                <h2 class="text-2xl font-bold text-white mb-4">{{ proyecto.nombre }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-purple-200 text-sm">Descripción</p>
                        <p class="text-white font-medium">{{ proyecto.descripcion || 'Sin descripción' }}</p>
                    </div>
                    <div>
                        <p class="text-purple-200 text-sm">Ubicación</p>
                        <p class="text-white font-medium">{{ proyecto.ubicacion || 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-purple-200 text-sm">Estado</p>
                        <p class="text-white font-medium capitalize">{{ proyecto.estado.replace('_', ' ') }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Plans Table -->
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 bg-white/5 border-b border-white/10">
                    <h3 class="text-lg font-semibold text-white">Planes de Pago del Proyecto</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/10">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Deuda Total
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Pagado
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Saldo Pendiente
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    N° Pagos
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr
                                v-for="plan in planPagos"
                                :key="plan.id"
                                class="hover:bg-white/5 transition-colors duration-150"
                            >
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-white">
                                        {{ formatCurrency(plan.deuda_total) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-green-300">
                                        {{ formatCurrency(plan.pagado_total) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-yellow-300">
                                        {{ formatCurrency(plan.deuda_total - plan.pagado_total) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-purple-100">
                                        {{ plan.numero_pagos }} / {{ plan.numero_deudas }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="getEstadoBadgeClass(plan.estado)"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border backdrop-blur-sm"
                                    >
                                        {{ getEstadoLabel(plan.estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <Link
                                        :href="route('cliente.planes.show', plan.id)"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver Pagos
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="planPagos.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-purple-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-purple-200 text-lg font-medium">No hay planes de pago registrados</p>
                                        <p class="text-purple-300 text-sm mt-2">Este proyecto aún no tiene planes de pago asociados</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
