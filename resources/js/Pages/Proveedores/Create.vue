<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    nombre: '',
    contacto: '',
    telefono: '',
    email: '',
    ubicacion: ''
});

const submit = () => {
    form.post(route('proveedores.store'));
};
</script>

<template>
    <Head title="Crear Proveedor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Nuevo Proveedor
                </h2>
                <p class="text-sm" style="color: var(--theme-text-secondary)">
                    Registra un nuevo proveedor en el sistema
                </p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <form @submit.prevent="submit">
                        <div class="form-grid">
                            <!-- Nombre -->
                            <div class="form-group full-width">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="text-error">*</span>
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="input theme-input"
                                    placeholder="Nombre del proveedor"
                                    required
                                />
                                <p v-if="form.errors.nombre" class="form-error">{{ form.errors.nombre }}</p>
                            </div>

                            <!-- Contacto -->
                            <div class="form-group">
                                <label for="contacto" class="form-label">
                                    Contacto
                                </label>
                                <input
                                    id="contacto"
                                    v-model="form.contacto"
                                    type="text"
                                    class="input theme-input"
                                    placeholder="Nombre del contacto"
                                />
                                <p v-if="form.errors.contacto" class="form-error">{{ form.errors.contacto }}</p>
                            </div>

                            <!-- Teléfono -->
                            <div class="form-group">
                                <label for="telefono" class="form-label">
                                    Teléfono
                                </label>
                                <input
                                    id="telefono"
                                    v-model="form.telefono"
                                    type="tel"
                                    class="input theme-input"
                                    placeholder="+591 ..."
                                />
                                <p v-if="form.errors.telefono" class="form-error">{{ form.errors.telefono }}</p>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    Email
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="input theme-input"
                                    placeholder="correo@proveedor.com"
                                />
                                <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
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
                                    placeholder="Ciudad, País"
                                />
                                <p v-if="form.errors.ubicacion" class="form-error">{{ form.errors.ubicacion }}</p>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('proveedores.index')" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Crear Proveedor</span>
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

.justify-end {
    justify-content: flex-end;
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

.gap-3 {
    gap: var(--spacing-3);
}

.gap-6 {
    gap: var(--spacing-6);
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

.max-w-2xl {
    max-width: 42rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.input {
    padding: var(--spacing-2) var(--spacing-3);
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
