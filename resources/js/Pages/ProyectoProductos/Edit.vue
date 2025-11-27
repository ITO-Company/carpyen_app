<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    proyecto: Object,
    producto: Object,
    cantidadActual: Number,
});

const form = useForm({
    cantidad: props.cantidadActual,
});

const submit = () => {
    form.put(route("proyectos.productos.update", [props.proyecto.id, props.producto.id]));
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};
</script>

<template>
    <Head title="Editar Producto del Proyecto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Editar Producto del Proyecto
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <!-- Info del Proyecto -->
                    <div class="mb-8 pb-6 border-b" style="border-color: var(--theme-border)">
                        <p class="text-sm mb-2" style="color: var(--theme-text-secondary);">Proyecto</p>
                        <p class="font-semibold text-lg" style="color: var(--theme-text-primary);">
                            {{ proyecto.nombre || "-" }}
                        </p>
                        <p class="text-sm mt-2" style="color: var(--theme-text-secondary);">
                            Cliente: <span style="color: var(--theme-text-primary);">{{ proyecto.cliente?.nombre || "-" }}</span>
                        </p>
                    </div>

                    <!-- Info del Producto -->
                    <div class="mb-8 pb-6 border-b" style="border-color: var(--theme-border)">
                        <p class="text-sm mb-2" style="color: var(--theme-text-secondary);">Producto</p>
                        <p class="font-semibold text-lg" style="color: var(--theme-text-primary);">
                            {{ producto.nombre || "-" }}
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3">
                            <div>
                                <p class="text-xs" style="color: var(--theme-text-secondary);">Tipo</p>
                                <p class="font-medium" style="color: var(--theme-text-primary);">{{ producto.tipo }}</p>
                            </div>
                            <div>
                                <p class="text-xs" style="color: var(--theme-text-secondary);">Precio Unitario</p>
                                <p class="font-medium" style="color: var(--theme-text-primary);">{{ formatCurrency(producto.precio_unitario) }}</p>
                            </div>
                            <div>
                                <p class="text-xs" style="color: var(--theme-text-secondary);">Stock General</p>
                                <p class="font-medium" style="color: var(--theme-text-primary);">{{ producto.stock }}</p>
                            </div>
                            <div>
                                <p class="text-xs" style="color: var(--theme-text-secondary);">Unidad de Medida</p>
                                <p class="font-medium" style="color: var(--theme-text-primary);">{{ producto.unidad_medida }}</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Cantidad -->
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold" style="color: var(--theme-text-primary);">
                                    Cantidad en Proyecto
                                    <span class="text-error">*</span>
                                </span>
                            </label>
                            <input
                                v-model.number="form.cantidad"
                                type="number"
                                min="1"
                                step="1"
                                class="input input-bordered w-full"
                                :class="form.errors.cantidad ? 'input-error' : ''"
                                style="
                                    background-color: var(--theme-bg-primary);
                                    color: var(--theme-text-primary);
                                    border-color: var(--theme-border);
                                "
                            />
                            <div v-if="form.errors.cantidad" class="text-error text-sm mt-1">
                                {{ form.errors.cantidad }}
                            </div>
                            <p class="text-xs mt-1" style="color: var(--theme-text-secondary);">
                                Cantidad anterior: <span style="color: var(--theme-text-primary);">{{ cantidadActual }}</span>
                            </p>
                            <p class="text-xs mt-1" style="color: var(--theme-text-secondary);">
                                Si aumenta la cantidad, se restará del stock. Si disminuye, se devolverá al stock.
                            </p>
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
                                    form.processing ? "Guardando..." : "Guardar Cambios"
                                }}
                            </button>
                            <Link
                                :href="route('proyectos.productos.index', proyecto.id)"
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

.grid {
    display: grid;
}

.grid-cols-2 {
    grid-template-columns: repeat(2, 1fr);
}

.md\:grid-cols-4 {
    grid-template-columns: repeat(4, 1fr);
}

.gap-4 {
    gap: var(--spacing-4);
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

.mt-3 {
    margin-top: var(--spacing-3);
}

.pt-6 {
    padding-top: var(--spacing-6);
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
