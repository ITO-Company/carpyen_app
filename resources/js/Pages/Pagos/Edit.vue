<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    pago: Object,
    planesPago: Array
});

const form = useForm({
    plan_pago_id: props.pago.plan_pago_id,
    fecha: props.pago.fecha,
    total: props.pago.total,
    metodo_pago: props.pago.metodo_pago,
    estado: props.pago.estado
});

const submit = () => {
    form.put(route('pagos.update', props.pago.id));
};
</script>

<template>
    <Head title="Editar Pago" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Editar Pago
                </h2>
                <Link :href="route('pagos.index')" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <form @submit.prevent="submit">
                        <div class="form-grid">
                            <!-- Plan de Pago -->
                            <div class="form-group full-width">
                                <label for="plan_pago_id" class="form-label">
                                    Plan de Pago <span class="text-error">*</span>
                                </label>
                                <select
                                    id="plan_pago_id"
                                    v-model="form.plan_pago_id"
                                    class="input theme-input"
                                    required
                                >
                                    <option value="">Seleccione un plan de pago</option>
                                    <option v-for="plan in planesPago" :key="plan.id" :value="plan.id">
                                        {{ plan.proyecto?.nombre || 'Sin proyecto' }} - Bs. {{ plan.deuda_total }}
                                    </option>
                                </select>
                                <div v-if="form.errors.plan_pago_id" class="form-error">
                                    {{ form.errors.plan_pago_id }}
                                </div>
                            </div>

                            <!-- Fecha -->
                            <div class="form-group">
                                <label for="fecha" class="form-label">
                                    Fecha de Pago <span class="text-error">*</span>
                                </label>
                                <input
                                    id="fecha"
                                    v-model="form.fecha"
                                    type="date"
                                    class="input theme-input"
                                    required
                                />
                                <div v-if="form.errors.fecha" class="form-error">
                                    {{ form.errors.fecha }}
                                </div>
                            </div>

                            <!-- Monto -->
                            <div class="form-group">
                                <label for="total" class="form-label">
                                    Monto (Bs.) <span class="text-error">*</span>
                                </label>
                                <input
                                    id="total"
                                    v-model="form.total"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="input theme-input"
                                    required
                                    placeholder="0.00"
                                />
                                <div v-if="form.errors.total" class="form-error">
                                    {{ form.errors.total }}
                                </div>
                            </div>

                            <!-- Método de Pago -->
                            <div class="form-group">
                                <label for="metodo_pago" class="form-label">
                                    Método de Pago <span class="text-error">*</span>
                                </label>
                                <select
                                    id="metodo_pago"
                                    v-model="form.metodo_pago"
                                    class="input theme-input"
                                    required
                                >
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                    <option value="qr">Código QR</option>
                                </select>
                                <div v-if="form.errors.metodo_pago" class="form-error">
                                    {{ form.errors.metodo_pago }}
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="form-group">
                                <label for="estado" class="form-label">
                                    Estado <span class="text-error">*</span>
                                </label>
                                <select
                                    id="estado"
                                    v-model="form.estado"
                                    class="input theme-input"
                                    required
                                >
                                    <option value="pendiente">Pendiente</option>
                                    <option value="completado">Completado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                                <div v-if="form.errors.estado" class="form-error">
                                    {{ form.errors.estado }}
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('pagos.index')" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Actualizando...</span>
                                <span v-else>Actualizar Pago</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-6);
    margin-bottom: var(--spacing-6);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-2);
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--theme-text-primary);
}

.form-error {
    font-size: var(--font-size-sm);
    color: var(--theme-error);
    margin-top: var(--spacing-1);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-3);
    padding-top: var(--spacing-6);
    border-top: 1px solid var(--theme-border);
}

/* Adaptación del input al tema */
.theme-input {
    background-color: var(--theme-bg-primary) !important;
    color: var(--theme-text-primary) !important;
    border: 1px solid var(--theme-border) !important;
}

.theme-input::placeholder {
    color: var(--theme-text-secondary) !important;
    opacity: 0.7;
}

.theme-input:focus {
    border-color: var(--theme-primary) !important;
    outline: none;
    box-shadow: 0 0 0 3px var(--theme-primary-alpha) !important;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
