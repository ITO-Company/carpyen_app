<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Reportes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="page-title">Reportes</h2>
        </template>

        <div class="reports-container">
            <div class="reports-intro">
                <h3 class="intro-title">Centro de Reportes</h3>
                <p class="intro-description">
                    Accede a reportes detallados sobre proyectos, clientes,
                    ventas e inventario
                </p>
            </div>

            <div class="reports-grid">
                <!-- Reporte de Estadísticas -->
                <Link
                    :href="route('reportes.estadisticas')"
                    class="report-card card-primary"
                >
                    <div class="report-icon">
                        <svg
                            width="48"
                            height="48"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <line x1="12" y1="20" x2="12" y2="10" />
                            <line x1="18" y1="20" x2="18" y2="4" />
                            <line x1="6" y1="20" x2="6" y2="16" />
                        </svg>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">Estadísticas Generales</h3>
                        <p class="report-description">
                            Vista general del negocio con gráficos y métricas
                            clave
                        </p>
                    </div>
                    <div class="report-arrow">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </div>
                </Link>

                <!-- Reporte de Proyectos -->
                <Link
                    :href="route('reportes.proyectos')"
                    class="report-card card-success"
                >
                    <div class="report-icon">
                        <svg
                            width="48"
                            height="48"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"
                            />
                        </svg>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">Proyectos</h3>
                        <p class="report-description">
                            Listado completo de proyectos con filtros por estado
                            y fecha
                        </p>
                    </div>
                    <div class="report-arrow">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </div>
                </Link>

                <!-- Reporte de Clientes -->
                <Link
                    :href="route('reportes.clientes')"
                    class="report-card card-info"
                >
                    <div class="report-icon">
                        <svg
                            width="48"
                            height="48"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                            />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">Clientes</h3>
                        <p class="report-description">
                            Información detallada de clientes y su historial
                        </p>
                    </div>
                    <div class="report-arrow">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </div>
                </Link>

                <!-- Reporte de Ventas -->
                <Link
                    :href="route('reportes.ventas')"
                    class="report-card card-warning"
                >
                    <div class="report-icon">
                        <svg
                            width="48"
                            height="48"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <line x1="12" y1="1" x2="12" y2="23" />
                            <path
                                d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                            />
                        </svg>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">Ventas y Pagos</h3>
                        <p class="report-description">
                            Análisis de ingresos, pagos completados y pendientes
                        </p>
                    </div>
                    <div class="report-arrow">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </div>
                </Link>

                <!-- Reporte de Inventario -->
                <Link
                    :href="route('reportes.inventario')"
                    class="report-card card-danger"
                >
                    <div class="report-icon">
                        <svg
                            width="48"
                            height="48"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
                            />
                        </svg>
                    </div>
                    <div class="report-content">
                        <h3 class="report-title">Inventario</h3>
                        <p class="report-description">
                            Control de stock, productos y alertas de inventario
                        </p>
                    </div>
                    <div class="report-arrow">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </div>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.reports-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.reports-intro {
    margin-bottom: 3rem;
    text-align: center;
}

.intro-title {
    font-size: var(--font-size-3xl);
    font-weight: 800;
    color: var(--theme-text-primary);
    margin-bottom: 0.5rem;
}

.intro-description {
    font-size: var(--font-size-lg);
    color: var(--theme-text-secondary);
}

.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.report-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 2rem;
    background: var(--theme-bg-primary);
    border: 2px solid var(--theme-border);
    border-radius: var(--border-radius-xl);
    text-decoration: none;
    transition: all var(--transition-normal);
    cursor: pointer;
}

.report-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-2xl);
}

.card-primary {
    border-color: transparent;
    background: linear-gradient(
        135deg,
        rgba(167, 139, 250, 0.1) 0%,
        rgba(129, 140, 248, 0.1) 100%
    );
}

.card-primary:hover {
    border-color: #a78bfa;
    box-shadow: 0 20px 40px rgba(167, 139, 250, 0.3);
}

.card-success {
    border-color: transparent;
    background: linear-gradient(
        135deg,
        rgba(16, 185, 129, 0.1) 0%,
        rgba(5, 150, 105, 0.1) 100%
    );
}

.card-success:hover {
    border-color: #10b981;
    box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3);
}

.card-info {
    border-color: transparent;
    background: linear-gradient(
        135deg,
        rgba(59, 130, 246, 0.1) 0%,
        rgba(37, 99, 235, 0.1) 100%
    );
}

.card-info:hover {
    border-color: #3b82f6;
    box-shadow: 0 20px 40px rgba(59, 130, 246, 0.3);
}

.card-warning {
    border-color: transparent;
    background: linear-gradient(
        135deg,
        rgba(245, 158, 11, 0.1) 0%,
        rgba(217, 119, 6, 0.1) 100%
    );
}

.card-warning:hover {
    border-color: #f59e0b;
    box-shadow: 0 20px 40px rgba(245, 158, 11, 0.3);
}

.card-danger {
    border-color: transparent;
    background: linear-gradient(
        135deg,
        rgba(239, 68, 68, 0.1) 0%,
        rgba(220, 38, 38, 0.1) 100%
    );
}

.card-danger:hover {
    border-color: #ef4444;
    box-shadow: 0 20px 40px rgba(239, 68, 68, 0.3);
}

.report-icon {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--border-radius-lg);
}

.card-primary .report-icon {
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    color: white;
}

.card-success .report-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.card-info .report-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.card-warning .report-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.card-danger .report-icon {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.report-content {
    flex: 1;
}

.report-title {
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--theme-text-primary);
    margin-bottom: 0.5rem;
}

.report-description {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    line-height: 1.5;
}

.report-arrow {
    flex-shrink: 0;
    color: var(--theme-text-tertiary);
    transition: all var(--transition-fast);
}

.report-card:hover .report-arrow {
    transform: translateX(4px);
    color: var(--theme-text-primary);
}

/* Responsive */
@media (max-width: 768px) {
    .reports-container {
        padding: 1rem;
    }

    .reports-grid {
        grid-template-columns: 1fr;
    }

    .report-card {
        flex-direction: column;
        text-align: center;
    }

    .report-arrow {
        transform: rotate(90deg);
    }

    .report-card:hover .report-arrow {
        transform: rotate(90deg) translateX(4px);
    }
}
</style>
