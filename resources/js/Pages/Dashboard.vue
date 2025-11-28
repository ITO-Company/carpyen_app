<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    proyectosPorEstado: {
        type: Object,
        default: () => ({}),
    },
    proyectosRecientes: {
        type: Array,
        default: () => [],
    },
    ingresosPorMes: {
        type: Array,
        default: () => [],
    },
    productosPopulares: {
        type: Array,
        default: () => [],
    },
});

const estadosChart = ref(null);
const ingresosChart = ref(null);

const formatCurrency = (value) => {
    return new Intl.NumberFormat("es-BO", {
        style: "currency",
        currency: "BOB",
    }).format(value || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const getEstadoBadge = (estado) => {
    const badges = {
        pendiente: "badge-warning",
        en_proceso: "badge-info",
        completado: "badge-success",
        cancelado: "badge-danger",
    };
    return badges[estado] || "badge-secondary";
};

const getEstadoLabel = (estado) => {
    const labels = {
        pendiente: "Pendiente",
        en_proceso: "En Proceso",
        completado: "Completado",
        cancelado: "Cancelado",
    };
    return labels[estado] || estado;
};

const mesesNombres = [
    "Ene",
    "Feb",
    "Mar",
    "Abr",
    "May",
    "Jun",
    "Jul",
    "Ago",
    "Sep",
    "Oct",
    "Nov",
    "Dic",
];

// Computed property for greeting based on time of day
const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return "Buenos días";
    if (hour < 19) return "Buenas tardes";
    return "Buenas noches";
});

