<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    proyecto: Object,
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

const getMetroTipo = (tipoMetro) => {
    const tipos = {
        'lineal': 'Lineal',
        'cuadrado': 'Cuadrado'
    };
    return tipos[tipoMetro] || tipoMetro;
};
</script>

<template>
    <Head :title="`Cotizaciones - ${proyecto.nombre}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1">
                    <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                        Cotizaciones del Proyecto
                    </h2>
                    <p class="text-sm" style="color: var(--theme-text-secondary)">
                        {{ proyecto.nombre }} - {{ proyecto.cliente?.nombre }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('proyectos.cotizaciones.create', proyecto.id)" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nueva Cotización
                    </Link>
                    <Link :href="route('proyectos.index')" class="btn btn-secondary">
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
                                    <th>Total (Bs.)</th>
                                    <th>Estado</th>
                                    <th>Tipo</th>
                                    <th>Metros</th>
                                    <th>Muebles</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cotizacion in cotizaciones.data" :key="cotizacion.id">
                                    <td>{{ cotizacion.id }}</td>
                                    <td class="font-semibold">{{ Number(cotizacion.total).toFixed(2) }}</td>
                                    <td>
                                        <span :class="['badge', getEstadoClass(cotizacion.estado)]">
                                            {{ getEstadoTexto(cotizacion.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-xs badge" :style="{
                                            backgroundColor: cotizacion.tipo_metro === 'lineal' ? 'var(--theme-info)' : 'var(--theme-warning)',
                                            color: 'white'
                                        }">
                                            {{ getMetroTipo(cotizacion.tipo_metro) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span v-if="cotizacion.cantidad_metro">
                                            {{ cotizacion.cantidad_metro }} m² (Bs. {{ Number(cotizacion.costo_metro).toFixed(2) }})
                                        </span>
                                        <span v-else style="color: var(--theme-text-secondary)">—</span>
                                    </td>
                                    <td>
                                        <span v-if="cotizacion.mueble_numero">
                                            {{ cotizacion.mueble_numero }} un. (Bs. {{ Number(cotizacion.costo_mueble).toFixed(2) }})
                                        </span>
                                        <span v-else style="color: var(--theme-text-secondary)">—</span>
                                    </td>
                                    <td>{{ new Date(cotizacion.created_at).toLocaleDateString('es-ES') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link
                                                :href="route('cotizaciones.disenos.index', cotizacion.id)"
                                                class="btn-icon btn-icon-view"
                                                title="Ver Diseños"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="route('proyectos.cotizaciones.edit', [proyecto.id, cotizacion.id])"
                                                class="btn-icon btn-icon-edit"
                                                title="Editar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="route('proyectos.cotizaciones.destroy', [proyecto.id, cotizacion.id])"
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
                                    <td colspan="8" class="text-center py-8" style="color: var(--theme-text-secondary)">
                                        No hay cotizaciones registradas para este proyecto
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Info de cotizaciones -->
                    <div v-if="cotizaciones.data && cotizaciones.data.length > 0" class="mt-6 p-4" style="background-color: var(--theme-bg-secondary); border-radius: 8px;">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">Total Cotizaciones</p>
                                <p class="text-lg font-semibold" style="color: var(--theme-text-primary);">{{ cotizaciones.data.length }}</p>
                            </div>
                            <div>
                                <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">Suma Total</p>
                                <p class="text-lg font-semibold" style="color: var(--theme-text-primary);">
                                    Bs. {{ (cotizaciones.data.reduce((sum, c) => sum + parseFloat(c.total), 0)).toFixed(2) }}
                                </p>
                            </div>
                            <div>
                                <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">Aprobadas</p>
                                <p class="text-lg font-semibold" style="color: var(--theme-success);">
                                    {{ cotizaciones.data.filter(c => c.estado === 'aprobada').length }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.flex {
    display: flex;
}

.justify-between {
    justify-content: space-between;
}

.items-center {
    align-items: center;
}

.gap-3 {
    gap: var(--spacing-3);
}

.gap-4 {
    gap: var(--spacing-4);
}

.mt-6 {
    margin-top: var(--spacing-6);
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

.text-lg {
    font-size: var(--font-size-lg);
}

.font-semibold {
    font-weight: 600;
}

.flex-col {
    flex-direction: column;
}

.grid-cols-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
}

.action-buttons {
    display: flex;
    gap: var(--spacing-2);
    justify-content: center;
}

.table tbody tr:hover {
    background-color: var(--theme-bg-secondary);
    transition: all 0.2s ease;
}
</style>
