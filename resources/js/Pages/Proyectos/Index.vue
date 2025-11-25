<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    proyectos: Object,
});

const paginationLabel = (label) => {
    if (label.includes("Previous")) return "← Anterior";
    if (label.includes("Next")) return "Siguiente →";
    return label;
};

const estadoColors = {
    pendiente: "badge-warning",
    en_proceso: "badge-primary",
    completado: "badge-success",
    cancelado: "badge-error",
};

const estadoLabels = {
    pendiente: "Pendiente",
    en_proceso: "En Proceso",
    completado: "Completado",
    cancelado: "Cancelado",
};
</script>

<template>
    <Head title="Proyectos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl leading-tight"
                    style="color: var(--theme-text-primary)"
                >
                    Gestión de Proyectos
                </h2>
                <Link :href="route('proyectos.create')" class="btn btn-primary">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nuevo Proyecto
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="proyecto in proyectos.data"
                                    :key="proyecto.id"
                                >
                                    <td>{{ proyecto.id }}</td>
                                    <td class="font-medium">
                                        {{ proyecto.nombre || "-" }}
                                    </td>
                                    <td>
                                        {{ proyecto.cliente?.nombre || "-" }}
                                    </td>
                                    <td>
                                        {{
                                            proyecto.vendedor?.name ||
                                            "Sin asignar"
                                        }}
                                    </td>
                                    <td>{{ proyecto.ubicacion || "-" }}</td>
                                    <td>
                                        <span
                                            :class="[
                                                'badge',
                                                (proyecto.estado &&
                                                    estadoColors[
                                                        proyecto.estado
                                                    ]) ||
                                                    'badge-secondary',
                                            ]"
                                        >
                                            {{
                                                (proyecto.estado &&
                                                    estadoLabels[
                                                        proyecto.estado
                                                    ]) ||
                                                proyecto.estado ||
                                                "Desconocido"
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'proyectos.show',
                                                        proyecto.id
                                                    )
                                                "
                                                class="text-primary hover:underline"
                                                style="color: var(--theme-info)"
                                            >
                                                Ver
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'proyectos.edit',
                                                        proyecto.id
                                                    )
                                                "
                                                class="text-primary hover:underline"
                                                style="
                                                    color: var(--theme-primary);
                                                "
                                            >
                                                Editar
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        !proyectos.data ||
                                        proyectos.data.length === 0
                                    "
                                >
                                    <td
                                        colspan="7"
                                        class="text-center py-8"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        No hay proyectos registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div
                        v-if="proyectos.links"
                        class="mt-6 flex justify-between items-center"
                    >
                        <div
                            style="
                                color: var(--theme-text-secondary);
                                font-size: var(--font-size-sm);
                            "
                        >
                            Mostrando {{ proyectos.from }} a
                            {{ proyectos.to }} de
                            {{ proyectos.total }} resultados
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in proyectos.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'btn',
                                    link.active
                                        ? 'btn-primary'
                                        : 'btn-secondary',
                                    !link.url && 'btn-disabled',
                                ]"
                                @click.prevent="
                                    link.url && $inertia.visit(link.url)
                                "
                                v-text="paginationLabel(link.label)"
                            />
                        </div>
                    </div>
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

.items-center {
    align-items: center;
}

.gap-2 {
    gap: var(--spacing-2);
}

.mt-6 {
    margin-top: var(--spacing-6);
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.max-w-7xl {
    max-width: 80rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.overflow-x-auto {
    overflow-x: auto;
}

.text-center {
    text-align: center;
}

.font-medium {
    font-weight: 500;
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
</style>
