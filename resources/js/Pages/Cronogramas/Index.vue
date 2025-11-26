<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    cronogramas: Object,
});

const searchQuery = ref("");

const filteredCronogramas = computed(() => {
    if (!searchQuery.value) {
        return props.cronogramas;
    }
    
    const query = searchQuery.value.toLowerCase();
    const filtered = props.cronogramas.data.filter(cronograma => {
        return (
            (cronograma.proyecto?.nombre && cronograma.proyecto.nombre.toLowerCase().includes(query)) ||
            (cronograma.usuario?.name && cronograma.usuario.name.toLowerCase().includes(query))
        );
    });
    
    return {
        ...props.cronogramas,
        data: filtered
    };
});

const getEstadoBadge = (estado) => {
    const badgeMap = {
        'pendiente': 'badge-warning',
        'en_curso': 'badge-info',
        'completado': 'badge-success',
        'atrasado': 'badge-error'
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
        'en_curso': 'En Curso',
        'completado': 'Completado',
        'atrasado': 'Atrasado'
    };
    return labels[estado] || estado;
};
</script>

<template>
    <Head title="Cronogramas" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl leading-tight"
                style="color: var(--theme-text-primary)"
            >
                Gestión de Cronogramas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('cronogramas.create')" class="btn btn-primary">
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
                        Nuevo Cronograma
                    </Link>
                </div>

                <div class="card fade-in">
                    <!-- Barra de búsqueda -->
                    <div class="mb-6">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar por proyecto o usuario..."
                            class="input search-input"
                            style="width: 100%; max-width: 500px"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proyecto</th>
                                    <th>Usuario</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Días Estimados</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="cronograma in filteredCronogramas.data"
                                    :key="cronograma.id"
                                >
                                    <td>{{ cronograma.id }}</td>
                                    <td class="font-medium">
                                        {{ cronograma.proyecto?.nombre || "-" }}
                                    </td>
                                    <td>{{ cronograma.usuario?.name || "-" }}</td>
                                    <td>{{ formatDate(cronograma.fecha_inicio) }}</td>
                                    <td>{{ formatDate(cronograma.fecha_fin) }}</td>
                                    <td>{{ cronograma.dias_estimados }} días</td>
                                    <td>
                                        <span :class="['badge', getEstadoBadge(cronograma.estado)]">
                                            {{ getEstadoLabel(cronograma.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <Link
                                                :href="route('cronogramas.edit', cronograma.id)"
                                                class="text-primary hover:underline"
                                                style="color: var(--theme-primary);"
                                            >
                                                Editar
                                            </Link>
                                            <Link
                                                :href="route('cronogramas.destroy', cronograma.id)"
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
                                    v-if="!filteredCronogramas.data || filteredCronogramas.data.length === 0"
                                >
                                    <td colspan="8" class="text-center py-8" style="color: var(--theme-text-secondary);">
                                        No hay cronogramas registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contador de resultados -->
                    <div
                        v-if="filteredCronogramas.data && filteredCronogramas.data.length > 0"
                        class="mt-4 text-sm"
                        style="color: var(--theme-text-secondary)"
                    >
                        Mostrando {{ filteredCronogramas.data.length }} resultado(s)
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
</style>
