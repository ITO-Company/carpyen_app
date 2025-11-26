<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    proveedor: Object
});
</script>

<template>
    <Head :title="`Proveedor - ${proveedor.nombre}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    {{ proveedor.nombre }}
                </h2>
                <p class="text-sm" style="color: var(--theme-text-secondary)">
                    Detalles del proveedor
                </p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex gap-3">
                    <Link :href="route('proveedores.edit', { id: props.proveedor.id })" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar
                    </Link>
                    <Link :href="route('proveedores.index')" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Volver
                    </Link>
                </div>

                <!-- Información General -->
                <div class="card fade-in mb-6">
                    <h3 style="color: var(--theme-text-primary); font-size: 18px; font-weight: 600; margin-bottom: 20px;">
                        Información General
                    </h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <p class="info-label">Nombre</p>
                            <p class="info-value">{{ proveedor.nombre }}</p>
                        </div>

                        <div class="info-item">
                            <p class="info-label">Contacto</p>
                            <p class="info-value">{{ proveedor.contacto || 'No especificado' }}</p>
                        </div>

                        <div class="info-item">
                            <p class="info-label">Email</p>
                            <p class="info-value">
                                <a v-if="proveedor.email" :href="`mailto:${proveedor.email}`" style="color: var(--theme-primary); text-decoration: none;">
                                    {{ proveedor.email }}
                                </a>
                                <span v-else>No especificado</span>
                            </p>
                        </div>

                        <div class="info-item">
                            <p class="info-label">Teléfono</p>
                            <p class="info-value">{{ proveedor.telefono || 'No especificado' }}</p>
                        </div>

                        <div class="info-item" style="grid-column: 1 / -1;">
                            <p class="info-label">Ubicación</p>
                            <p class="info-value">{{ proveedor.ubicacion || 'No especificada' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Productos del Proveedor -->
                <div class="card fade-in mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 style="color: var(--theme-text-primary); font-size: 18px; font-weight: 600; margin: 0;">
                            Productos Relacionados
                        </h3>
                        <Link :href="route('proveedores.agregarProductos', { id: props.proveedor.id })" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Agregar Productos
                        </Link>
                    </div>

                    <div v-if="proveedor.productos && proveedor.productos.length > 0" class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--theme-border)">
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">ID</th>
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Nombre</th>
                                    <th style="text-align: right; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Cantidad</th>
                                    <th style="text-align: right; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Precio Unitario</th>
                                    <th style="text-align: right; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="producto in proveedor.productos" :key="producto.id" style="border-bottom: 1px solid var(--theme-border)">
                                    <td style="padding: 12px; color: var(--theme-text-secondary);">{{ producto.id }}</td>
                                    <td style="padding: 12px; color: var(--theme-text-primary); font-weight: 500;">{{ producto.nombre }}</td>
                                    <td style="padding: 12px; color: var(--theme-text-secondary); text-align: right;">
                                        {{ producto.pivot.cantidad }}
                                    </td>
                                    <td style="padding: 12px; color: var(--theme-text-secondary); text-align: right;">
                                        Bs. {{ parseFloat(producto.pivot.precio_unitario).toFixed(2) }}
                                    </td>
                                    <td style="padding: 12px; color: var(--theme-text-secondary); text-align: right;">
                                        Bs. {{ parseFloat(producto.pivot.total).toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else style="text-align: center; color: var(--theme-text-secondary); padding: 40px;">
                        <p>No hay productos asociados a este proveedor</p>
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

.gap-1 {
    gap: var(--spacing-1);
}

.gap-3 {
    gap: var(--spacing-3);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.flex {
    display: flex;
}

.justify-between {
    justify-content: space-between;
}

.items-center {
    align-items: center;
}

.gap-1 {
    gap: var(--spacing-1);
}

.gap-3 {
    gap: var(--spacing-3);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.max-w-4xl {
    max-width: 56rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.text-center {
    text-align: center;
}

.text-sm {
    font-size: var(--font-size-sm);
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

.flex-col {
    flex-direction: column;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-6);
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-2);
}

.info-label {
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--theme-text-secondary);
    text-transform: uppercase;
}

.info-value {
    font-size: 16px;
    color: var(--theme-text-primary);
    font-weight: 500;
}

.overflow-x-auto {
    overflow-x: auto;
}

.table {
    border-collapse: collapse;
    width: 100%;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
