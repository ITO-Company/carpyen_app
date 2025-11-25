<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    cotizaciones: Object
});

const getEstadoClass = (estado) => {
    const estados = {
        'pendiente': 'badge-warning',
        'aprobada': 'badge-success',
        'rechazada': 'badge-error'
    };
    return estados[estado] || 'badge';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'pendiente': 'Pendiente',
        'aprobada': 'Aprobada',
        'rechazada': 'Rechazada'
    };
    return textos[estado] || estado;
};
</script>

<template>
    <Head title="Cotizaciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Cotizaciones
                </h2>
                <Link :href="route('cotizaciones.create')" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nueva Cotización
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proyecto</th>
                                    <th>Cliente</th>
                                    <th>Total (Bs.)</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cotizacion in cotizaciones.data" :key="cotizacion.id">
                                    <td>{{ cotizacion.id }}</td>
                                    <td>{{ cotizacion.proyecto?.nombre || 'Sin proyecto' }}</td>
                                    <td>{{ cotizacion.proyecto?.cliente?.nombre || 'Sin cliente' }}</td>
                                    <td class="font-semibold">{{ Number(cotizacion.total).toFixed(2) }}</td>
                                    <td>
                                        <span :class="['badge', getEstadoClass(cotizacion.estado)]">
                                            {{ getEstadoTexto(cotizacion.estado) }}
                                        </span>
                                    </td>
                                    <td>{{ new Date(cotizacion.created_at).toLocaleDateString('es-ES') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link
                                                :href="route('cotizaciones.edit', cotizacion.id)"
                                                class="btn-icon btn-icon-edit"
                                                title="Editar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="route('cotizaciones.destroy', cotizacion.id)"
                                                method="delete"
                                                as="button"
                                                class="btn-icon btn-icon-delete"
                                                title="Eliminar"
                                                @click="(e) => !confirm('¿Estás seguro de eliminar esta cotización?') && e.preventDefault()"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="cotizaciones.data.length === 0">
                                    <td colspan="7" class="text-center py-8" style="color: var(--theme-text-secondary)">
                                        No hay cotizaciones registradas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.action-buttons {
    display: flex;
    gap: var(--spacing-2);
    justify-content: center;
}
</style>
