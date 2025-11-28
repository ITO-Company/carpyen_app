<script setup>
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ThemeSettings from "@/Components/ThemeSettings.vue";
import PageCounter from "@/Components/PageCounter.vue";

const showSidebar = ref(true);
const menuItems = ref([]);
const user = usePage().props.auth.user;

// Cargar menú dinámico
onMounted(async () => {
    try {
        // Por ahora usamos un menú estático basado en los roles
        // En producción, esto vendría de la base de datos
        menuItems.value = getMenuForRole(user.rol);
    } catch (error) {
        console.error("Error loading menu:", error);
    }
});

function getMenuForRole(rol) {
    const allMenuItems = [
        {
            nombre: "Dashboard",
            icono: "home",
            ruta: route("dashboard"),
            roles: [
                "ADMIN",
                "VENDEDOR",
                "JEFE_INSTALADOR",
                "DISEÑADOR",
                "INSTALADOR",
            ],
        },
        // ADMIN: Gestión Completa
        {
            nombre: "Usuarios",
            icono: "users",
            ruta: route("usuarios.index"),
            roles: ["ADMIN"],
        },
        // VENDEDOR: Gestión de Clientes y Proyectos
        {
            nombre: "Clientes",
            icono: "user-check",
            ruta: route("clientes.index"),
            roles: ["ADMIN", "VENDEDOR"],
        },
        {
            nombre: "Proyectos",
            icono: "folder",
            ruta: route("proyectos.index"),
            roles: ["ADMIN", "VENDEDOR", "JEFE_INSTALADOR", "INSTALADOR"],
        },
        // JEFE_INSTALADOR: Gestión de Proveedores y Productos
        {
            nombre: "Proveedores",
            icono: "truck",
            ruta: route("proveedores.index"),
            roles: ["ADMIN", "JEFE_INSTALADOR"],
        },
        {
            nombre: "Productos",
            icono: "package",
            ruta: route("productos.index"),
            roles: ["ADMIN", "JEFE_INSTALADOR", "INSTALADOR"],
        },
        // VENDEDOR: Diseños
        {
            nombre: "Diseños",
            icono: "layout",
            ruta: route("disenos.index"),
            roles: ["ADMIN", "VENDEDOR"],
        },
        // JEFE_INSTALADOR: Cronogramas y Tareas
        {
            nombre: "Cronogramas",
            icono: "calendar",
            ruta: "/cronogramas",
            roles: ["ADMIN", "VENDEDOR", "JEFE_INSTALADOR"],
        },
        // INSTALADOR: Mis Tareas
        {
            nombre: "Mis Tareas",
            icono: "check-square",
            ruta: "/mis-tareas",
            roles: ["INSTALADOR"],
        },
        // DISEÑADOR: Mis Diseños
        {
            nombre: "Mis Diseños",
            icono: "palette",
            ruta: "/mis-disenos",
            roles: ["DISEÑADOR"],
        },
        // VENDEDOR: Pagos y Reportes
        {
            nombre: "Pagos",
            icono: "credit-card",
            ruta: "/pagos",
            roles: ["ADMIN", "VENDEDOR"],
        },
        {
            nombre: "Reportes",
            icono: "bar-chart",
            ruta: "/reportes/estadisticas",
            roles: ["ADMIN", "VENDEDOR"],
        },
    ];

    return allMenuItems.filter((item) => item.roles.includes(rol));
}

const getIcon = (iconName) => {
    const icons = {
        home: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
        users: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
        "user-check":
            '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline>',
        folder: '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>',
        package:
            '<line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>',
        truck: '<polyline points="1 4 1 10 15 10 15 4"></polyline><path d="M19 8v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-5h-8"></path><circle cx="5" cy="20" r="2"></circle><circle cx="21" cy="20" r="2"></circle>',
        layout: '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line>',
        calendar:
            '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
        "check-square":
            '<polyline points="6.5 15.5 10 12 16 18 23 11"></polyline><rect x="4" y="3" width="16" height="16" rx="2" ry="2"></rect>',
        palette:
            '<circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle><path d="M12 1v4m8.66 2.34l-2.83 2.83m4 0l-2.83-2.83m-8.66 14.32l2.83-2.83m-4 0l2.83 2.83M1 12h4m14 0h4"></path>',
        archive:
            '<polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line>',
        "credit-card":
            '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line>',
        "bar-chart":
            '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>',
    };
    return icons[iconName] || icons["folder"];
};
</script>

