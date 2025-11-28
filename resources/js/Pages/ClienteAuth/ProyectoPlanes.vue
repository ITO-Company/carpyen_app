<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClienteAuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';

const props = defineProps({
    cliente: {
        type: Object,
        required: true,
    },
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
        'activo': 'badge-info',
        'completado': 'badge-success',
        'mora': 'badge-danger',
    };
    return classes[estado] || 'badge-secondary';
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

    <ClienteAuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('cliente.dashboard')" class="back-btn">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Planes de Pago - {{ proyecto.nombre }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Project Info Card -->
                <div class="card mb-8 fade-in">
                    <h3 class="section-title">{{ proyecto.nombre }}</h3>
                    <div class="info-grid">
                        <div>
                            <p class="info-label">Descripción</p>
                            <p class="info-value">{{ proyecto.descripcion || 'Sin descripción' }}</p>
                        </div>
                        <div>
                            <p class="info-label">Ubicación</p>
                            <p class="info-value">{{ proyecto.ubicacion || 'No especificada' }}</p>
                        </div>
                        <div>
                            <p class="info-label">Estado</p>
                            <p class="info-value capitalize">{{ proyecto.estado.replace('_', ' ') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Plans Table -->
                <div class="card fade-in" style="animation-delay: 0.1s">
                    <h3 class="section-title mb-6">Planes de Pago del Proyecto</h3>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Deuda Total</th>
                                    <th>Pagado</th>
                                    <th>Saldo Pendiente</th>
                                    <th>N° Pagos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="plan in planPagos" :key="plan.id">
                                    <td>
                                        <div class="font-semibold" style="color: var(--theme-text-primary)">
                                            {{ formatCurrency(plan.deuda_total) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium" style="color: var(--theme-success)">
                                            {{ formatCurrency(plan.pagado_total) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium" style="color: var(--theme-warning)">
                                            {{ formatCurrency(plan.deuda_total - plan.pagado_total) }}
                                        </div>
                                    </td>
                                    <td style="color: var(--theme-text-secondary)">
                                        {{ plan.numero_pagos }} / {{ plan.numero_deudas }}
                                    </td>
                                    <td>
                                        <span :class="['badge', getEstadoBadgeClass(plan.estado)]">
                                            {{ getEstadoLabel(plan.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <Link
                                            :href="route('cliente.planes.show', plan.id)"
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
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver Pagos
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="planPagos.length === 0">
                                    <td colspan="6" class="text-center py-12">
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
                                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="empty-state-title">No hay planes de pago registrados</p>
                                            <p class="empty-state-text">Este proyecto aún no tiene planes de pago asociados</p>
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

.mb-6 {
    margin-bottom: 1.5rem;
}

.font-semibold {
    font-weight: 600;
}

.font-medium {
    font-weight: 500;
}

.text-xl {
    font-size: var(--font-size-xl);
}

.leading-tight {
    line-height: 1.25;
}

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.gap-4 {
    gap: 1rem;
}

.back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: var(--border-radius-md);
    background: none;
    border: 1px solid var(--theme-border);
    color: var(--theme-text-secondary);
    cursor: pointer;
    transition: all var(--transition-fast);
    text-decoration: none;
}

.back-btn:hover {
    background-color: var(--theme-bg-secondary);
    color: var(--theme-primary);
    border-color: var(--theme-primary);
}

.section-title {
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: var(--spacing-3);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-6);
}

.info-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-1);
}

.info-value {
    font-size: var(--font-size-base);
    font-weight: 500;
    color: var(--theme-text-primary);
}

.capitalize {
    text-transform: capitalize;
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
