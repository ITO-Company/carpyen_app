<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed } from "vue";

const props = defineProps({
    cronograma: Object,
    usuarios: Array,
});

const form = useForm({
    cronograma_id: props.cronograma.id,
    user_id: null,
    fecha: "",
    hora_inicio: "",
    hora_fin: "",
    descripcion: "",
});

const submit = () => {
    form.post(route("tareas.store", props.cronograma.id));
};

const usuarios_filtrados = computed(() => {
    if (!props.usuarios) return [];
    return props.usuarios;
});

// Calcula la fecha mínima permitida (fecha de inicio del cronograma)
const fechaMinima = computed(() => {
    if (!props.cronograma.fecha_inicio) return "";
    return new Date(props.cronograma.fecha_inicio).toISOString().split('T')[0];
});
</script>

<template>
    <Head title="Nueva Tarea" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Nueva Tarea
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <!-- Info del Cronograma -->
                    <div class="mb-8 pb-6 border-b" style="border-color: var(--theme-border)">
                        <p class="text-sm mb-2" style="color: var(--theme-text-secondary);">Cronograma</p>
                        <p class="font-semibold text-lg" style="color: var(--theme-text-primary);">
                            {{ cronograma.proyecto?.nombre || "-" }} - {{ cronograma.usuario?.nombre || "-" }}
                        </p>
                        <p class="text-sm mt-2" style="color: var(--theme-text-secondary);">
                            Fecha de inicio: <span style="color: var(--theme-text-primary);">{{ new Date(cronograma.fecha_inicio).toLocaleDateString('es-ES') }}</span>
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Usuario -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold" style="color: var(--theme-text-primary);">
                                    Usuario (Instalador)
                                    <span class="text-error">*</span>
                                </span>
                            </label>
                            <select
                                v-model="form.user_id"
                                class="select select-bordered w-full"
                                :class="form.errors.user_id ? 'select-error' : ''"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    color: var(--theme-text-primary);
                                    border-color: var(--theme-border);
                                "
                            >
                                <option value="" disabled selected>Seleccionar usuario...</option>
                                <option
                                    v-for="usuario in usuarios_filtrados"
                                    :key="usuario.id"
                                    :value="usuario.id"
                                >
                                    {{ usuario.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.user_id" class="text-error text-sm mt-1">
                                {{ form.errors.user_id }}
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold" style="color: var(--theme-text-primary);">
                                    Fecha
                                    <span class="text-error">*</span>
                                </span>
                            </label>
                            <input
                                v-model="form.fecha"
                                type="date"
                                :min="fechaMinima"
                                class="input input-bordered w-full"
                                :class="form.errors.fecha ? 'input-error' : ''"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    color: var(--theme-text-primary);
                                    border-color: var(--theme-border);
                                "
                            />
                            <div v-if="form.errors.fecha" class="text-error text-sm mt-1">
                                {{ form.errors.fecha }}
                            </div>
                            <p class="text-xs mt-1" style="color: var(--theme-text-secondary);">
                                La fecha debe ser igual o posterior a {{ new Date(cronograma.fecha_inicio).toLocaleDateString('es-ES') }}
                            </p>
                        </div>

                        <!-- Hora Inicio -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold" style="color: var(--theme-text-primary);">
                                    Hora Inicio
                                    <span class="text-error">*</span>
                                </span>
                            </label>
                            <input
                                v-model="form.hora_inicio"
                                type="time"
                                class="input input-bordered w-full"
                                :class="form.errors.hora_inicio ? 'input-error' : ''"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    color: var(--theme-text-primary);
                                    border-color: var(--theme-border);
                                "
                            />
                            <div v-if="form.errors.hora_inicio" class="text-error text-sm mt-1">
                                {{ form.errors.hora_inicio }}
                            </div>
                        </div>

                        <!-- Hora Fin -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold" style="color: var(--theme-text-primary);">
                                    Hora Fin
                                    <span class="text-error">*</span>
                                </span>
                            </label>
                            <input
                                v-model="form.hora_fin"
                                type="time"
                                class="input input-bordered w-full"
                                :class="form.errors.hora_fin ? 'input-error' : ''"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    color: var(--theme-text-primary);
                                    border-color: var(--theme-border);
                                "
                            />
                            <div v-if="form.errors.hora_fin" class="text-error text-sm mt-1">
                                {{ form.errors.hora_fin }}
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold" style="color: var(--theme-text-primary);">
                                    Descripción
                                    <span class="text-error">*</span>
                                </span>
                            </label>
                            <textarea
                                v-model="form.descripcion"
                                class="textarea textarea-bordered w-full"
                                :class="form.errors.descripcion ? 'textarea-error' : ''"
                                rows="4"
                                placeholder="Descripción de la tarea..."
                                style="
                                    background-color: var(--theme-bg-primary);
                                    color: var(--theme-text-primary);
                                    border-color: var(--theme-border);
                                "
                            ></textarea>
                            <div v-if="form.errors.descripcion" class="text-error text-sm mt-1">
                                {{ form.errors.descripcion }}
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3 pt-6 border-t" style="border-color: var(--theme-border)">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <svg
                                    v-if="!form.processing"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                    <polyline points="19 12 12 12 12 12"></polyline>
                                </svg>
                                {{
                                    form.processing ? "Guardando..." : "Guardar Tarea"
                                }}
                            </button>
                            <Link
                                :href="route('tareas.index', cronograma.id)"
                                class="btn btn-secondary"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.space-y-6 > * + * {
    margin-top: var(--spacing-6);
}

.pb-6 {
    padding-bottom: var(--spacing-6);
}

.mb-8 {
    margin-bottom: var(--spacing-8);
}

.mb-2 {
    margin-bottom: var(--spacing-2);
}

.mt-1 {
    margin-top: var(--spacing-1);
}

.mt-2 {
    margin-top: var(--spacing-2);
}

.pt-6 {
    padding-top: var(--spacing-6);
}

.font-semibold {
    font-weight: 600;
}

.text-lg {
    font-size: var(--font-size-lg);
}

.text-sm {
    font-size: var(--font-size-sm);
}

.text-xs {
    font-size: var(--font-size-xs, 0.75rem);
}

.text-xl {
    font-size: var(--font-size-xl);
}

.leading-tight {
    line-height: 1.25;
}

.gap-3 {
    gap: var(--spacing-3);
}

.flex {
    display: flex;
}

.w-full {
    width: 100%;
}

.btn svg {
    margin-right: var(--spacing-2);
}
</style>
