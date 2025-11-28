<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import ThemeSettings from '@/Components/ThemeSettings.vue';

const showSidebar = ref(true);
const page = usePage();

// Get cliente from page props, with fallback
const cliente = computed(() => {
    return page.props.cliente || page.props.auth?.cliente || { nombre: 'Cliente' };
});

const menuItems = [
    {
        nombre: 'Mis Proyectos',
        icono: 'folder',
        ruta: route('cliente.dashboard'),
    },
];

const logout = () => {
    router.post(route('cliente.logout'));
};
</script>

<template>
    <div class="app-container">
        <!-- Sidebar -->
        <aside :class="['sidebar', { 'sidebar-collapsed': !showSidebar }]">
            <div class="sidebar-header">
                <div class="logo-link">
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
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span v-if="showSidebar">Portal de Clientes</span>
                    </div>
                </div>
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
                    :class="['nav-item', { active: $page.url && $page.url.startsWith(item.ruta) }]"
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
                            v-if="item.icono === 'folder'"
                            d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"
                        ></path>
                    </svg>
                    <span v-if="showSidebar">{{ item.nombre }}</span>
                </Link>
            </nav>

            <div class="sidebar-footer" v-if="showSidebar">
                <button @click="logout" class="logout-btn">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Cerrar Sesión</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="top-bar-left">
                    <slot name="header">
                        <h1 class="page-title">Portal de Clientes</h1>
                    </slot>
                </div>
                <div class="top-bar-right">
                    <div class="user-menu">
                        <div class="user-info">
                            <span class="user-name">{{ cliente.nombre }}</span>
                            <span class="user-role">CLIENTE</span>
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
    font-size: var(--font-size-lg);
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

.logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: var(--spacing-3);
    padding: var(--spacing-3) var(--spacing-4);
    background: none;
    border: 1px solid var(--theme-border);
    color: var(--theme-text-secondary);
    border-radius: var(--border-radius-md);
    cursor: pointer;
    transition: all var(--transition-fast);
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.logout-btn:hover {
    background-color: var(--theme-danger);
    color: white;
    border-color: var(--theme-danger);
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
