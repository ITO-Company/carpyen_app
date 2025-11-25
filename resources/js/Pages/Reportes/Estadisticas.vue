<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    stats: Object,
    proyectosPorEstado: Array,
    ingresosPorMes: Array
});
</script>

<template>
    <Head title="Estadísticas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Reportes y Estadísticas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Estadísticas Generales -->
                <div class="card fade-in">
                    <h3 class="section-title">Estadísticas Generales</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <p class="stat-label">Total Proyectos</p>
                            <p class="stat-value">{{ stats.total_proyectos }}</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-label">Proyectos Activos</p>
                            <p class="stat-value" style="color: var(--theme-success)">{{ stats.proyectos_activos }}</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-label">Proyectos Completados</p>
                            <p class="stat-value" style="color: var(--theme-primary)">{{ stats.proyectos_completados }}</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-label">Total Clientes</p>
                            <p class="stat-value">{{ stats.total_clientes }}</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-label">Total Productos</p>
                            <p class="stat-value">{{ stats.total_productos }}</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-label">Stock Bajo</p>
                            <p class="stat-value" style="color: var(--theme-warning)">{{ stats.stock_bajo }}</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-label">Pagos Pendientes</p>
                            <p class="stat-value" style="color: var(--theme-error)">{{ stats.pagos_pendientes }}</p>
                        </div>
                        <div class="stat-item">
                            <p class="stat-label">Ingresos del Mes</p>
                            <p class="stat-value" style="color: var(--theme-success)">
                                Bs. {{ Number(stats.ingresos_mes).toFixed(2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Proyectos por Estado -->
                <div class="card fade-in" style="animation-delay: 0.1s">
                    <h3 class="section-title">Proyectos por Estado</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th>Cantidad</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in proyectosPorEstado" :key="item.estado">
                                    <td class="font-medium">{{ item.estado }}</td>
                                    <td>{{ item.total }}</td>
                                    <td>
                                        <div class="progress-bar">
                                            <div 
                                                class="progress-fill" 
                                                :style="{ width: (item.total / stats.total_proyectos * 100) + '%' }"
                                            ></div>
                                        </div>
                                        <span class="percentage">
                                            {{ ((item.total / stats.total_proyectos) * 100).toFixed(1) }}%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ingresos por Mes -->
                <div class="card fade-in" style="animation-delay: 0.2s">
                    <h3 class="section-title">Ingresos Últimos 6 Meses</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Total Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in ingresosPorMes" :key="item.mes">
                                    <td class="font-medium">{{ item.mes }}</td>
                                    <td style="color: var(--theme-success)">
                                        Bs. {{ Number(item.total).toFixed(2) }}
                                    </td>
                                </tr>
                                <tr v-if="!ingresosPorMes || ingresosPorMes.length === 0">
                                    <td colspan="2" class="text-center py-8" style="color: var(--theme-text-secondary)">
                                        No hay datos de ingresos
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.space-y-6 > * + * {
    margin-top: var(--spacing-6);
}

.section-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: var(--spacing-6);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-6);
}

.stat-item {
    text-align: center;
    padding: var(--spacing-4);
    background: var(--theme-bg-secondary);
    border-radius: var(--border-radius-md);
}

.stat-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-2);
}

.stat-value {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--theme-text-primary);
}

.table-container {
    overflow-x: auto;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: var(--theme-bg-tertiary);
    border-radius: var(--border-radius-full);
    overflow: hidden;
    margin-bottom: var(--spacing-1);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--theme-primary), var(--theme-primary-light));
    transition: width var(--transition-base);
}

.percentage {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
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
</style>
