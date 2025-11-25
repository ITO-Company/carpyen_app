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

const getEstadoClass = (estado) => {
    const estados = {
        'pendiente': 'badge-warning',
        'en_proceso': 'badge-info',
        'completado': 'badge-success',
        'rechazado': 'badge-error'
    };
    return estados[estado] || 'badge';
};

const getEstadoTexto = (estado) => {
    const textos = {
        'pendiente': 'Pendiente',
        'en_proceso': 'En Proceso',
        'completado': 'Completado',
        'rechazado': 'Rechazado'
    };
    return textos[estado] || estado;
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
                Galería de Diseños
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Barra de búsqueda con botón crear -->
                <div class="mb-6 flex justify-between items-center gap-4">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar diseños..."
                        class="input search-input"
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

                <!-- Contador de resultados -->
                <div class="mb-4" style="color: var(--theme-text-secondary); font-size: var(--font-size-sm)">
                    Mostrando {{ filteredDisenos.data.length }} diseño(s)
                </div>

                <!-- Grid de Cards -->
                <div class="designs-grid">
                    <div
                        v-for="diseno in filteredDisenos.data"
                        :key="diseno.id"
                        class="design-card"
                    >
                        <!-- Imagen del diseño -->
                        <div class="design-image-container">
                            <img
                                :src="diseno.url_render"
                                :alt="diseno.proyecto?.nombre || 'Diseño'"
                                class="design-image"
                                @error="(e) => e.target.src = 'https://via.placeholder.com/400x300?text=Sin+Imagen'"
                            />
                            <div class="design-overlay">
                                <span :class="['badge', getEstadoClass(diseno.estado)]">
                                    {{ getEstadoTexto(diseno.estado) }}
                                </span>
                            </div>
                        </div>

                        <!-- Contenido del card -->
                        <div class="design-content">
                            <h3 class="design-title">
                                {{ diseno.proyecto?.nombre || 'Sin proyecto' }}
                            </h3>
                            
                            <p class="design-description">
                                {{ diseno.descripcion || diseno.comentario || 'Sin descripción' }}
                            </p>

                            <!-- Información adicional -->
                            <div class="design-info">
                                <div class="info-item" v-if="diseno.fecha_inicio">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span>{{ new Date(diseno.fecha_inicio).toLocaleDateString('es-ES') }}</span>
                                </div>
                                
                                <div class="info-item" v-if="diseno.aprovado">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Aprobado</span>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="design-actions">
                                <Link
                                    :href="route('disenos.edit', diseno.id)"
                                    class="btn-action btn-action-edit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar
                                </Link>
                                
                                <Link
                                    :href="route('disenos.destroy', diseno.id)"
                                    method="delete"
                                    as="button"
                                    class="btn-action btn-action-delete"
                                    @click="(e) => !confirm('¿Estás seguro de eliminar este diseño?') && e.preventDefault()"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Eliminar
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensaje si no hay resultados -->
                <div v-if="filteredDisenos.data.length === 0" class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <p>No se encontraron diseños</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Grid de diseños */
.designs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--spacing-6);
    margin-bottom: var(--spacing-6);
}

/* Card de diseño */
.design-card {
    background: var(--theme-bg-primary);
    border: 1px solid var(--theme-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.design-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: var(--theme-primary);
}

/* Imagen del diseño */
.design-image-container {
    position: relative;
    width: 100%;
    height: 250px;
    overflow: hidden;
    background: var(--theme-bg-secondary);
}

.design-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.design-card:hover .design-image {
    transform: scale(1.05);
}

.design-overlay {
    position: absolute;
    top: var(--spacing-3);
    right: var(--spacing-3);
}

/* Contenido del card */
.design-content {
    padding: var(--spacing-5);
}

.design-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: var(--spacing-2);
    line-height: 1.4;
}

.design-description {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-4);
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Información adicional */
.design-info {
    display: flex;
    gap: var(--spacing-4);
    margin-bottom: var(--spacing-4);
    padding-bottom: var(--spacing-4);
    border-bottom: 1px solid var(--theme-border);
}

.info-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    font-size: var(--font-size-xs);
    color: var(--theme-text-secondary);
}

.info-item svg {
    color: var(--theme-primary);
}

/* Acciones */
.design-actions {
    display: flex;
    gap: var(--spacing-2);
}

.btn-action {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-2);
    padding: var(--spacing-2) var(--spacing-3);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid var(--theme-border);
    background: var(--theme-bg-primary);
    color: var(--theme-text-primary);
    cursor: pointer;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-action-edit:hover {
    border-color: var(--theme-primary);
    color: var(--theme-primary);
    background: var(--theme-primary-alpha);
}

.btn-action-delete:hover {
    border-color: var(--theme-error);
    color: var(--theme-error);
    background: rgba(239, 68, 68, 0.1);
}

/* Input de búsqueda */
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

/* Estado vacío */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-12) var(--spacing-6);
    color: var(--theme-text-secondary);
    text-align: center;
}

.empty-state svg {
    margin-bottom: var(--spacing-4);
    opacity: 0.5;
}

.empty-state p {
    font-size: var(--font-size-lg);
}

/* Responsive */
@media (max-width: 768px) {
    .designs-grid {
        grid-template-columns: 1fr;
    }
    
    .design-image-container {
        height: 200px;
    }
}
</style>
