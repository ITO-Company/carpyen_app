<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    producto: Object
});

const form = useForm({
    nombre: props.producto.nombre,
    tipo: props.producto.tipo,
    unidad_medida: props.producto.unidad_medida,
    precio_unitario: props.producto.precio_unitario,
    stock: props.producto.stock
});

const submit = () => {
    form.put(route('productos.update', props.producto.id));
};
</script>

<template>
    <Head title="Editar Producto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Editar Producto
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('productos.index')" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Volver
                    </Link>
                </div>

                <div class="card fade-in">
                    <form @submit.prevent="submit">
                        <div class="form-grid">
                            <!-- Nombre -->
                            <div class="form-group">
                                <label for="nombre" class="form-label">
                                    Nombre del Producto <span class="text-error">*</span>
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="input theme-input"
                                    required
                                    placeholder="Ej: Madera de Roble"
                                />
                                <div v-if="form.errors.nombre" class="form-error">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <!-- Tipo -->
                            <div class="form-group">
                                <label for="tipo" class="form-label">
                                    Tipo
                                </label>
                                <input
                                    id="tipo"
                                    v-model="form.tipo"
                                    type="text"
                                    class="input theme-input"
                                    placeholder="Ej: Materia Prima"
                                />
                            </div>

                            <!-- Unidad de Medida -->
                            <div class="form-group">
                                <label for="unidad_medida" class="form-label">
                                    Unidad de Medida <span class="text-error">*</span>
                                </label>
                                <select
                                    id="unidad_medida"
                                    v-model="form.unidad_medida"
                                    class="input theme-input"
                                    required
                                >
                                    <option value="unidad">Unidad</option>
                                    <option value="metro">Metro</option>
                                    <option value="metro_cuadrado">Metro Cuadrado</option>
                                    <option value="litro">Litro</option>
                                    <option value="kilogramo">Kilogramo</option>
                                    <option value="caja">Caja</option>
                                </select>
                            </div>

                            <!-- Precio Unitario -->
                            <div class="form-group">
                                <label for="precio_unitario" class="form-label">
                                    Precio Unitario (Bs.) <span class="text-error">*</span>
                                </label>
                                <input
                                    id="precio_unitario"
                                    v-model="form.precio_unitario"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="input theme-input"
                                    required
                                    placeholder="0.00"
                                />
                                <div v-if="form.errors.precio_unitario" class="form-error">
                                    {{ form.errors.precio_unitario }}
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="form-group full-width">
                                <label for="stock" class="form-label">
                                    Stock Disponible
                                </label>
                                <input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    class="input theme-input"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('productos.index')" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Actualizando...</span>
                                <span v-else>Actualizar Producto</span>
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
