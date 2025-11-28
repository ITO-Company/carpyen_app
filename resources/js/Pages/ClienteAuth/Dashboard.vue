<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    cliente: {
        type: Object,
        required: true,
    },
    proyectos: {
        type: Array,
        required: true,
    },
});

const logout = () => {
    router.post(route('cliente.logout'));
};

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'pendiente': 'bg-yellow-500/20 text-yellow-300 border-yellow-400/30',
        'en_proceso': 'bg-blue-500/20 text-blue-300 border-blue-400/30',
        'completado': 'bg-green-500/20 text-green-300 border-green-400/30',
        'cancelado': 'bg-red-500/20 text-red-300 border-red-400/30',
    };
    return classes[estado] || 'bg-gray-500/20 text-gray-300 border-gray-400/30';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'pendiente': 'Pendiente',
        'en_proceso': 'En Proceso',
        'completado': 'Completado',
        'cancelado': 'Cancelado',
    };
    return labels[estado] || estado;
};
</script>

<template>
    <Head title="Portal de Clientes - Dashboard" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Header -->
        <header class="backdrop-blur-xl bg-white/5 border-b border-white/10 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-400 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-white">Portal de Clientes</h1>
                            <p class="text-sm text-purple-200">Bienvenido, {{ cliente.nombre }}</p>
                        </div>
                    </div>
                    <button
                        @click="logout"
                        class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg border border-white/20 transition-all duration-200 hover:scale-105 active:scale-95"
                    >
                        Cerrar Sesión
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Card -->
            <div class="mb-8 backdrop-blur-xl bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl p-6 border border-white/20 shadow-xl">
                <h2 class="text-2xl font-bold text-white mb-2">Tus Proyectos</h2>
                <p class="text-purple-100">Aquí puedes ver todos tus proyectos y sus planes de pago asociados.</p>
            </div>

            <!-- Projects Table -->
            <div class="backdrop-blur-xl bg-white/5 rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/10">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Proyecto
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Descripción
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-purple-200 uppercase tracking-wider">
                                    Ubicación
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
                                v-for="proyecto in proyectos"
                                :key="proyecto.id"
                                class="hover:bg-white/5 transition-colors duration-150"
                            >
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white">{{ proyecto.nombre }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-purple-100">
                                        {{ proyecto.descripcion || 'Sin descripción' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-purple-100">
                                        {{ proyecto.ubicacion || 'No especificada' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="getEstadoBadgeClass(proyecto.estado)"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border backdrop-blur-sm"
                                    >
                                        {{ getEstadoLabel(proyecto.estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <Link
                                        :href="route('cliente.proyectos.show', proyecto.id)"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        Ver Planes de Pago
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="proyectos.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-purple-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-purple-200 text-lg font-medium">No tienes proyectos registrados</p>
                                        <p class="text-purple-300 text-sm mt-2">Contacta a tu vendedor para más información</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-12 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-purple-300 text-sm">
                    © 2025 Carpyen. Todos los derechos reservados.
                </p>
            </div>
        </footer>
    </div>
</template>
