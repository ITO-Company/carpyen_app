<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    cotizacion: Object,
    proyecto: Object,
    diseñadores: Array
});

const form = useForm({
    url_render: '',
    plano_iluminador: '',
    user_id: '',
    descripcion: '',
    estado: 'pendiente',
    fecha_inicio: '',
    fecha_fin: ''
});

const submit = () => {
    form.post(route('cotizaciones.disenos.store', props.cotizacion.id));
};
</script>

<template>
    <Head title="Nuevo Diseño" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1">
                    <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                        Nuevo Diseño
                    </h2>
                    <p class="text-sm" style="color: var(--theme-text-secondary)">
                        {{ proyecto.nombre }} - Cotización #{{ cotizacion.id }}
                    </p>
                </div>
                <Link :href="route('cotizaciones.disenos.index', cotizacion.id)" class="btn btn-secondary">
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
                            <!-- URL Render -->
                            <div class="form-group">
                                <label for="url_render" class="form-label">
                                    URL del Render
                                </label>
                                <input
                                    id="url_render"
                                    v-model="form.url_render"
                                    type="text"
                                    class="input theme-input"
                                    placeholder="https://..."
                                />
                            </div>

                            <!-- Plano Iluminador -->
                            <div class="form-group">
                                <label for="plano_iluminador" class="form-label">
                                    Plano Iluminador
                                </label>
                                <input
                                    id="plano_iluminador"
                                    v-model="form.plano_iluminador"
                                    type="text"
                                    class="input theme-input"
                                    placeholder="Ruta o referencia"
                                />
                            </div>

                            <!-- Diseñador -->
                            <div class="form-group">
                                <label for="user_id" class="form-label">
                                    Diseñador Asignado
                                </label>
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="input theme-input"
                                >
                                    <option value="">Sin asignar</option>
                                    <option v-for="diseñador in props.diseñadores" :key="diseñador.id" :value="diseñador.id">
                                        {{ diseñador.nombre }}
                                    </option>
                                </select>
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
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="completado">Completado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>

                            <!-- Fecha Inicio -->
                            <div class="form-group">
                                <label for="fecha_inicio" class="form-label">
                                    Fecha Inicio
                                </label>
                                <input
                                    id="fecha_inicio"
                                    v-model="form.fecha_inicio"
                                    type="date"
                                    class="input theme-input"
                                />
                            </div>

                            <!-- Fecha Fin -->
                            <div class="form-group">
                                <label for="fecha_fin" class="form-label">
                                    Fecha Fin
                                </label>
                                <input
                                    id="fecha_fin"
                                    v-model="form.fecha_fin"
                                    type="date"
                                    class="input theme-input"
                                />
                            </div>

                            <!-- Descripción -->
                            <div class="form-group full-width">
                                <label for="descripcion" class="form-label">
                                    Descripción
                                </label>
                                <textarea
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    class="input theme-input"
                                    rows="4"
                                    placeholder="Detalles del diseño"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('cotizaciones.disenos.index', cotizacion.id)" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Diseño</span>
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
