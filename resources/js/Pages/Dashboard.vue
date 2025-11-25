<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    stats: Object
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                Dashboard - Carpyen
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tarjetas de Estadísticas -->
                <div class="dashboard-grid mb-8">
                    <div class="stat-card fade-in">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--theme-primary), var(--theme-primary-light))">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Total Proyectos</p>
                            <p class="stat-number">{{ stats.total_proyectos }}</p>
                        </div>
                    </div>

                    <div class="stat-card fade-in" style="animation-delay: 0.1s">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--theme-success), #34d399)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Proyectos Activos</p>
                            <p class="stat-number">{{ stats.proyectos_activos }}</p>
                        </div>
                    </div>

                    <div class="stat-card fade-in" style="animation-delay: 0.2s">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--theme-info), #60a5fa)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Total Clientes</p>
                            <p class="stat-number">{{ stats.total_clientes }}</p>
                        </div>
                    </div>

                    <div class="stat-card fade-in" style="animation-delay: 0.3s">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--theme-warning), #fbbf24)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Productos Stock Bajo</p>
                            <p class="stat-number">{{ stats.productos_stock_bajo }}</p>
                        </div>
                    </div>
                </div>

                <!-- Accesos Rápidos -->
                <div class="card fade-in" style="animation-delay: 0.4s">
                    <h3 class="section-title">Accesos Rápidos</h3>
                    <div class="quick-actions-grid">
                        <Link :href="route('clientes.create')" class="quick-action-card">
                            <div class="quick-action-icon" style="background: var(--theme-primary)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                </svg>
                            </div>
                            <span>Nuevo Cliente</span>
                        </Link>

                        <Link :href="route('proyectos.create')" class="quick-action-card">
                            <div class="quick-action-icon" style="background: var(--theme-success)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="12" y1="18" x2="12" y2="12"></line>
                                    <line x1="9" y1="15" x2="15" y2="15"></line>
                                </svg>
                            </div>
                            <span>Nuevo Proyecto</span>
                        </Link>

                        <Link :href="route('productos.create')" class="quick-action-card">
                            <div class="quick-action-icon" style="background: var(--theme-accent)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <span>Nuevo Producto</span>
                        </Link>

                        <Link :href="route('reportes.estadisticas')" class="quick-action-card">
                            <div class="quick-action-icon" style="background: var(--theme-secondary)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                            </div>
                            <span>Ver Reportes</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-6);
}

.stat-card {
    background: var(--theme-bg-primary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-6);
    display: flex;
    align-items: center;
    gap: var(--spacing-4);
    transition: all var(--transition-fast);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: var(--border-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: var(--spacing-1);
}

.stat-number {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--theme-text-primary);
}

.section-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: var(--spacing-6);
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-4);
}

.quick-action-card {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    padding: var(--spacing-4);
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-md);
    text-decoration: none;
    color: var(--theme-text-primary);
    transition: all var(--transition-fast);
}

.quick-action-card:hover {
    background: var(--theme-bg-tertiary);
    transform: translateX(4px);
    box-shadow: var(--shadow-md);
}

.quick-action-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.quick-action-card span {
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.mb-8 {
    margin-bottom: 2rem;
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.max-w-7xl {
    max-width: 80rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
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
