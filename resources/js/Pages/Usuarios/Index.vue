<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    usuarios: Object,
});

const searchQuery = ref("");

const filteredUsuarios = computed(() => {
    if (!searchQuery.value) {
        return props.usuarios;
    }
    
    const query = searchQuery.value.toLowerCase();
    const filtered = props.usuarios.data.filter(usuario => {
        return (
            (usuario.nombre && usuario.nombre.toLowerCase().includes(query)) ||
            (usuario.email && usuario.email.toLowerCase().includes(query)) ||
            (usuario.rol && usuario.rol.toLowerCase().includes(query))
        );
    });
    
    return {
        ...props.usuarios,
        data: filtered
    };
});

const paginationLabel = (label) => {
    if (label.includes("Previous")) return "← Anterior";
    if (label.includes("Next")) return "Siguiente →";
    return label;
};
</script>

<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl leading-tight"
                style="color: var(--theme-text-primary)"
            >
                Gestión de Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
                    <!-- Barra de búsqueda con botón crear -->
                    <div class="mb-6 flex justify-between items-center gap-4">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar usuarios..."
                            class="input search-input"
                            style="flex: 1; max-width: 500px"
                        />
                        <Link :href="route('usuarios.create')" class="btn btn-primary">
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
                            Nuevo Usuario
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="usuario in filteredUsuarios.data"
                                    :key="usuario.id"
                                >
                                    <td>{{ usuario.id }}</td>
                                    <td class="font-medium">
                                        {{ usuario.nombre || "-" }}
                                    </td>
                                    <td>{{ usuario.email || "-" }}</td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="{
                                                'badge-error':
                                                    usuario.rol === 'ADMIN',
                                                'badge-success':
                                                    usuario.rol === 'VENDEDOR',
                                                'badge-warning':
                                                    usuario.rol ===
                                                    'JEFE_INSTALADOR',
                                                'badge-info':
                                                    usuario.rol === 'DISEÑADOR',
                                                'badge-secondary':
                                                    usuario.rol ===
                                                    'INSTALADOR',
                                            }"
                                        >
                                            {{ usuario.rol || "Sin rol" }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'usuarios.edit',
                                                        usuario.id
                                                    )
                                                "
                                                class="text-primary hover:underline"
                                                style="
                                                    color: var(--theme-primary);
                                                "
                                            >
                                                Editar
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'usuarios.destroy',
                                                        usuario.id
                                                    )
                                                "
                                                method="delete"
                                                as="button"
                                                class="text-error hover:underline"
                                                style="
                                                    color: var(--theme-error);
                                                "
                                            >
                                                Eliminar
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        !filteredUsuarios.data ||
                                        filteredUsuarios.data.length === 0
                                    "
                                >
                                    <td
                                        colspan="5"
                                        class="text-center py-8"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        No hay usuarios registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contador de resultados -->
                    <div
                        v-if="filteredUsuarios.data && filteredUsuarios.data.length > 0"
                        class="mt-4 text-sm"
                        style="color: var(--theme-text-secondary)"
                    >
                        Mostrando {{ filteredUsuarios.data.length }} resultado(s)
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

.gap-4 {
    gap: var(--spacing-4);
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.mt-4 {
    margin-top: var(--spacing-4);
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

.text-sm {
    font-size: var(--font-size-sm);
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

/* Mejoras visuales para la tabla */
.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Adaptación del buscador al tema */
.search-input {
    background-color: var(--theme-bg-primary) !important;
    color: var(--theme-text-primary) !important;
    border: 1px solid var(--theme-border) !important;
}

.search-input::placeholder {
    color: var(--theme-text-secondary) !important;
    opacity: 0.7;
}

.search-input:focus {
    border-color: var(--theme-primary) !important;
    outline: none;
    box-shadow: 0 0 0 3px var(--theme-primary-alpha) !important;
}
</style>
