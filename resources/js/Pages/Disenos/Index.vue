<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    disenos: Object,
});

const searchQuery = ref("");

const filteredDisenos = computed(() => {
    if (!searchQuery.value) {
        return props.disenos;
    }
    
    const query = searchQuery.value.toLowerCase();
    const filtered = props.disenos.data.filter(diseno => {
        return (
            (diseno.proyecto?.nombre && diseno.proyecto.nombre.toLowerCase().includes(query)) ||
            (diseno.descripcion && diseno.descripcion.toLowerCase().includes(query)) ||
            (diseno.estado && diseno.estado.toLowerCase().includes(query))
        );
    });
    
    return {
        ...props.disenos,
        data: filtered
    };
});

const paginationLabel = (label) => {
    if (label.includes("Previous")) return "← Anterior";
    if (label.includes("Next")) return "Siguiente →";
    return label;
};
</script>

<template>
    <Head title="Diseños" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl leading-tight"
                style="color: var(--theme-text-primary)"
            >
                Gestión de Diseños
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <!-- Barra de búsqueda con botón crear -->
                    <div class="mb-6 flex justify-between items-center gap-4">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar diseños..."
                            class="input"
                            style="flex: 1; max-width: 500px"
                        />
                        <Link :href="route('disenos.create')" class="btn btn-primary">
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
                            Nuevo Diseño
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proyecto</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="diseno in filteredDisenos.data"
                                    :key="diseno.id"
                                >
                                    <td>{{ diseno.id }}</td>
                                    <td class="font-medium">
                                        {{ diseno.proyecto?.nombre || "-" }}
                                    </td>
                                    <td>{{ diseno.descripcion || "-" }}</td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="{
                                                'badge-warning':
                                                    (diseno.estado ?? null) ===
                                                    'pendiente',
                                                'badge-info':
                                                    (diseno.estado ?? null) ===
                                                    'en_proceso',
                                                'badge-success':
                                                    (diseno.estado ?? null) ===
                                                    'completado',
                                                'badge-error':
                                                    (diseno.estado ?? null) ===
                                                    'rechazado',
                                            }"
                                        >
                                            {{ diseno.estado || "-" }}
                                        </span>
                                    </td>
                                    <td>{{ diseno.fecha_inicio || "-" }}</td>
                                    <td>{{ diseno.fecha_fin || "-" }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'disenos.edit',
                                                        diseno.id
                                                    )
                                                "
                                                class="text-primary hover:underline"
                                                style="
                                                    color: var(--theme-primary);
                                                "
                                            >
                                                Editar
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'disenos.destroy',
                                                        diseno.id
                                                    )
                                                "
                                                method="delete"
                                                as="button"
                                                class="text-error hover:underline"
                                                style="
                                                    color: var(--theme-error);
                                                "
                                            >
                                                Eliminar
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        !filteredDisenos.data ||
                                        filteredDisenos.data.length === 0
                                    "
                                >
                                    <td
                                        colspan="7"
                                        class="text-center py-8"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        No hay diseños registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contador de resultados -->
                    <div
                        v-if="filteredDisenos.data && filteredDisenos.data.length > 0"
                        class="mt-4 text-sm"
                        style="color: var(--theme-text-secondary)"
                    >
                        Mostrando {{ filteredDisenos.data.length }} resultado(s)
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

.gap-4 {
    gap: var(--spacing-4);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.mt-4 {
    margin-top: var(--spacing-4);
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

.text-sm {
    font-size: var(--font-size-sm);
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

/* Mejoras visuales para la tabla */
.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
</style>
