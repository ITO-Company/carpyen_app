<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    proveedores: Object
});

const getEstadoClass = (estado) => {
    const estados = {
        'activo': 'badge-success',
        'inactivo': 'badge-error'
    };
    return estados[estado] || 'badge';
};
</script>

<template>
    <Head title="Proveedores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Gestión de Proveedores
                </h2>
                <p class="text-sm" style="color: var(--theme-text-secondary)">
                    Administra los proveedores y sus productos
                </p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('proveedores.create')" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nuevo Proveedor
                    </Link>
                </div>

                <div class="card fade-in">
                    <!-- Tabla de proveedores -->
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--theme-border)">
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">ID</th>
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Nombre</th>
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Contacto</th>
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Email</th>
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Teléfono</th>
                                    <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Ubicación</th>
                                    <th style="text-align: center; padding: 12px; color: var(--theme-text-primary); font-weight: 600;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proveedor in proveedores.data" :key="proveedor.id" style="border-bottom: 1px solid var(--theme-border)">
                                    <td style="padding: 12px; color: var(--theme-text-secondary);">{{ proveedor.id }}</td>
                                    <td style="padding: 12px; color: var(--theme-text-primary); font-weight: 500;">{{ proveedor.nombre }}</td>
                                    <td style="padding: 12px; color: var(--theme-text-secondary);">{{ proveedor.contacto || '-' }}</td>
                                    <td style="padding: 12px; color: var(--theme-text-secondary);">
                                        <a v-if="proveedor.email" :href="`mailto:${proveedor.email}`" style="color: var(--theme-primary); text-decoration: none;">
                                            {{ proveedor.email }}
                                        </a>
                                        <span v-else>-</span>
                                    </td>
                                    <td style="padding: 12px; color: var(--theme-text-secondary);">{{ proveedor.telefono || '-' }}</td>
                                    <td style="padding: 12px; color: var(--theme-text-secondary);">{{ proveedor.ubicacion || '-' }}</td>
                                    <td style="padding: 12px;">
                                        <div class="action-buttons">
                                            <Link
                                                :href="route('proveedores.show', { id: proveedor.id })"
                                                class="btn-icon btn-icon-view"
                                                title="Ver"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="route('proveedores.edit', { id: proveedor.id })"
                                                class="btn-icon btn-icon-edit"
                                                title="Editar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="proveedores.data.length === 0">
                                    <td colspan="7" class="text-center py-8" style="color: var(--theme-text-secondary)">
                                        No hay proveedores registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div v-if="proveedores.links && proveedores.links.length > 3" class="mt-6 flex justify-center gap-2">
                        <template v-for="link in proveedores.links" :key="link.url">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="{
                                    'btn btn-primary': link.active,
                                    'btn btn-secondary': !link.active
                                }"
                                v-html="link.label"
                            ></Link>
                            <span v-else :class="{ 'opacity-50': !link.active }" v-html="link.label"></span>
                        </template>
                    </div>

                    <!-- Stats -->
                    <div v-if="proveedores.data && proveedores.data.length > 0" class="mt-6 p-4" style="background-color: var(--theme-bg-secondary); border-radius: 8px;">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">Total Proveedores</p>
                                <p class="text-lg font-semibold" style="color: var(--theme-text-primary);">{{ proveedores.total }}</p>
                            </div>
                            <div>
                                <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">En Esta Página</p>
                                <p class="text-lg font-semibold" style="color: var(--theme-text-primary);">{{ proveedores.data.length }}</p>
                            </div>
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

.justify-center {
    justify-content: center;
}

.items-center {
    align-items: center;
}

.gap-1 {
    gap: var(--spacing-1);
}

.gap-2 {
    gap: var(--spacing-2);
}

.gap-4 {
    gap: var(--spacing-4);
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

.text-center {
    text-align: center;
}

.text-sm {
    font-size: var(--font-size-sm);
}

.text-lg {
    font-size: var(--font-size-lg);
}

.font-semibold {
    font-weight: 600;
}

.flex-col {
    flex-direction: column;
}

.grid-cols-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
}

.overflow-x-auto {
    overflow-x: auto;
}

.table {
    border-collapse: collapse;
    width: 100%;
}

.action-buttons {
    display: flex;
    gap: var(--spacing-2);
    justify-content: center;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-icon-view {
    color: var(--theme-primary);
    background-color: var(--theme-primary-alpha);
}

.btn-icon-view:hover {
    background-color: var(--theme-primary);
    color: white;
}

.btn-icon-edit {
    color: var(--theme-info);
    background-color: var(--theme-info-alpha);
}

.btn-icon-edit:hover {
    background-color: var(--theme-info);
    color: white;
}

.opacity-50 {
    opacity: 0.5;
}
</style>
