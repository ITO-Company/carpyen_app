<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    tareas: Object,
});

const searchQuery = ref("");

const filteredTareas = computed(() => {
    if (!searchQuery.value) {
        return props.tareas;
    }
    
    const query = searchQuery.value.toLowerCase();
    const filtered = props.tareas.data.filter(tarea => {
        return (
            (tarea.descripcion && tarea.descripcion.toLowerCase().includes(query)) ||
            (tarea.cronograma?.proyecto?.nombre && tarea.cronograma.proyecto.nombre.toLowerCase().includes(query)) ||
            (tarea.cronograma?.proyecto?.ubicacion && tarea.cronograma.proyecto.ubicacion.toLowerCase().includes(query))
        );
    });
    
    return {
        ...props.tareas,
        data: filtered
    };
});

const getEstadoBadge = (estado) => {
    const badgeMap = {
        'pendiente': 'badge-warning',
        'en_progreso': 'badge-info',
        'completada': 'badge-success',
        'bloqueada': 'badge-error'
    };
    return badgeMap[estado] || 'badge-secondary';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'pendiente': 'Pendiente',
        'en_progreso': 'En Progreso',
        'completada': 'Completada',
        'bloqueada': 'Bloqueada'
    };
    return labels[estado] || estado;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit' 
    });
};
</script>

<template>
    <Head title="Mis Tareas" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl leading-tight"
                style="color: var(--theme-text-primary)"
            >
                Mis Tareas Asignadas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <!-- Barra de búsqueda -->
                    <div class="mb-6">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar por descripción, proyecto o ubicación..."
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
                                    <th>Proyecto</th>
                                    <th>Ubicación</th>
                                    <th>Cronograma</th>
                                    <th>Fecha Inicio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="tarea in filteredTareas.data"
                                    :key="tarea.id"
                                >
                                    <td class="font-medium">{{ tarea.id }}</td>
                                    <td>
                                        <div class="max-w-xs truncate">
                                            {{ tarea.descripcion }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium">
                                            {{ tarea.cronograma?.proyecto?.nombre || "-" }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            {{ tarea.cronograma?.proyecto?.ubicacion || "-" }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm" style="color: var(--theme-text-secondary);">
                                            Asignado: {{ tarea.cronograma?.usuario?.nombre || "-" }}
                                        </div>
                                        <div class="text-xs mt-1" style="color: var(--theme-text-secondary);">
                                            {{ formatDate(tarea.cronograma?.fecha_inicio) }}
                                        </div>
                                    </td>
                                    <td>{{ formatDate(tarea.cronograma?.fecha_inicio) }}</td>
                                    <td>
                                        <span :class="['badge', getEstadoBadge(tarea.estado)]">
                                            {{ getEstadoLabel(tarea.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2 flex-wrap">
                                            <Link
                                                :href="route('instalador.tareas.edit', tarea.id)"
                                                class="text-primary hover:underline"
                                                style="color: var(--theme-primary);"
                                            >
                                                Editar
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="!filteredTareas.data || filteredTareas.data.length === 0"
                                >
                                    <td colspan="8" class="text-center py-8" style="color: var(--theme-text-secondary);">
                                        No tienes tareas asignadas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contador de resultados -->
                    <div
                        v-if="filteredTareas.data && filteredTareas.data.length > 0"
                        class="mt-4 text-sm"
                        style="color: var(--theme-text-secondary)"
                    >
                        Mostrando {{ filteredTareas.data.length }} resultado(s)
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

.gap-2 {
    gap: var(--spacing-2);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.mt-4 {
    margin-top: var(--spacing-4);
}

.mt-1 {
    margin-top: 0.25rem;
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

.max-w-xs {
    max-width: 20rem;
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

.text-xs {
    font-size: var(--font-size-xs);
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

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.flex-wrap {
    flex-wrap: wrap;
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
</style>
