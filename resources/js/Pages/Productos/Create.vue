<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    nombre: '',
    tipo: '',
    unidad_medida: 'unidad',
    precio_unitario: 0,
    stock: 0
});

const submit = () => {
    form.post(route('productos.store'));
};
</script>

<template>
    <Head title="Nuevo Producto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Nuevo Producto
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
                            <div class="form-group full-width">
                                <label for="nombre" class="form-label">
                                    Nombre del Producto <span class="text-error">*</span>
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="input"
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
                                    class="input"
                                    placeholder="Ej: Materia Prima"
                                />
                                <div v-if="form.errors.tipo" class="form-error">
                                    {{ form.errors.tipo }}
                                </div>
                            </div>

                            <!-- Unidad de Medida -->
                            <div class="form-group">
                                <label for="unidad_medida" class="form-label">
                                    Unidad de Medida <span class="text-error">*</span>
                                </label>
                                <select
                                    id="unidad_medida"
                                    v-model="form.unidad_medida"
                                    class="input"
                                    required
                                >
                                    <option value="unidad">Unidad</option>
                                    <option value="metro">Metro</option>
                                    <option value="metro_cuadrado">Metro Cuadrado</option>
                                    <option value="kilogramo">Kilogramo</option>
                                    <option value="litro">Litro</option>
                                    <option value="caja">Caja</option>
                                </select>
                                <div v-if="form.errors.unidad_medida" class="form-error">
                                    {{ form.errors.unidad_medida }}
                                </div>
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
                                    class="input"
                                    required
                                    placeholder="0.00"
                                />
                                <div v-if="form.errors.precio_unitario" class="form-error">
                                    {{ form.errors.precio_unitario }}
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="form-group">
                                <label for="stock" class="form-label">
                                    Stock Inicial <span class="text-error">*</span>
                                </label>
                                <input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    class="input"
                                    required
                                    placeholder="0"
                                />
                                <div v-if="form.errors.stock" class="form-error">
                                    {{ form.errors.stock }}
                                </div>
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
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Producto</span>
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

.flex {
    display: flex;
}

.justify-between {
    justify-content: space-between;
}

.items-center {
    align-items: center;
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.max-w-3xl {
    max-width: 48rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
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

.btn svg {
    margin-right: var(--spacing-2);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