const currentDate = computed(() => {
    return new Date().toLocaleDateString("es-BO", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

onMounted(() => {
    // Gráfico de proyectos por estado
    if (estadosChart.value) {
        const ctx = estadosChart.value.getContext("2d");
        new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: Object.keys(props.proyectosPorEstado).map((estado) =>
                    getEstadoLabel(estado)
                ),
                datasets: [
                    {
                        data: Object.values(props.proyectosPorEstado),
                        backgroundColor: [
                            "#fbbf24",
                            "#3b82f6",
                            "#10b981",
                            "#ef4444",
                        ],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            color: "#e2e8f0",
                            padding: 15,
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
            },
        });
    }

    // Gráfico de ingresos por mes
    if (ingresosChart.value && props.ingresosPorMes.length > 0) {
        const ctx = ingresosChart.value.getContext("2d");
        new Chart(ctx, {
            type: "line",
            data: {
                labels: props.ingresosPorMes.map(
                    (item) => mesesNombres[item.mes - 1]
                ),
                datasets: [
                    {
                        label: "Ingresos",
                        data: props.ingresosPorMes.map((item) => item.total),
                        borderColor: "#a78bfa",
                        backgroundColor: "rgba(167, 139, 250, 0.1)",
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: "#a78bfa",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                        pointRadius: 5,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: "#94a3b8",
                            callback: function (value) {
                                return "Bs " + value.toLocaleString();
                            },
                        },
                        grid: {
                            color: "rgba(148, 163, 184, 0.1)",
                        },
                    },
                    x: {
                        ticks: {
                            color: "#94a3b8",
                        },
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="page-title">Dashboard</h2>
        </template>

        <div class="dashboard-container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1 class="welcome-title">
                            {{ greeting }}, {{ $page.props.auth.user.nombre }}!
                        </h1>
                        <p class="welcome-subtitle">{{ currentDate }}</p>
                    </div>
                    <div class="welcome-stats">
                        <div class="quick-stat">
                            <div class="quick-stat-value">
                                {{ stats.proyectos_activos }}
                            </div>
                            <div class="quick-stat-label">
                                Proyectos Activos
                            </div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-value">
                                {{ formatCurrency(stats.total_ingresos) }}
                            </div>
                            <div class="quick-stat-label">Ingresos Totales</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <!-- Proyectos -->
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <svg
                            width="32"
                            height="32"
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
                    <div class="stat-content">
                        <div class="stat-label">Total Proyectos</div>
                        <div class="stat-value">
                            {{ stats.total_proyectos }}
                        </div>
                        <div class="stat-detail">
                            {{ stats.proyectos_activos }} activos
                        </div>
                    </div>
                </div>

                <!-- Clientes -->
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <svg
                            width="32"
                            height="32"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Clientes</div>
                        <div class="stat-value">{{ stats.total_clientes }}</div>
                        <div class="stat-detail">Registrados</div>
                    </div>
                </div>

                <!-- Ingresos -->
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <svg
                            width="32"
                            height="32"
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
                    <div class="stat-content">
                        <div class="stat-label">Ingresos Totales</div>
                        <div class="stat-value">
                            {{ formatCurrency(stats.total_ingresos) }}
                        </div>
                        <div class="stat-detail">
                            {{ formatCurrency(stats.pagos_pendientes) }}
                            pendientes
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <svg
                            width="32"
                            height="32"
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
                    <div class="stat-content">
                        <div class="stat-label">Productos</div>
                        <div class="stat-value">
                            {{ stats.total_productos }}
                        </div>
                        <div class="stat-detail">
                            {{ stats.productos_stock_bajo }} stock bajo
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-grid">
                <!-- Proyectos por Estado -->
                <div class="chart-card">
                    <h3 class="chart-title">Proyectos por Estado</h3>
                    <div class="chart-container">
                        <canvas ref="estadosChart"></canvas>
                    </div>
                </div>

                <!-- Ingresos Mensuales -->
                <div class="chart-card">
                    <h3 class="chart-title">Ingresos Últimos 6 Meses</h3>
                    <div class="chart-container">
                        <canvas ref="ingresosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables Row -->
            <div class="tables-grid">
                <!-- Proyectos Recientes -->
                <div class="table-card">
                    <h3 class="table-title">Proyectos Recientes</h3>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Proyecto</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="proyecto in proyectosRecientes"
                                    :key="proyecto.id"
                                >
                                    <td>
                                        <div class="project-name">
                                            {{ proyecto.nombre }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="client-name">
                                            {{ proyecto.cliente?.nombre }}
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            :class="[
                                                'badge',
                                                getEstadoBadge(proyecto.estado),
                                            ]"
                                        >
                                            {{ getEstadoLabel(proyecto.estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-text">
                                            {{ formatDate(proyecto.created_at) }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="proyectosRecientes.length === 0">
                                    <td colspan="4" class="empty-message">
                                        No hay proyectos recientes
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Productos Populares -->
                <div class="table-card">
                    <h3 class="table-title">Productos Más Usados</h3>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Proyectos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="producto in productosPopulares"
                                    :key="producto.id"
                                >
                                    <td>
                                        <div class="product-name">
                                            {{ producto.nombre }}
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            :class="[
                                                'stock-badge',
                                                producto.stock < 10
                                                    ? 'stock-low'
                                                    : 'stock-ok',
                                            ]"
                                        >
                                            {{ producto.stock }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="usage-count">
                                            {{ producto.proyectos_count || 0 }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="productosPopulares.length === 0">
                                    <td colspan="3" class="empty-message">
                                        No hay datos de productos
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="additional-stats">
                <div class="stat-box">
                    <div class="stat-box-label">Cotizaciones</div>
                    <div class="stat-box-value">
                        {{ stats.total_cotizaciones }}
                    </div>
                    <div class="stat-box-detail">
                        {{ stats.cotizaciones_pendientes }} pendientes
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-label">Diseños</div>
                    <div class="stat-box-value">{{ stats.total_disenos }}</div>
                    <div class="stat-box-detail">
                        {{ stats.disenos_aprobados }} aprobados
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-label">Proyectos Completados</div>
                    <div class="stat-box-value">
                        {{ stats.proyectos_completados }}
                    </div>
                    <div class="stat-box-detail">Este año</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-title {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--theme-text-primary);
}

/* Welcome Section */
.welcome-section {
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    border-radius: var(--border-radius-xl);
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
}

.welcome-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.welcome-text {
    flex: 1;
}

.welcome-title {
    font-size: var(--font-size-3xl);
    font-weight: 800;
    color: white;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.welcome-subtitle {
    font-size: var(--font-size-base);
    color: rgba(255, 255, 255, 0.9);
    text-transform: capitalize;
}

.welcome-stats {
    display: flex;
    gap: 2rem;
}

.quick-stat {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 1.5rem;
    border-radius: var(--border-radius-lg);
    min-width: 150px;
    text-align: center;
}

.quick-stat-value {
    font-size: var(--font-size-2xl);
    font-weight: 800;
    color: white;
    margin-bottom: 0.5rem;
}

.quick-stat-label {
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.9);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--theme-bg-primary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
    transition: all var(--transition-normal);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: var(--border-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-primary .stat-icon {
    background: linear-gradient(135deg, #a78bfa 0%, #818cf8 100%);
    color: white;
}

.stat-success .stat-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.stat-info .stat-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.stat-warning .stat-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: var(--font-size-3xl);
    font-weight: 800;
    color: var(--theme-text-primary);
    margin-bottom: 0.25rem;
}

.stat-detail {
    font-size: var(--font-size-xs);
    color: var(--theme-text-tertiary);
}

/* Charts Grid */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.chart-card {
    background: var(--theme-bg-primary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
}

.chart-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: 1.5rem;
}

.chart-container {
    height: 300px;
    position: relative;
}

/* Tables Grid */
.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.table-card {
    background: var(--theme-bg-primary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
}

.table-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin-bottom: 1rem;
}

.table-container {
    overflow-x: auto;
}

.project-name,
.client-name,
.product-name {
    font-weight: 500;
    color: var(--theme-text-primary);
}

.date-text {
    color: var(--theme-text-secondary);
    font-size: var(--font-size-sm);
}

.stock-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: var(--border-radius-md);
    font-weight: 600;
    font-size: var(--font-size-sm);
}

.stock-low {
    background: rgba(239, 68, 68, 0.1);
    color: var(--theme-danger);
}

.stock-ok {
    background: rgba(16, 185, 129, 0.1);
    color: var(--theme-success);
}

.usage-count {
    font-weight: 600;
    color: var(--theme-primary);
}

.empty-message {
    text-align: center;
    padding: 2rem;
    color: var(--theme-text-tertiary);
    font-style: italic;
}

/* Additional Stats */
.additional-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-box {
    background: var(--theme-bg-secondary);
    border: 1px solid var(--theme-border);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    text-align: center;
}

.stat-box-label {
    font-size: var(--font-size-sm);
    color: var(--theme-text-secondary);
    margin-bottom: 0.5rem;
}

.stat-box-value {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--theme-text-primary);
    margin-bottom: 0.25rem;
}

.stat-box-detail {
    font-size: var(--font-size-xs);
    color: var(--theme-text-tertiary);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }

    .welcome-section {
        padding: 1.5rem;
    }

    .welcome-content {
        flex-direction: column;
        text-align: center;
    }

    .welcome-title {
        font-size: var(--font-size-2xl);
    }

    .welcome-stats {
        flex-direction: column;
        width: 100%;
    }

    .quick-stat {
        width: 100%;
    }

    .stats-grid,
    .charts-grid,
    .tables-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        flex-direction: column;
        text-align: center;
    }

    .stat-icon {
        margin: 0 auto;
    }
}
</style>
