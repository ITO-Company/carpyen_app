<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    cronograma: Object,
    tareas: Array,
});

const searchQuery = ref("");

const filteredTareas = computed(() => {
    if (!searchQuery.value) {
        return props.tareas;
    }
    
    const query = searchQuery.value.toLowerCase();
    return props.tareas.filter(tarea => {
        return (
            (tarea.descripcion && tarea.descripcion.toLowerCase().includes(query)) ||
            (tarea.instalador?.name && tarea.instalador.name.toLowerCase().includes(query))
        );
    });
});

const getEstadoBadge = (estado) => {
    const badgeMap = {
        'pendiente': 'badge-warning',
        'en_proceso': 'badge-info',
        'completada': 'badge-success',
        'cancelada': 'badge-error'
    };
    return badgeMap[estado] || 'badge-secondary';
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
        'completada': 'Completada',
        'cancelada': 'Cancelada'
    };
    return labels[estado] || estado;
};
</script>

<template>
    <Head title="Tareas del Cronograma" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Tareas del Cronograma
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Info del Cronograma -->
                <div class="card fade-in mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm" style="color: var(--theme-text-secondary);">Proyecto</p>
                            <p class="font-semibold" style="color: var(--theme-text-primary);">
                                {{ cronograma.proyecto?.nombre || '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm" style="color: var(--theme-text-secondary);">Usuario</p>
                            <p class="font-semibold" style="color: var(--theme-text-primary);">
                                {{ cronograma.usuario?.name || '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm" style="color: var(--theme-text-secondary);">Fecha Inicio</p>
                            <p class="font-semibold" style="color: var(--theme-text-primary);">
                                {{ formatDate(cronograma.fecha_inicio) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm" style="color: var(--theme-text-secondary);">Estado</p>
                            <span :class="['badge', getEstadoBadge(cronograma.estado)]">
                                {{ getEstadoLabel(cronograma.estado) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="mb-6 flex gap-3 flex-wrap">
                    <Link :href="route('cronogramas.index')" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Volver a Cronogramas
                    </Link>
                    <Link :href="route('tareas.create', cronograma.id)" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nueva Tarea
                    </Link>
                </div>

                <div class="card fade-in">
                    <!-- Barra de búsqueda -->
                    <div class="mb-6">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar por descripción o usuario..."
                            class="input search-input"
                            style="width: 100%; max-width: 500px"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Hora Inicio</th>
                                    <th>Hora Fin</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="tarea in filteredTareas"
                                    :key="tarea.id"
                                >
                                    <td>{{ tarea.id }}</td>
                                    <td class="font-medium">
                                        {{ tarea.descripcion || "-" }}
                                    </td>
                                    <td>{{ tarea.instalador?.name || "-" }}</td>
                                    <td>{{ formatDate(tarea.fecha) }}</td>
                                    <td>{{ tarea.hora_inicio || "-" }}</td>
                                    <td>{{ tarea.hora_fin || "-" }}</td>
                                    <td>
                                        <span :class="['badge', getEstadoBadge(tarea.estado)]">
                                            {{ getEstadoLabel(tarea.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <Link
                                                :href="route('tareas.edit', tarea.id)"
                                                class="text-primary hover:underline"
                                                style="color: var(--theme-primary);"
                                            >
                                                Editar
                                            </Link>
                                            <Link
                                                :href="route('tareas.destroy', tarea.id)"
                                                method="delete"
                                                as="button"
                                                class="text-error hover:underline"
                                                style="color: var(--theme-error);"
                                            >
                                                Eliminar
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="!filteredTareas || filteredTareas.length === 0"
                                >
                                    <td colspan="7" class="text-center py-8" style="color: var(--theme-text-secondary);">
                                        No hay tareas registradas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contador de resultados -->
                    <div
                        v-if="filteredTareas && filteredTareas.length > 0"
                        class="mt-4 text-sm"
                        style="color: var(--theme-text-secondary)"
                    >
                        Mostrando {{ filteredTareas.length }} resultado(s)
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
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

.gap-3 {
    gap: var(--spacing-3);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.mt-4 {
    margin-top: var(--spacing-4);
}

.flex {
    display: flex;
}

.flex-wrap {
    flex-wrap: wrap;
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

.text-sm {
    font-size: var(--font-size-sm);
}

.leading-tight {
    line-height: 1.25;
}

.overflow-x-auto {
    overflow-x: auto;
}

.text-center {
    text-align: center;
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
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

/* Adaptación del buscador al tema */
.search-input {
    background-color: var(--theme-bg-primary) !important;
    color: var(--theme-text-primary) !important;
    border: 1px solid var(--theme-border) !important;
}

.search-input::placeholder {
    color: var(--theme-text-secondary) !important;
    opacity: 0.7;
}

.search-input:focus {
    border-color: var(--theme-primary) !important;
    outline: none;
    box-shadow: 0 0 0 3px var(--theme-primary-alpha) !important;
}

@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>
