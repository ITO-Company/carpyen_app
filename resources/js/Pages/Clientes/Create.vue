<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    nombre: '',
    email: '',
    telefono: '',
    direccion: '',
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.post(route('clientes.store'));
};
</script>

<template>
    <Head title="Nuevo Cliente" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Nuevo Cliente
                </h2>
                <Link :href="route('clientes.index')" class="btn btn-secondary">
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
                                    Nombre Completo <span class="text-error">*</span>
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="input"
                                    required
                                    placeholder="Ej: Juan Pérez"
                                />
                                <div v-if="form.errors.nombre" class="form-error">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    Correo Electrónico <span class="text-error">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="input"
                                    required
                                    placeholder="ejemplo@correo.com"
                                />
                                <div v-if="form.errors.email" class="form-error">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="form-group">
                                <label for="telefono" class="form-label">
                                    Teléfono <span class="text-error">*</span>
                                </label>
                                <input
                                    id="telefono"
                                    v-model="form.telefono"
                                    type="tel"
                                    class="input"
                                    required
                                    placeholder="70000000"
                                />
                                <div v-if="form.errors.telefono" class="form-error">
                                    {{ form.errors.telefono }}
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="form-group full-width">
                                <label for="direccion" class="form-label">
                                    Dirección <span class="text-error">*</span>
                                </label>
                                <textarea
                                    id="direccion"
                                    v-model="form.direccion"
                                    class="input"
                                    rows="3"
                                    required
                                    placeholder="Dirección completa"
                                ></textarea>
                                <div v-if="form.errors.direccion" class="form-error">
                                    {{ form.errors.direccion }}
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    Contraseña <span class="text-error">*</span>
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="input"
                                    required
                                    placeholder="Mínimo 8 caracteres"
                                />
                                <div v-if="form.errors.password" class="form-error">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    Confirmar Contraseña <span class="text-error">*</span>
                                </label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="input"
                                    required
                                    placeholder="Repite tu contraseña"
                                />
                                <div v-if="form.errors.password_confirmation" class="form-error">
                                    {{ form.errors.password_confirmation }}
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions">
                            <Link :href="route('clientes.index')" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button 
                                type="submit" 
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Cliente</span>
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

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
