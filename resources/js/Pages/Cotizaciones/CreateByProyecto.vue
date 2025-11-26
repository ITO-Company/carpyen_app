<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    proyecto: Object
});

const form = useForm({
    tipo_metro: 'lineal',
    costo_metro: '',
    cantidad_metro: '',
    costo_mueble: '',
    mueble_numero: '',
    total: '',
    estado: 'pendiente',
    comentario: ''
});

// Calcular total automáticamente
const calcularTotal = () => {
    const costoMetros = (parseFloat(form.costo_metro) || 0) * (parseInt(form.cantidad_metro) || 0);
    const costoMuebles = (parseFloat(form.costo_mueble) || 0) * (parseInt(form.mueble_numero) || 0);
    form.total = (costoMetros + costoMuebles).toFixed(2);
};

const submit = () => {
    form.post(route('proyectos.cotizaciones.store', props.proyecto.id));
};
</script>

<template>
    <Head title="Nueva Cotización" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1">
                    <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                        Nueva Cotización
                    </h2>
                    <p class="text-sm" style="color: var(--theme-text-secondary)">
                        {{ proyecto.nombre }}
                    </p>
                </div>
                <Link :href="route('proyectos.cotizaciones.index', proyecto.id)" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <form @submit.prevent="submit">
                        <div class="form-grid">
                            <!-- Tipo de Metro -->
                            <div class="form-group">
                                <label for="tipo_metro" class="form-label">
                                    Tipo de Metro <span class="text-error">*</span>
                                </label>
                                <select
                                    id="tipo_metro"
                                    v-model="form.tipo_metro"
                                    class="input theme-input"
                                    required
                                >
                                    <option value="lineal">Metro Lineal</option>
                                    <option value="cuadrado">Metro Cuadrado</option>
                                </select>
                            </div>

                            <!-- Costo por Metro -->
                            <div class="form-group">
                                <label for="costo_metro" class="form-label">
                                    Costo por Metro (Bs.)
                                </label>
                                <input
                                    id="costo_metro"
                                    v-model="form.costo_metro"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="input theme-input"
                                    placeholder="0.00"
                                    @input="calcularTotal"
                                />
                            </div>

                            <!-- Cantidad de Metros -->
                            <div class="form-group">
                                <label for="cantidad_metro" class="form-label">
                                    Cantidad de Metros
                                </label>
                                <input
                                    id="cantidad_metro"
                                    v-model="form.cantidad_metro"
                                    type="number"
                                    min="0"
                                    class="input theme-input"
                                    placeholder="0"
                                    @input="calcularTotal"
                                />
                            </div>

                            <!-- Costo por Mueble -->
                            <div class="form-group">
                                <label for="costo_mueble" class="form-label">
                                    Costo por Mueble (Bs.)
                                </label>
                                <input
                                    id="costo_mueble"
                                    v-model="form.costo_mueble"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="input theme-input"
                                    placeholder="0.00"
                                    @input="calcularTotal"
                                />
                            </div>

                            <!-- Número de Muebles -->
                            <div class="form-group">
                                <label for="mueble_numero" class="form-label">
                                    Número de Muebles
                                </label>
                                <input
                                    id="mueble_numero"
                                    v-model="form.mueble_numero"
                                    type="number"
                                    min="0"
                                    class="input theme-input"
                                    placeholder="0"
                                    @input="calcularTotal"
                                />
                            </div>

                            <!-- Total -->
                            <div class="form-group">
                                <label for="total" class="form-label">
                                    Total (Bs.) <span class="text-error">*</span>
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

                            <!-- Comentario -->
                            <div class="form-group full-width">
                                <label for="comentario" class="form-label">
                                    Comentario
                                </label>
                                <textarea
                                    id="comentario"
                                    v-model="form.comentario"
                                    class="input theme-input"
                                    rows="4"
                                    placeholder="Comentarios adicionales sobre la cotización"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('proyectos.cotizaciones.index', proyecto.id)" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Cotización</span>
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

textarea.input {
    resize: vertical;
    min-height: 100px;
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

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
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

.max-w-4xl {
    max-width: 56rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
