<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClienteAuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';
import { ref } from 'vue';

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

// Modal state
const mostrarModalPago = ref(false);
const pagoSeleccionado = ref(null);
const qrImage = ref(null);
const transaccionId = ref(null);
const generandoQR = ref(false);
const errorQR = ref(null);

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

// Payment functions
const abrirModalPago = (pago) => {
    pagoSeleccionado.value = pago;
    qrImage.value = null;
    transaccionId.value = null;
    errorQR.value = null;
    mostrarModalPago.value = true;
};

const cerrarModalPago = () => {
    mostrarModalPago.value = false;
    pagoSeleccionado.value = null;
    qrImage.value = null;
    transaccionId.value = null;
    errorQR.value = null;
};

const generarQRPago = async () => {
    if (!pagoSeleccionado.value || !props.planPago) return;

    generandoQR.value = true;
    errorQR.value = null;

    console.log('📱 Generando QR para pago:', pagoSeleccionado.value.id);

    try {
        const datosEnvio = {
            plan_pago_id: props.planPago.id,
            pago_id: pagoSeleccionado.value.id,
        };

        console.log('📤 Enviando solicitud:', datosEnvio);

        const response = await window.axios.post(
            '/cliente/pagos/generar-qr',
            datosEnvio
        );

        console.log('✅ Respuesta recibida:', response.data);

        if (response.data.success) {
            let qrDataUrl = response.data.qrBase64;
            if (!qrDataUrl.startsWith('data:')) {
                qrDataUrl = 'data:image/png;base64,' + qrDataUrl;
            }

            qrImage.value = qrDataUrl;
            transaccionId.value = response.data.transactionId;
            console.log('✨ QR generado correctamente');
        } else {
            const mensaje = response.data.message || 'Error al generar código QR';
            console.warn('⚠️ Error:', mensaje);
            errorQR.value = mensaje;
        }
    } catch (error) {
        console.error('❌ Error al generar QR:', error);
        
        if (error.response) {
            if (error.response.status === 400) {
                errorQR.value = 'Datos inválidos: verifica que el pago sea válido';
            } else if (error.response.status === 422) {
                errorQR.value = 'Validación fallida';
            } else if (error.response.status === 500) {
                errorQR.value = error.response.data.message || 'Error al generar QR';
            } else {
                errorQR.value = 'Error al conectar con el servidor';
            }
        } else if (error.request) {
            errorQR.value = 'Error de conectividad con el servidor';
        } else {
            errorQR.value = error.message;
        }
    } finally {
        generandoQR.value = false;
    }
};
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
                                    <th>Acciones</th>
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
                                    <td>
                                        <button
                                            v-if="pago.estado === 'pendiente'"
                                            @click="abrirModalPago(pago)"
                                            class="btn btn-primary btn-sm"
                                        >
                                            Pagar
                                        </button>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                </tr>
                                <tr v-if="pagos.length === 0">
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

        <!-- Payment Modal -->
        <div v-if="mostrarModalPago" class="modal-overlay" @click="cerrarModalPago">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Realizar Pago</h3>
                    <button @click="cerrarModalPago" class="modal-close">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    <div v-if="!qrImage && !errorQR" class="payment-info">
                        <div class="payment-detail">
                            <span class="payment-label">Monto a pagar:</span>
                            <span class="payment-value">{{ formatCurrency(pagoSeleccionado?.total || 0) }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-label">Fecha de pago:</span>
                            <span class="payment-value">{{ formatDate(pagoSeleccionado?.fecha) }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-label">Proyecto:</span>
                            <span class="payment-value">{{ proyecto.nombre }}</span>
                        </div>

                        <button
                            @click="generarQRPago"
                            :disabled="generandoQR"
                            class="btn btn-primary btn-block mt-6"
                        >
                            <svg
                                v-if="!generandoQR"
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span v-if="generandoQR">Generando QR...</span>
                            <span v-else>Generar Código QR</span>
                        </button>
                    </div>

                    <div v-if="qrImage" class="qr-container">
                        <div class="qr-success-message">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <p>¡QR Generado Exitosamente!</p>
                        </div>
                        
                        <div class="qr-image-wrapper">
                            <img :src="qrImage" alt="Código QR de Pago" class="qr-image" />
                        </div>

                        <div class="qr-instructions">
                            <p class="qr-instruction-title">Instrucciones:</p>
                            <ol class="qr-instruction-list">
                                <li>Abre tu aplicación bancaria</li>
                                <li>Escanea el código QR</li>
                                <li>Confirma el pago de {{ formatCurrency(pagoSeleccionado?.total || 0) }}</li>
                                <li>Guarda el comprobante de transacción</li>
                            </ol>
                        </div>

                        <div v-if="transaccionId" class="transaction-info">
                            <p class="transaction-label">ID de Transacción:</p>
                            <code class="transaction-code">{{ transaccionId }}</code>
                        </div>
                    </div>

                    <div v-if="errorQR" class="error-container">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="48"
                            height="48"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p class="error-message">{{ errorQR }}</p>
                        <button @click="generarQRPago" class="btn btn-secondary mt-4">
                            Intentar Nuevamente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ClienteAuthenticatedLayout>
</template>

<style scoped>
/* Existing styles */
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

.mt-6 {
    margin-top: 1.5rem;
}

.mt-4 {
    margin-top: 1rem;
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

.text-muted {
    color: var(--theme-text-tertiary);
    font-size: var(--font-size-sm);
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

/* Modal styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.75);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-content {
    background: var(--theme-bg-primary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-xl);
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-2xl);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid var(--theme-border);
}

.modal-title {
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin: 0;
}

.modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: var(--border-radius-md);
    background: none;
    border: none;
    color: var(--theme-text-secondary);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.modal-close:hover {
    background: var(--theme-bg-secondary);
    color: var(--theme-text-primary);
}

.modal-body {
    padding: 1.5rem;
}

.payment-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.payment-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: var(--theme-bg-secondary);
    border-radius: var(--border-radius-md);
}

.payment-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
}

.payment-value {
    font-size: var(--font-size-base);
    font-weight: 600;
    color: var(--theme-text-primary);
}

.btn-block {
    width: 100%;
}

.qr-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.qr-success-message {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: var(--theme-success);
}

.qr-success-message p {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin: 0;
}

.qr-image-wrapper {
    padding: 1rem;
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
}

.qr-image {
    display: block;
    width: 250px;
    height: 250px;
}

.qr-instructions {
    width: 100%;
    padding: 1rem;
    background: var(--theme-bg-secondary);
    border-radius: var(--border-radius-md);
}

.qr-instruction-title {
    font-size: var(--font-size-base);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: 0.5rem;
}

.qr-instruction-list {
    margin: 0;
    padding-left: 1.5rem;
    color: var(--theme-text-secondary);
    font-size: var(--font-size-sm);
}

.qr-instruction-list li {
    margin-bottom: 0.25rem;
}

.transaction-info {
    text-align: center;
}

.transaction-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: 0.5rem;
}

.transaction-code {
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-size: var(--font-size-sm);
    color: var(--theme-primary);
    background: var(--theme-bg-secondary);
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius-md);
    border: 1px solid var(--theme-border);
}

.error-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
    color: var(--theme-danger);
}

.error-message {
    font-size: var(--font-size-base);
    text-align: center;
    color: var(--theme-text-primary);
}
</style>