<template>
    <div class="app-container">
        <!-- Sidebar -->
        <aside :class="['sidebar', { 'sidebar-collapsed': !showSidebar }]">
            <div class="sidebar-header">
                <Link :href="route('dashboard')" class="logo-link">
                    <div class="logo">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="32"
                            height="32"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                            ></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span v-if="showSidebar">Carpyen</span>
                    </div>
                </Link>
                <button @click="showSidebar = !showSidebar" class="toggle-btn">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <Link
                    v-for="item in menuItems"
                    :key="item.ruta"
                    :href="item.ruta"
                    :class="[
                        'nav-item',
                        {
                            active:
                                $page.url && $page.url.startsWith(item.ruta),
                        },
                    ]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            v-if="item.icono === 'home'"
                            d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                        ></path>
                        <polyline
                            v-if="item.icono === 'home'"
                            points="9 22 9 12 15 12 15 22"
                        ></polyline>

                        <path
                            v-if="item.icono === 'users'"
                            d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                        ></path>
                        <circle
                            v-if="item.icono === 'users'"
                            cx="9"
                            cy="7"
                            r="4"
                        ></circle>
                        <path
                            v-if="item.icono === 'users'"
                            d="M23 21v-2a4 4 0 0 0-3-3.87"
                        ></path>
                        <path
                            v-if="item.icono === 'users'"
                            d="M16 3.13a4 4 0 0 1 0 7.75"
                        ></path>

                        <path
                            v-if="item.icono === 'user-check'"
                            d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                        ></path>
                        <circle
                            v-if="item.icono === 'user-check'"
                            cx="8.5"
                            cy="7"
                            r="4"
                        ></circle>
                        <polyline
                            v-if="item.icono === 'user-check'"
                            points="17 11 19 13 23 9"
                        ></polyline>

                        <path
                            v-if="item.icono === 'folder'"
                            d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"
                        ></path>

                        <line
                            v-if="item.icono === 'package'"
                            x1="16.5"
                            y1="9.4"
                            x2="7.5"
                            y2="4.21"
                        ></line>
                        <path
                            v-if="item.icono === 'package'"
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
                        ></path>
                        <polyline
                            v-if="item.icono === 'package'"
                            points="3.27 6.96 12 12.01 20.73 6.96"
                        ></polyline>
                        <line
                            v-if="item.icono === 'package'"
                            x1="12"
                            y1="22.08"
                            x2="12"
                            y2="12"
                        ></line>

                        <polyline
                            v-if="item.icono === 'truck'"
                            points="1 4 1 10 15 10 15 4"
                        ></polyline>
                        <path
                            v-if="item.icono === 'truck'"
                            d="M19 8v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-5h-8"
                        ></path>
                        <circle
                            v-if="item.icono === 'truck'"
                            cx="5"
                            cy="20"
                            r="2"
                        ></circle>
                        <circle
                            v-if="item.icono === 'truck'"
                            cx="21"
                            cy="20"
                            r="2"
                        ></circle>

                        <rect
                            v-if="item.icono === 'layout'"
                            x="3"
                            y="3"
                            width="18"
                            height="18"
                            rx="2"
                            ry="2"
                        ></rect>
                        <line
                            v-if="item.icono === 'layout'"
                            x1="3"
                            y1="9"
                            x2="21"
                            y2="9"
                        ></line>
                        <line
                            v-if="item.icono === 'layout'"
                            x1="9"
                            y1="21"
                            x2="9"
                            y2="9"
                        ></line>

                        <rect
                            v-if="item.icono === 'calendar'"
                            x="3"
                            y="4"
                            width="18"
                            height="18"
                            rx="2"
                            ry="2"
                        ></rect>
                        <line
                            v-if="item.icono === 'calendar'"
                            x1="16"
                            y1="2"
                            x2="16"
                            y2="6"
                        ></line>
                        <line
                            v-if="item.icono === 'calendar'"
                            x1="8"
                            y1="2"
                            x2="8"
                            y2="6"
                        ></line>
                        <line
                            v-if="item.icono === 'calendar'"
                            x1="3"
                            y1="10"
                            x2="21"
                            y2="10"
                        ></line>

                        <polyline
                            v-if="item.icono === 'check-square'"
                            points="6.5 15.5 10 12 16 18 23 11"
                        ></polyline>
                        <rect
                            v-if="item.icono === 'check-square'"
                            x="4"
                            y="3"
                            width="16"
                            height="16"
                            rx="2"
                            ry="2"
                        ></rect>

                        <circle
                            v-if="item.icono === 'palette'"
                            cx="12"
                            cy="12"
                            r="10"
                        ></circle>
                        <circle
                            v-if="item.icono === 'palette'"
                            cx="12"
                            cy="12"
                            r="3"
                        ></circle>
                        <path
                            v-if="item.icono === 'palette'"
                            d="M12 1v4m8.66 2.34l-2.83 2.83m4 0l-2.83-2.83m-8.66 14.32l2.83-2.83m-4 0l2.83 2.83M1 12h4m14 0h4"
                        ></path>

                        <polyline
                            v-if="item.icono === 'archive'"
                            points="21 8 21 21 3 21 3 8"
                        ></polyline>
                        <rect
                            v-if="item.icono === 'archive'"
                            x="1"
                            y="3"
                            width="22"
                            height="5"
                        ></rect>
                        <line
                            v-if="item.icono === 'archive'"
                            x1="10"
                            y1="12"
                            x2="14"
                            y2="12"
                        ></line>

                        <rect
                            v-if="item.icono === 'credit-card'"
                            x="1"
                            y="4"
                            width="22"
                            height="16"
                            rx="2"
                            ry="2"
                        ></rect>
                        <line
                            v-if="item.icono === 'credit-card'"
                            x1="1"
                            y1="10"
                            x2="23"
                            y2="10"
                        ></line>

                        <line
                            v-if="item.icono === 'bar-chart'"
                            x1="18"
                            y1="20"
                            x2="18"
                            y2="10"
                        ></line>
                        <line
                            v-if="item.icono === 'bar-chart'"
                            x1="12"
                            y1="20"
                            x2="12"
                            y2="4"
                        ></line>
                        <line
                            v-if="item.icono === 'bar-chart'"
                            x1="6"
                            y1="20"
                            x2="6"
                            y2="14"
                        ></line>
                    </svg>
                    <span v-if="showSidebar">{{ item.nombre }}</span>
                </Link>
            </nav>

            <div class="sidebar-footer" v-if="showSidebar">
                <PageCounter />
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <slot name="header">
                        <h1 class="page-title">Dashboard</h1>
                    </slot>
                </div>
                <div class="top-bar-right">
                    <div class="user-menu">
                        <div class="user-info">
                            <span class="user-name">{{ user.name }}</span>
                            <span class="user-role">{{ user.rol }}</span>
                        </div>
                        <div class="user-actions">
                            <Link
                                :href="route('profile.edit')"
                                class="user-action-btn"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                    ></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="user-action-btn"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                    ></path>
                                    <polyline
                                        points="16 17 21 12 16 7"
                                    ></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                <slot />
            </main>
        </div>

        <!-- Theme Settings (Floating Button) -->
        <ThemeSettings />
    </div>
