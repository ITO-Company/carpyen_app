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
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: var(--spacing-8);
    margin-bottom: var(--spacing-6);
}

/* Card de diseño - Diseño premium mejorado */
.design-card {
    background: var(--theme-bg-primary);
    border: 2px solid var(--theme-border);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -1px rgba(0, 0, 0, 0.06),
        0 0 0 1px rgba(0, 0, 0, 0.05);
    position: relative;
}

/* Efecto de brillo sutil en el borde */
.design-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 20px;
    padding: 2px;
    background: linear-gradient(135deg, 
        var(--theme-primary) 0%, 
        transparent 50%, 
        var(--theme-primary) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.design-card:hover::before {
    opacity: 0.6;
}

.design-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 
        0 20px 25px -5px rgba(0, 0, 0, 0.15),
        0 10px 10px -5px rgba(0, 0, 0, 0.08),
        0 0 0 1px var(--theme-primary);
    border-color: var(--theme-primary);
}

/* Imagen del diseño con overlay gradiente */
.design-image-container {
    position: relative;
    width: 100%;
    height: 280px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--theme-bg-secondary) 0%, var(--theme-bg-tertiary) 100%);
}

/* Gradiente overlay sobre la imagen */
.design-image-container::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50%;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.4), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.design-card:hover .design-image-container::after {
    opacity: 1;
}

.design-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    filter: brightness(0.95);
}

.design-card:hover .design-image {
    transform: scale(1.1);
    filter: brightness(1.05);
}

.design-overlay {
    position: absolute;
    top: var(--spacing-4);
    right: var(--spacing-4);
    z-index: 10;
}

/* Contenido del card con mejor espaciado */
.design-content {
    padding: var(--spacing-6);
    background: var(--theme-bg-primary);
}

.design-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--theme-text-primary);
    margin-bottom: var(--spacing-3);
    line-height: 1.3;
    letter-spacing: -0.02em;
}

.design-description {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-5);
    line-height: 1.7;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Información adicional con iconos mejorados */
.design-info {
    display: flex;
    gap: var(--spacing-5);
    margin-bottom: var(--spacing-5);
    padding-bottom: var(--spacing-4);
    border-bottom: 2px solid var(--theme-border);
}

.info-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    font-size: var(--font-size-xs);
    font-weight: 500;
    color: var(--theme-text-secondary);
    padding: var(--spacing-1) var(--spacing-2);
    background: var(--theme-bg-secondary);
    border-radius: 8px;
}

.info-item svg {
    color: var(--theme-primary);
    flex-shrink: 0;
}

/* Acciones con diseño mejorado */
.design-actions {
    display: flex;
    gap: var(--spacing-3);
}

.btn-action {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-2);
    padding: var(--spacing-3) var(--spacing-4);
    border-radius: 12px;
    font-size: var(--font-size-sm);
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid var(--theme-border);
    background: var(--theme-bg-secondary);
    color: var(--theme-text-primary);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-action::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: var(--theme-primary-alpha);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
}

.btn-action:hover::before {
    width: 300px;
    height: 300px;
}

.btn-action svg {
    position: relative;
    z-index: 1;
}

.btn-action span {
    position: relative;
    z-index: 1;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

.btn-action-delete:hover::before {
    background: rgba(239, 68, 68, 0.1);
}

/* Input de búsqueda mejorado */
.search-input {
    background-color: var(--theme-bg-primary) !important;
    color: var(--theme-text-primary) !important;
    border: 2px solid var(--theme-border) !important;
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
}

.search-input::placeholder {
    color: var(--theme-text-secondary) !important;
    opacity: 0.7;
}

.search-input:focus {
    border-color: var(--theme-primary) !important;
    outline: none;
    box-shadow: 0 0 0 4px var(--theme-primary-alpha) !important;
    transform: translateY(-1px);
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
    background: var(--theme-bg-secondary);
    border-radius: 20px;
    border: 2px dashed var(--theme-border);
}

.empty-state svg {
    margin-bottom: var(--spacing-4);
    opacity: 0.4;
}

.empty-state p {
    font-size: var(--font-size-lg);
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .designs-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-6);
    }
    
    .design-image-container {
        height: 220px;
    }
    
    .design-card {
        border-radius: 16px;
    }
}

/* Mejoras específicas para modo oscuro */
@media (prefers-color-scheme: dark) {
    .design-card {
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.3),
            0 2px 4px -1px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.05);
    }
    
    .design-card:hover {
        box-shadow: 
            0 20px 25px -5px rgba(0, 0, 0, 0.4),
            0 10px 10px -5px rgba(0, 0, 0, 0.3),
            0 0 0 1px var(--theme-primary);
    }
    
    .design-image {
        filter: brightness(0.9);
    }
    
    .design-card:hover .design-image {
        filter: brightness(1);
    }
}
</style>
