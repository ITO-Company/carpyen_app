<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    diseno: Object,
});

const form = useForm({
    descripcion: props.diseno.descripcion || '',
    estado: props.diseno.estado || 'pendiente',
    url_render: props.diseno.url_render || '',
    plano_iluminador: props.diseno.plano_iluminador || '',
    comentario: props.diseno.comentario || '',
});

const submit = () => {
    form.put(route('diseñador.disenos.update', props.diseno.id));
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit' 
    });
};

const getEstadoLabel = (estado) => {
    const labels = {
        'pendiente': 'Pendiente',
        'en_proceso': 'En Proceso',
        'completado': 'Completado',
        'rechazado': 'Rechazado'
    };
    return labels[estado] || estado;
};

const getAprobadoLabel = (aprobado) => {
    return aprobado ? 'Aprobado' : 'Pendiente de Aprobación';
};
</script>

<template>
    <Head :title="`Editar Diseño #${diseno.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl leading-tight"
                    style="color: var(--theme-text-primary)"
                >
                    Editar Diseño #{{ diseno.id }}
                </h2>
                <Link :href="route('diseñador.disenos.index')" class="btn btn-secondary">
                    ← Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Detalles del Proyecto y Cotización -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-semibold mb-4" style="color: var(--theme-text-primary)">
                                    Información del Proyecto y Cotización
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-lg" style="background-color: var(--theme-bg-secondary);">
                                    <div>
                                        <label class="label-text">Proyecto</label>
                                        <p class="text-sm font-medium" style="color: var(--theme-text-primary);">
                                            {{ diseno.cotizacion?.proyecto?.nombre || '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="label-text">Cotización</label>
                                        <p class="text-sm font-medium" style="color: var(--theme-text-primary);">
                                            #{{ diseno.cotizacion?.id || '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="label-text">Aprobación</label>
                                        <p class="text-sm font-medium" style="color: var(--theme-text-primary);">
                                            {{ getAprobadoLabel(diseno.aprovado) }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="label-text">Fecha Inicio</label>
                                        <p class="text-sm font-medium" style="color: var(--theme-text-primary);">
                                            {{ formatDate(diseno.fecha_inicio) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label for="descripcion" class="label-text">Descripción</label>
                                <textarea
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    rows="4"
                                    class="input"
                                    placeholder="Descripción del diseño"
                                    :class="{ 'is-invalid': form.errors.descripcion }"
                                ></textarea>
                                <p v-if="form.errors.descripcion" class="text-error text-sm mt-1">
                                    {{ form.errors.descripcion }}
                                </p>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado" class="label-text">Estado del Diseño</label>
                                <select
                                    id="estado"
                                    v-model="form.estado"
                                    class="input"
                                    :class="{ 'is-invalid': form.errors.estado }"
                                >
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="completado">Completado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                                <p v-if="form.errors.estado" class="text-error text-sm mt-1">
                                    {{ form.errors.estado }}
                                </p>
                            </div>

                            <!-- URL Render -->
                            <div>
                                <label for="url_render" class="label-text">URL Render</label>
                                <input
                                    id="url_render"
                                    v-model="form.url_render"
                                    type="url"
                                    class="input"
                                    placeholder="https://ejemplo.com/render"
                                    :class="{ 'is-invalid': form.errors.url_render }"
                                />
                                <p v-if="form.errors.url_render" class="text-error text-sm mt-1">
                                    {{ form.errors.url_render }}
                                </p>
                            </div>

                            <!-- Plano Iluminador -->
                            <div>
                                <label for="plano_iluminador" class="label-text">Plano Iluminador</label>
                                <input
                                    id="plano_iluminador"
                                    v-model="form.plano_iluminador"
                                    type="url"
                                    class="input"
                                    placeholder="https://ejemplo.com/plano"
                                    :class="{ 'is-invalid': form.errors.plano_iluminador }"
                                />
                                <p v-if="form.errors.plano_iluminador" class="text-error text-sm mt-1">
                                    {{ form.errors.plano_iluminador }}
                                </p>
                            </div>

                            <!-- Comentario -->
                            <div class="md:col-span-2">
                                <label for="comentario" class="label-text">Comentarios</label>
                                <textarea
                                    id="comentario"
                                    v-model="form.comentario"
                                    rows="3"
                                    class="input"
                                    placeholder="Comentarios adicionales"
                                    :class="{ 'is-invalid': form.errors.comentario }"
                                ></textarea>
                                <p v-if="form.errors.comentario" class="text-error text-sm mt-1">
                                    {{ form.errors.comentario }}
                                </p>
                            </div>

                            <!-- Botones -->
                            <div class="md:col-span-2 flex gap-3 justify-end">
                                <Link :href="route('diseñador.disenos.index')" class="btn btn-secondary">
                                    Cancelar
                                </Link>
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Guardar Cambios</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
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

.gap-3 {
    gap: var(--spacing-3);
}

.gap-4 {
    gap: var(--spacing-4);
}

.gap-6 {
    gap: var(--spacing-6);
}

.mb-4 {
    margin-bottom: var(--spacing-4);
}

.mt-1 {
    margin-top: 0.25rem;
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.p-4 {
    padding: var(--spacing-4);
}

.max-w-4xl {
    max-width: 56rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:col-span-2 {
        grid-column: span 2;
    }

    .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.rounded-lg {
    border-radius: 0.5rem;
}

.font-semibold {
    font-weight: 600;
}

.font-medium {
    font-weight: 500;
}

.text-lg {
    font-size: var(--font-size-lg);
}

.text-sm {
    font-size: var(--font-size-sm);
}

.text-xl {
    font-size: var(--font-size-xl);
}

.leading-tight {
    line-height: 1.25;
}

.is-invalid {
    border-color: var(--theme-error) !important;
}

textarea,
input {
    font-family: inherit;
    resize: vertical;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
