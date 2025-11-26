<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    cotizacion: Object,
    proyecto: Object,
    disenos: Object
});

const getEstadoClass = (estado) => {
    const estados = {
        'pendiente': 'badge-warning',
        'en_proceso': 'badge-info',
        'completado': 'badge-success',
        'rechazado': 'badge-error'
    };
    return estados[estado] || 'badge';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'pendiente': 'Pendiente',
        'en_proceso': 'En Proceso',
        'completado': 'Completado',
        'rechazado': 'Rechazado'
    };
    return textos[estado] || estado;
};
</script>

<template>
    <Head :title="`Diseños - Cotización ${cotizacion.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1">
                    <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                        Diseños de la Cotización
                    </h2>
                    <p class="text-sm" style="color: var(--theme-text-secondary)">
                        {{ proyecto.nombre }} - Cotización #{{ cotizacion.id }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('cotizaciones.disenos.create', cotizacion.id)" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nuevo Diseño
                    </Link>
                    <Link :href="route('proyectos.cotizaciones.index', proyecto.id)" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Volver
                    </Link>
                </div>
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
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Diseñador</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="diseno in disenos.data" :key="diseno.id">
                                    <td>{{ diseno.id }}</td>
                                    <td>{{ diseno.descripcion || '-' }}</td>
                                    <td>
                                        <span :class="['badge', getEstadoClass(diseno.estado)]">
                                            {{ getEstadoTexto(diseno.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ diseno.diseñador?.name || 'Sin asignar' }}
                                    </td>
                                    <td>
                                        {{ diseno.fecha_inicio ? new Date(diseno.fecha_inicio).toLocaleDateString('es-ES') : '-' }}
                                    </td>
                                    <td>
                                        {{ diseno.fecha_fin ? new Date(diseno.fecha_fin).toLocaleDateString('es-ES') : '-' }}
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link
                                                :href="route('cotizaciones.disenos.edit', [cotizacion.id, diseno.id])"
                                                class="btn-icon btn-icon-edit"
                                                title="Editar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="route('cotizaciones.disenos.destroy', [cotizacion.id, diseno.id])"
                                                method="delete"
                                                as="button"
                                                class="btn-icon btn-icon-delete"
                                                title="Eliminar"
                                                @click="(e) => !confirm('¿Estás seguro de eliminar este diseño?') && e.preventDefault()"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="disenos.data.length === 0">
                                    <td colspan="7" class="text-center py-8" style="color: var(--theme-text-secondary)">
                                        No hay diseños registrados para esta cotización
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

.flex {
    display: flex;
}

.justify-between {
    justify-content: space-between;
}

.items-center {
    align-items: center;
}

.flex-col {
    flex-direction: column;
}

.gap-1 {
    gap: var(--spacing-1);
}

.gap-3 {
    gap: var(--spacing-3);
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.max-w-7xl {
    max-width: 80rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.text-center {
    text-align: center;
}

.text-sm {
    font-size: var(--font-size-sm);
}

.font-semibold {
    font-weight: 600;
}

.text-xl {
    font-size: var(--font-size-xl);
}

.leading-tight {
    line-height: 1.25;
}

.table tbody tr:hover {
    background-color: var(--theme-bg-secondary);
    transition: all 0.2s ease;
}
</style>
