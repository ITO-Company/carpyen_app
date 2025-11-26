<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    proyecto: Object
});
</script>

<template>
    <Head :title="`Proyecto: ${proyecto.nombre}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Detalles del Proyecto
                </h2>
                <div class="flex gap-3">
                    <Link :href="route('proyectos.cotizaciones.index', proyecto.id)" class="btn btn-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M20 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h11"></path>
                        </svg>
                        Ver Cotizaciones
                    </Link>
                    <Link :href="route('proyectos.edit', proyecto.id)" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar
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
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <div class="detail-grid">
                        <!-- Nombre -->
                        <div class="detail-item full-width">
                            <label class="detail-label">Nombre del Proyecto</label>
                            <p class="detail-value">{{ proyecto.nombre }}</p>
                        </div>

                        <!-- Cliente -->
                        <div class="detail-item">
                            <label class="detail-label">Cliente</label>
                            <p class="detail-value">{{ proyecto.cliente?.nombre || 'Sin cliente' }}</p>
                        </div>

                        <!-- Vendedor -->
                        <div class="detail-item">
                            <label class="detail-label">Vendedor Asignado</label>
                            <p class="detail-value">{{ proyecto.vendedor?.name || 'Sin asignar' }}</p>
                        </div>

                        <!-- Estado -->
                        <div class="detail-item">
                            <label class="detail-label">Estado</label>
                            <span :class="['badge', getEstadoClass(proyecto.estado)]">
                                {{ getEstadoTexto(proyecto.estado) }}
                            </span>
                        </div>

                        <!-- Ubicación -->
                        <div class="detail-item">
                            <label class="detail-label">Ubicación</label>
                            <p class="detail-value">{{ proyecto.ubicacion || 'No especificada' }}</p>
                        </div>

                        <!-- Descripción -->
                        <div class="detail-item full-width">
                            <label class="detail-label">Descripción</label>
                            <p class="detail-value">{{ proyecto.descripcion || 'Sin descripción' }}</p>
                        </div>

                        <!-- Fechas -->
                        <div class="detail-item">
                            <label class="detail-label">Fecha de Creación</label>
                            <p class="detail-value">{{ new Date(proyecto.created_at).toLocaleDateString('es-ES') }}</p>
                        </div>

                        <div class="detail-item">
                            <label class="detail-label">Última Actualización</label>
                            <p class="detail-value">{{ new Date(proyecto.updated_at).toLocaleDateString('es-ES') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    methods: {
        getEstadoClass(estado) {
            const estados = {
                'pendiente': 'badge-warning',
                'en_proceso': 'badge-info',
                'completado': 'badge-success',
                'cancelado': 'badge-error'
            };
            return estados[estado] || 'badge';
        },
        getEstadoTexto(estado) {
            const textos = {
                'pendiente': 'Pendiente',
                'en_proceso': 'En Proceso',
                'completado': 'Completado',
                'cancelado': 'Cancelado'
            };
            return textos[estado] || estado;
        }
    }
};
</script>

<style scoped>
.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-6);
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-2);
}

.detail-item.full-width {
    grid-column: 1 / -1;
}

.detail-label {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--theme-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.detail-value {
    font-size: var(--font-size-base);
    color: var(--theme-text-primary);
    line-height: 1.6;
}

@media (max-width: 768px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>
