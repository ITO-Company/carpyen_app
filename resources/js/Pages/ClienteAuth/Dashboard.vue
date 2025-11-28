<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClienteAuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';

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

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'pendiente': 'badge-warning',
        'en_proceso': 'badge-info',
        'completado': 'badge-success',
        'cancelado': 'badge-danger',
    };
    return classes[estado] || 'badge-secondary';
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
    <Head title="Mis Proyectos" />

    <ClienteAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Mis Proyectos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Card -->
                <div class="card mb-8 fade-in">
                    <h3 class="section-title">Bienvenido, {{ cliente.nombre }}</h3>
                    <p style="color: var(--theme-text-secondary)">
                        Aquí puedes ver todos tus proyectos y sus planes de pago asociados.
                    </p>
                </div>

                <!-- Projects Table -->
                <div class="card fade-in" style="animation-delay: 0.1s">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Proyecto</th>
                                    <th>Descripción</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proyecto in proyectos" :key="proyecto.id">
                                    <td>
                                        <div class="font-medium" style="color: var(--theme-text-primary)">
                                            {{ proyecto.nombre }}
                                        </div>
                                    </td>
                                    <td style="color: var(--theme-text-secondary)">
                                        {{ proyecto.descripcion || 'Sin descripción' }}
                                    </td>
                                    <td style="color: var(--theme-text-secondary)">
                                        {{ proyecto.ubicacion || 'No especificada' }}
                                    </td>
                                    <td>
                                        <span :class="['badge', getEstadoBadgeClass(proyecto.estado)]">
                                            {{ getEstadoLabel(proyecto.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <Link
                                            :href="route('cliente.proyectos.show', proyecto.id)"
                                            class="btn btn-primary btn-sm"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Ver Planes de Pago
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="proyectos.length === 0">
                                    <td colspan="5" class="text-center py-12">
                                        <div class="empty-state">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="64"
                                                height="64"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                            >
                                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                                            </svg>
                                            <p class="empty-state-title">No tienes proyectos registrados</p>
                                            <p class="empty-state-text">Contacta a tu vendedor para más información</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </ClienteAuthenticatedLayout>
</template>

<style scoped>
.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.max-w-7xl {
    max-width: 80rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.mb-8 {
    margin-bottom: 2rem;
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

.section-title {
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: var(--spacing-3);
}

.font-medium {
    font-weight: 500;
}

.text-center {
    text-align: center;
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-4);
}

.empty-state svg {
    color: var(--theme-text-tertiary);
    opacity: 0.5;
}

.empty-state-title {
    font-size: var(--font-size-lg);
    font-weight: 500;
    color: var(--theme-text-secondary);
    margin: 0;
}

.empty-state-text {
    font-size: var(--font-size-sm);
    color: var(--theme-text-tertiary);
    margin: 0;
}
</style>
