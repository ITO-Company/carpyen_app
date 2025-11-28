<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClienteAuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';

const props = defineProps({
    cliente: {
        type: Object,
        required: true,
    },
    planesPago: {
        type: Array,
        required: true,
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value);
};

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
</script>

<template>
    <Head title="Mis Planes de Pago" />

    <ClienteAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Mis Planes de Pago
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Lista de Planes de Pago -->
                <template v-if="planesPago && planesPago.length > 0">
                    <div class="grid gap-6">
                        <div
                            v-for="plan in planesPago"
                            :key="plan.id"
                            class="card fade-in"
                        >
                            <div class="card-body p-6">
                                <!-- Encabezado -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold" style="color: var(--theme-text-primary)">
                                            {{ plan.proyecto.nombre }}
                                        </h3>
                                        <p style="color: var(--theme-text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
                                            {{ plan.proyecto.descripcion || 'Sin descripción' }}
                                        </p>
                                    </div>
                                    <span :class="['badge', getEstadoBadgeClass(plan.estado)]">
                                        {{ getEstadoLabel(plan.estado) }}
                                    </span>
                                </div>

                                <!-- Información Financiera -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                    <div class="stat-box">
                                        <p class="stat-label">Deuda Total</p>
                                        <p class="stat-value">
                                            {{ formatCurrency(plan.deuda_total) }}
                                        </p>
                                    </div>
                                    <div class="stat-box stat-success">
                                        <p class="stat-label">Pagado</p>
                                        <p class="stat-value">
                                            {{ formatCurrency(plan.pagado_total) }}
                                        </p>
                                    </div>
                                    <div class="stat-box stat-warning">
                                        <p class="stat-label">Saldo</p>
                                        <p class="stat-value">
                                            {{ formatCurrency(plan.deuda_total - plan.pagado_total) }}
                                        </p>
                                    </div>
                                    <div class="stat-box stat-info">
                                        <p class="stat-label">Progreso</p>
                                        <p class="stat-value">
                                            {{ ((plan.pagado_total / plan.deuda_total) * 100).toFixed(0) }}%
                                        </p>
                                    </div>
                                </div>

                                <!-- Barra de Progreso -->
                                <div class="mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-sm font-semibold" style="color: var(--theme-text-primary)">
                                            Progreso de Pago
                                        </p>
                                        <p class="text-sm" style="color: var(--theme-text-secondary)">
                                            {{ ((plan.pagado_total / plan.deuda_total) * 100).toFixed(0) }}%
                                        </p>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div
                                            class="progress-bar"
                                            :style="{
                                                width: ((plan.pagado_total / plan.deuda_total) * 100) + '%',
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex gap-2 flex-wrap">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Sin planes de pago -->
                <div v-else class="card text-center py-12">
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
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        <p class="empty-state-title">No tienes planes de pago registrados</p>
                        <p class="empty-state-text">Contacta a tu vendedor para más información</p>
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

.mb-4 {
    margin-bottom: 1rem;
}

.mb-6 {
    margin-bottom: 1.5rem;
}

.font-semibold {
    font-weight: 600;
}

.font-bold {
    font-weight: 700;
}

.text-xl {
    font-size: var(--font-size-xl);
}

.text-sm {
    font-size: var(--font-size-sm);
}

.leading-tight {
    line-height: 1.25;
}

.grid {
    display: grid;
}

.gap-6 {
    gap: 1.5rem;
}

.gap-4 {
    gap: 1rem;
}

.gap-2 {
    gap: 0.5rem;
}

.grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.flex {
    display: flex;
}

.flex-wrap {
    flex-wrap: wrap;
}

.justify-between {
    justify-content: space-between;
}

.items-start {
    align-items: flex-start;
}

.items-center {
    align-items: center;
}

.text-center {
    text-align: center;
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.stat-box {
    padding: var(--spacing-4);
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-md);
}

.stat-box.stat-success {
    background: rgba(34, 197, 94, 0.1);
    border-color: rgba(34, 197, 94, 0.3);
}

.stat-box.stat-warning {
    background: rgba(251, 191, 36, 0.1);
    border-color: rgba(251, 191, 36, 0.3);
}

.stat-box.stat-info {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.stat-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-2);
}

.stat-value {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--theme-text-primary);
}

.progress-bar-container {
    width: 100%;
    height: 0.5rem;
    background: var(--theme-bg-secondary);
    border-radius: var(--border-radius-full);
    overflow: hidden;
    border: 1px solid var(--theme-border);
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--theme-success), #34d399);
    transition: width 0.5s ease;
    border-radius: var(--border-radius-full);
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

@media (min-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    
    .md\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}
</style>
