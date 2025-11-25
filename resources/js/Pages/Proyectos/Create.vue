<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    clientes: Array,
    vendedores: Array
});

const form = useForm({
    nombre: '',
    descripcion: '',
    ubicacion: '',
    estado: 'pendiente',
    cliente_id: '',
    user_id: ''
});

const submit = () => {
    form.post(route('proyectos.store'));
};
</script>

<template>
    <Head title="Nuevo Proyecto" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Nuevo Proyecto
                </h2>
                <Link :href="route('proyectos.index')" class="btn btn-secondary">
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
                            <!-- Nombre -->
                            <div class="form-group">
                                <label for="nombre" class="form-label">
                                    Nombre del Proyecto <span class="text-error">*</span>
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="input theme-input"
                                    required
                                    placeholder="Ej: Proyecto Residencial"
                                />
                                <div v-if="form.errors.nombre" class="form-error">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <!-- Cliente -->
                            <div class="form-group">
                                <label for="cliente_id" class="form-label">
                                    Cliente <span class="text-error">*</span>
                                </label>
                                <select
                                    id="cliente_id"
                                    v-model="form.cliente_id"
                                    class="input theme-input"
                                    required
                                >
                                    <option value="">Seleccione un cliente</option>
                                    <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                        {{ cliente.nombre }}
                                    </option>
                                </select>
                                <div v-if="form.errors.cliente_id" class="form-error">
                                    {{ form.errors.cliente_id }}
                                </div>
                            </div>

                            <!-- Vendedor -->
                            <div class="form-group">
                                <label for="user_id" class="form-label">
                                    Vendedor Asignado
                                </label>
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="input theme-input"
                                >
                                    <option value="">Sin asignar</option>
                                    <option v-for="vendedor in vendedores" :key="vendedor.id" :value="vendedor.id">
                                        {{ vendedor.name }}
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
                                    <option value="cancelado">Cancelado</option>
                                </select>
                                <div v-if="form.errors.estado" class="form-error">
                                    {{ form.errors.estado }}
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="form-group full-width">
                                <label for="ubicacion" class="form-label">
                                    Ubicación
                                </label>
                                <input
                                    id="ubicacion"
                                    v-model="form.ubicacion"
                                    type="text"
                                    class="input theme-input"
                                    placeholder="Dirección del proyecto"
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
                                    rows="3"
                                    placeholder="Descripción detallada del proyecto"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('proyectos.index')" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Proyecto</span>
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