</template>

<style scoped>
.app-container {
    display: flex;
    min-height: 100vh;
    background-color: var(--theme-bg-secondary);
}

/* Sidebar */
.sidebar {
    width: 260px;
    background-color: var(--theme-bg-primary);
    border-right: 1px solid var(--theme-border);
    display: flex;
    flex-direction: column;
    transition: width var(--transition-base);
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    z-index: 100;
}

.sidebar-collapsed {
    width: 70px;
}

.sidebar-header {
    padding: var(--spacing-6);
    border-bottom: 1px solid var(--theme-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo-link {
    text-decoration: none;
    flex: 1;
}

.logo {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    color: var(--theme-primary);
    font-size: var(--font-size-xl);
    font-weight: 700;
}

.toggle-btn {
    background: none;
    border: none;
    color: var(--theme-text-secondary);
    cursor: pointer;
    padding: var(--spacing-2);
    border-radius: var(--border-radius-md);
    transition: all var(--transition-fast);
}

.toggle-btn:hover {
    background-color: var(--theme-bg-secondary);
    color: var(--theme-text-primary);
}

.sidebar-nav {
    flex: 1;
    padding: var(--spacing-4);
    overflow-y: auto;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    padding: var(--spacing-3) var(--spacing-4);
    color: var(--theme-text-secondary);
    text-decoration: none;
    border-radius: var(--border-radius-md);
    margin-bottom: var(--spacing-2);
    transition: all var(--transition-fast);
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.nav-item:hover {
    background-color: var(--theme-bg-secondary);
    color: var(--theme-text-primary);
}

.nav-item.active {
    background-color: var(--theme-primary);
    color: white;
}

.sidebar-footer {
    padding: var(--spacing-4);
    border-top: 1px solid var(--theme-border);
}

/* Main Content */
.main-content {
    flex: 1;
    margin-left: 260px;
    transition: margin-left var(--transition-base);
    display: flex;
    flex-direction: column;
}

.sidebar-collapsed ~ .main-content {
    margin-left: 70px;
}

.top-bar {
    background-color: var(--theme-bg-primary);
    border-bottom: 1px solid var(--theme-border);
    padding: var(--spacing-4) var(--spacing-6);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 90;
}

.page-title {
    font-size: var(--font-size-2xl);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin: 0;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: var(--spacing-4);
}

.user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.user-name {
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--theme-text-primary);
}

.user-role {
    font-size: var(--font-size-xs);
    color: var(--theme-text-secondary);
    text-transform: uppercase;
}

.user-actions {
    display: flex;
    gap: var(--spacing-2);
}

.user-action-btn {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: 1px solid var(--theme-border);
    color: var(--theme-text-secondary);
    cursor: pointer;
    transition: all var(--transition-fast);
    text-decoration: none;
}

.user-action-btn:hover {
    background-color: var(--theme-bg-secondary);
    color: var(--theme-primary);
    border-color: var(--theme-primary);
}

.page-content {
    flex: 1;
    padding: var(--spacing-6);
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar-collapsed {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
    }
}
</style>
