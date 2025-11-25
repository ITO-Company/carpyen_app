<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    pagos: Object
});
</script>

<template>
    <Head title="Pagos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Gestión de Pagos
                </h2>
                <Link :href="route('pagos.create')" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nuevo Pago
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
                                    <th>Plan de Pago</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Método de Pago</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pago in pagos.data" :key="pago.id">
                                    <td>{{ pago.id }}</td>
                                    <td class="font-medium">{{ pago.plan_pago?.proyecto?.nombre || '-' }}</td>
                                    <td>{{ pago.fecha || '-' }}</td>
                                    <td>Bs. {{ Number(pago.total || 0).toFixed(2) }}</td>
                                    <td>{{ pago.metodo_pago || '-' }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'badge-warning': pago.estado === 'pendiente',
                                            'badge-success': pago.estado === 'completado',
                                            'badge-error': pago.estado === 'cancelado'
                                        }">
                                            {{ pago.estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <Link 
                                                :href="route('pagos.show', pago.id)" 
                                                class="text-primary hover:underline"
                                                style="color: var(--theme-primary)"
                                            >
                                                Ver
                                            </Link>
                                            <Link 
                                                :href="route('pagos.destroy', pago.id)" 
                                                method="delete"
                                                as="button"
                                                class="text-error hover:underline"
                                                style="color: var(--theme-error)"
                                            >
                                                Eliminar
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!pagos.data || pagos.data.length === 0">
                                    <td colspan="7" class="text-center py-8" style="color: var(--theme-text-secondary)">
                                        No hay pagos registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div v-if="pagos.links" class="mt-6 flex justify-between items-center">
                        <div style="color: var(--theme-text-secondary); font-size: var(--font-size-sm)">
                            Mostrando {{ pagos.from }} a {{ pagos.to }} de {{ pagos.total }} resultados
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in pagos.links"
                                :key="link.label"
                                :href="link.url"
                                :class="['btn', link.active ? 'btn-primary' : 'btn-secondary']"
                                v-html="link.label"
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
