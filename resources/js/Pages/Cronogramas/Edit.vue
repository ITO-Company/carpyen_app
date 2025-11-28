<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    cronograma: Object,
    proyectos: Array
});

const form = useForm({
    proyecto_id: props.cronograma.proyecto_id,
    fecha_inicio: props.cronograma.fecha_inicio ? new Date(props.cronograma.fecha_inicio).toISOString().split('T')[0] : '',
    dias_estimados: props.cronograma.dias_estimados,
    estado: props.cronograma.estado
});

const fechaEstimada = computed(() => {
    if (!form.fecha_inicio || !form.dias_estimados) return '';
    const fecha = new Date(form.fecha_inicio);
    fecha.setDate(fecha.getDate() + parseInt(form.dias_estimados));
    return fecha.toISOString().split('T')[0];
});

const submit = () => {
    form.put(route('cronogramas.update', props.cronograma.id));
};

const getEstadoLabel = (estado) => {
    const labels = {
        pendiente: 'Pendiente',
        en_curso: 'En Curso',
        completado: 'Completado',
        atrasado: 'Atrasado'
    };
    return labels[estado] || estado;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};

const formatDateDisplay = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};
</script>

<template>
    <Head title="Editar Cronograma" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Editar Cronograma
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <div class="flex justify-between items-center mb-6 pb-6 border-b" style="border-color: var(--theme-border);">
                        <h3 class="text-lg font-semibold" style="color: var(--theme-text-primary)">Editar Cronograma</h3>
                        <Link :href="route('cronogramas.index')" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Volver
                        </Link>
                    </div>

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

                            <!-- Fecha Inicio -->
                            <div class="form-group">
                                <label for="fecha_inicio" class="form-label">
                                    Fecha de Inicio <span class="text-error">*</span>
                                </label>
                                <input
                                    id="fecha_inicio"
                                    v-model="form.fecha_inicio"
                                    type="date"
                                    class="input theme-input"
                                    required
                                />
                                <div v-if="form.errors.fecha_inicio" class="form-error">
                                    {{ form.errors.fecha_inicio }}
                                </div>
                            </div>

                            <!-- Días Estimados -->
                            <div class="form-group">
                                <label for="dias_estimados" class="form-label">
                                    Días Estimados <span class="text-error">*</span>
                                </label>
                                <input
                                    id="dias_estimados"
                                    v-model.number="form.dias_estimados"
                                    type="number"
                                    min="1"
                                    class="input theme-input"
                                    required
                                    placeholder="Ej: 30"
                                />
                                <div v-if="form.errors.dias_estimados" class="form-error">
                                    {{ form.errors.dias_estimados }}
                                </div>
                            </div>

                            <!-- Fecha Estimada (Read-only) -->
                            <div class="form-group">
                                <label for="fecha_estimada" class="form-label">
                                    Fecha Estimada
                                </label>
                                <div class="input theme-input read-only">
                                    {{ formatDateDisplay(fechaEstimada) }}
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
                                    <option value="en_curso">En Curso</option>
                                    <option value="completado">Completado</option>
                                    <option value="atrasado">Atrasado</option>
                                </select>
                                <div v-if="form.errors.estado" class="form-error">
                                    {{ form.errors.estado }}
                                </div>
                            </div>

                            <!-- Usuario (Read-only) -->
                            <div class="form-group">
                                <label class="form-label">Usuario</label>
                                <div class="input theme-input read-only" style="display: flex; align-items: center;">
                                    {{ cronograma.usuario.nombre }}
                                </div>
                            </div>

                            <!-- Fecha Fin (Read-only) -->
                            <div class="form-group">
                                <label class="form-label">Fecha de Finalización</label>
                                <div class="input theme-input read-only" style="display: flex; align-items: center;">
                                    {{ formatDate(cronograma.fecha_fin) }}
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="form-group full-width">
                                <div class="alert alert-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <span>La fecha de finalización se asignará automáticamente cuando cambies el estado a 'Completado'</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('cronogramas.index')" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Cambios</span>
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
    min-height: 80px;
}

.read-only {
    background-color: var(--theme-bg-secondary) !important;
    cursor: not-allowed;
    padding: var(--spacing-2);
    border-radius: 4px;
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

.alert {
    display: flex;
    gap: var(--spacing-3);
    align-items: flex-start;
    padding: var(--spacing-3);
    border-radius: 6px;
    font-size: var(--font-size-sm);
}

.alert-info {
    background-color: rgba(59, 130, 246, 0.1);
    color: #1e40af;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
