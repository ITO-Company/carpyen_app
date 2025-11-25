<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    usuarios: Object,
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
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl leading-tight"
                    style="color: var(--theme-text-primary)"
                >
                    Gestión de Usuarios
                </h2>
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
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card fade-in">
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
                                    v-for="usuario in usuarios.data"
                                    :key="usuario.id"
                                >
                                    <td>{{ usuario.id }}</td>
                                    <td class="font-medium">
                                        {{ usuario.name || "-" }}
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
                                        !usuarios.data ||
                                        usuarios.data.length === 0
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

                    <!-- Paginación -->
                    <div
                        v-if="usuarios.links"
                        class="mt-6 flex justify-between items-center"
                    >
                        <div
                            style="
                                color: var(--theme-text-secondary);
                                font-size: var(--font-size-sm);
                            "
                        >
                            Mostrando {{ usuarios.from }} a {{ usuarios.to }} de
                            {{ usuarios.total }} resultados
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in usuarios.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'btn',
                                    link.active
                                        ? 'btn-primary'
                                        : 'btn-secondary',
                                    !link.url && 'btn-disabled',
                                ]"
                                @click.prevent="
                                    link.url && $inertia.visit(link.url)
                                "
                                v-text="paginationLabel(link.label)"
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
