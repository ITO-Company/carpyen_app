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
            (diseno.descripcion && diseno.descripcion.toLowerCase().includes(query)) ||
            (diseno.cotizacion?.proyecto?.nombre && diseno.cotizacion.proyecto.nombre.toLowerCase().includes(query)) ||
            (diseno.estado && diseno.estado.toLowerCase().includes(query))
        );
    });
    
    return {
        ...props.disenos,
        data: filtered
    };
});

const getEstadoBadge = (estado) => {
    const badgeMap = {
        'pendiente': 'badge-warning',
        'en_proceso': 'badge-info',
        'completado': 'badge-success',
        'rechazado': 'badge-error'
    };
    return badgeMap[estado] || 'badge-secondary';
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

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit' 
    });
};

const getAprobadoBadge = (aprobado) => {
    return aprobado ? 'badge-success' : 'badge-warning';
};

const getAprobadoLabel = (aprobado) => {
    return aprobado ? 'Aprobado' : 'Pendiente de Aprobación';
};
</script>

<template>
    <Head title="Mis Diseños" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl leading-tight"
                style="color: var(--theme-text-primary)"
            >
                Mis Diseños Asignados
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
                            placeholder="Buscar por descripción, proyecto o estado..."
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
                                    <th>Cotización</th>
                                    <th>Estado Diseño</th>
                                    <th>Aprobación</th>
                                    <th>Fecha Inicio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="diseno in filteredDisenos.data"
                                    :key="diseno.id"
                                >
                                    <td class="font-medium">{{ diseno.id }}</td>
                                    <td>
                                        <div class="max-w-xs truncate">
                                            {{ diseno.descripcion || '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium">
                                            {{ diseno.cotizacion?.proyecto?.nombre || "-" }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            Cot. #{{ diseno.cotizacion?.id || "-" }}
                                        </div>
                                    </td>
                                    <td>
                                        <span :class="['badge', getEstadoBadge(diseno.estado)]">
                                            {{ getEstadoLabel(diseno.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span :class="['badge', getAprobadoBadge(diseno.aprovado)]">
                                            {{ getAprobadoLabel(diseno.aprovado) }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(diseno.fecha_inicio) }}</td>
                                    <td>
                                        <div class="flex gap-2 flex-wrap">
                                            <Link
                                                :href="route('diseñador.disenos.edit', diseno.id)"
                                                class="text-primary hover:underline"
                                                style="color: var(--theme-primary);"
                                            >
                                                Editar
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="!filteredDisenos.data || filteredDisenos.data.length === 0"
                                >
                                    <td colspan="8" class="text-center py-8" style="color: var(--theme-text-secondary);">
                                        No tienes diseños asignados
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

.gap-2 {
    gap: var(--spacing-2);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.mt-4 {
    margin-top: var(--spacing-4);
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
