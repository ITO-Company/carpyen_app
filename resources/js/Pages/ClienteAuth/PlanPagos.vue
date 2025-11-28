<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClienteAuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';

const props = defineProps({
    cliente: {
        type: Object,
        required: true,
    },
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
        'pendiente': 'badge-warning',
        'completado': 'badge-success',
        'fallido': 'badge-danger',
    };
    return classes[estado] || 'badge-secondary';
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

    <ClienteAuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('cliente.proyectos.show', proyecto.id)" class="back-btn">
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
                    Detalle de Pagos - {{ proyecto.nombre }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Payment Plan Summary -->
                <div class="card mb-8 fade-in">
                    <h3 class="section-title mb-6">Resumen del Plan de Pago</h3>
                    
                    <div class="stats-grid mb-6">
                        <div class="stat-box">
                            <p class="stat-label">Deuda Total</p>
                            <p class="stat-value">{{ formatCurrency(planPago.deuda_total) }}</p>
                        </div>
                        <div class="stat-box stat-success">
                            <p class="stat-label">Total Pagado</p>
                            <p class="stat-value">{{ formatCurrency(planPago.pagado_total) }}</p>
                        </div>
                        <div class="stat-box stat-warning">
                            <p class="stat-label">Saldo Pendiente</p>
                            <p class="stat-value">{{ formatCurrency(saldoPendiente) }}</p>
                        </div>
                        <div class="stat-box stat-info">
                            <p class="stat-label">Progreso</p>
                            <p class="stat-value">{{ porcentajePagado.toFixed(1) }}%</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress-bar-container">
                        <div class="progress-bar" :style="{ width: `${porcentajePagado}%` }"></div>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="card fade-in" style="animation-delay: 0.1s">
                    <h3 class="section-title mb-6">Historial de Pagos</h3>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método de Pago</th>
                                    <th>Estado</th>
                                    <th>ID Transacción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pago in pagos" :key="pago.id">
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                style="color: var(--theme-text-tertiary)"
                                            >
                                                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span style="color: var(--theme-text-primary)">{{ formatDate(pago.fecha) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-semibold" style="color: var(--theme-text-primary)">
                                            {{ formatCurrency(pago.total) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                style="color: var(--theme-text-tertiary)"
                                            >
                                                <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            <span style="color: var(--theme-text-secondary)">{{ pago.metodo_pago || 'No especificado' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span :class="['badge', getEstadoBadgeClass(pago.estado)]">
                                            <span
                                                class="badge-dot"
                                                :class="{
                                                    'badge-dot-success': pago.estado === 'completado',
                                                    'badge-dot-warning': pago.estado === 'pendiente',
                                                    'badge-dot-danger': pago.estado === 'fallido',
                                                }"
                                            ></span>
                                            {{ getEstadoLabel(pago.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="transaction-id">{{ pago.transaccion_id || 'N/A' }}</code>
                                    </td>
                                </tr>
                                <tr v-if="pagos.length === 0">
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
                                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            <p class="empty-state-title">No hay pagos registrados</p>
                                            <p class="empty-state-text">Este plan de pago aún no tiene pagos asociados</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Back to Dashboard Button -->
                <div class="mt-8 flex justify-center">
                    <Link :href="route('cliente.dashboard')" class="btn btn-secondary">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Volver al Dashboard
                    </Link>
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

.mt-8 {
    margin-top: 2rem;
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

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.justify-center {
    justify-content: center;
}

.gap-2 {
    gap: 0.5rem;
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-4);
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
    height: 1rem;
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

.transaction-id {
    font-family: 'Courier New', monospace;
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    background: var(--theme-bg-secondary);
    padding: 0.25rem 0.5rem;
    border-radius: var(--border-radius-sm);
}

.badge-dot {
    display: inline-block;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.badge-dot-success {
    background-color: var(--theme-success);
}

.badge-dot-warning {
    background-color: var(--theme-warning);
}

.badge-dot-danger {
    background-color: var(--theme-danger);
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
