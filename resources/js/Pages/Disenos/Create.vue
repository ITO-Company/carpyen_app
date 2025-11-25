<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    proyectos: Array
});

const form = useForm({
    proyecto_id: '',
    descripcion: '',
    estado: 'pendiente',
    fecha_inicio: '',
    fecha_fin: ''
});

const submit = () => {
    form.post(route('disenos.store'));
};
</script>

<template>
    <Head title="Nuevo Diseño" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Nuevo Diseño
                </h2>
                <Link :href="route('disenos.index')" class="btn btn-secondary">
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
                            <!-- Proyecto -->
                            <div class="form-group full-width">
                                <label for="proyecto_id" class="form-label">
                                    Proyecto <span class="text-error">*</span>
                                </label>
                                <select
                                    id="proyecto_id"
                                    v-model="form.proyecto_id"
                                    class="input theme-input"
                                    required
                                >
                                    <option value="">Seleccione un proyecto</option>
                                    <option v-for="proyecto in proyectos" :key="proyecto.id" :value="proyecto.id">
                                        {{ proyecto.nombre }}
                                    </option>
                                </select>
                                <div v-if="form.errors.proyecto_id" class="form-error">
                                    {{ form.errors.proyecto_id }}
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
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="completado">Completado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                                <div v-if="form.errors.estado" class="form-error">
                                    {{ form.errors.estado }}
                                </div>
                            </div>

                            <!-- Fecha Inicio -->
                            <div class="form-group">
                                <label for="fecha_inicio" class="form-label">
                                    Fecha de Inicio
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
                                    Fecha de Fin
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
                                    placeholder="Descripción del diseño"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('disenos.index')" class="btn btn-secondary">
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
