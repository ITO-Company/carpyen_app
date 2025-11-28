<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    productos: Object,
});

const searchQuery = ref("");
const stockForm = ref({
    visible: false,
    productId: null,
    cantidad: 1,
    action: null, // 'agregar' o 'disminuir'
});

const filteredProductos = computed(() => {
    if (!searchQuery.value) {
        return props.productos;
    }
    
    const query = searchQuery.value.toLowerCase();
    const filtered = props.productos.data.filter(producto => {
        return (
            (producto.nombre && producto.nombre.toLowerCase().includes(query)) ||
            (producto.tipo && producto.tipo.toLowerCase().includes(query)) ||
            (producto.unidad_medida && producto.unidad_medida.toLowerCase().includes(query))
        );
    });
    
    return {
        ...props.productos,
        data: filtered
    };
});

const paginationLabel = (label) => {
    if (label.includes("Previous")) return "← Anterior";
    if (label.includes("Next")) return "Siguiente →";
    return label;
};

const abrirModalStock = (productId, action) => {
    stockForm.value.productId = productId;
    stockForm.value.action = action;
    stockForm.value.cantidad = 1;
    stockForm.value.visible = true;
};

const cerrarModalStock = () => {
    stockForm.value.visible = false;
    stockForm.value.productId = null;
    stockForm.value.cantidad = 1;
    stockForm.value.action = null;
};

const enviarStock = () => {
    if (!stockForm.value.cantidad || stockForm.value.cantidad < 1) {
        alert('Por favor ingresa una cantidad válida');
        return;
    }

    const routeName = stockForm.value.action === 'agregar' 
        ? 'productos.agregarStock' 
        : 'productos.disminuirStock';
    
    const form = useForm({
        cantidad: parseInt(stockForm.value.cantidad)
    });

    form.post(route(routeName, stockForm.value.productId), {
        onSuccess: () => {
            cerrarModalStock();
            window.location.reload();
        },
        onError: (errors) => {
            alert('Error al actualizar stock');
            console.error(errors);
        }
    });
};
</script>

<template>
    <Head title="Productos" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl leading-tight"
                style="color: var(--theme-text-primary)"
            >
                Gestión de Productos
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
                            placeholder="Buscar productos..."
                            class="input search-input"
                            style="flex: 1; max-width: 500px"
                        />
                        <Link :href="route('productos.create')" class="btn btn-primary btn-sm">
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
                            Nuevo Producto
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Unidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="producto in filteredProductos.data"
                                    :key="producto.id"
                                >
                                    <td>{{ producto.id }}</td>
                                    <td class="font-medium">
                                        {{ producto.nombre || "-" }}
                                    </td>
                                    <td>{{ producto.tipo || "-" }}</td>
                                    <td>{{ producto.unidad_medida || "-" }}</td>
                                    <td>
                                        Bs.
                                        {{
                                            Number(
                                                producto.precio_unitario || 0
                                            ).toFixed(2)
                                        }}
                                    </td>
                                    <td>
                                        <span
                                            :class="[
                                                'badge',
                                                (producto.stock || 0) < 10
                                                    ? 'badge-warning'
                                                    : 'badge-success',
                                            ]"
                                        >
                                            {{ producto.stock || 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2" style="flex-wrap: wrap;">
                                            <Link
                                                :href="
                                                    route(
                                                        'productos.edit',
                                                        producto.id
                                                    )
                                                "
                                                class="text-primary hover:underline"
                                                style="
                                                    color: var(--theme-primary);
                                                "
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                @click="abrirModalStock(producto.id, 'agregar')"
                                                class="text-primary hover:underline"
                                                style="
                                                    color: var(--theme-success);
                                                    background: none;
                                                    border: none;
                                                    cursor: pointer;
                                                "
                                            >
                                                +Stock
                                            </button>
                                            <button
                                                @click="abrirModalStock(producto.id, 'disminuir')"
                                                class="text-primary hover:underline"
                                                style="
                                                    color: var(--theme-warning);
                                                    background: none;
                                                    border: none;
                                                    cursor: pointer;
                                                "
                                            >
                                                -Stock
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="
                                        !filteredProductos.data ||
                                        filteredProductos.data.length === 0
                                    "
                                >
                                    <td
                                        colspan="7"
                                        class="text-center py-8"
                                        style="
                                            color: var(--theme-text-secondary);
                                        "
                                    >
                                        No hay productos registrados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contador de resultados -->
                    <div
                        v-if="filteredProductos.data && filteredProductos.data.length > 0"
                        class="mt-4 text-sm"
                        style="color: var(--theme-text-secondary)"
                    >
                        Mostrando {{ filteredProductos.data.length }} resultado(s)
                    </div>
                </div>

                <!-- Modal para Agregar/Disminuir Stock -->
                <div v-if="stockForm.visible" class="modal-overlay" @click.self="cerrarModalStock">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">
                                {{ stockForm.action === 'agregar' ? '➕ Agregar Stock' : '➖ Disminuir Stock' }}
                            </h3>
                            <button @click="cerrarModalStock" class="modal-close">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">Cantidad</label>
                                <input
                                    v-model.number="stockForm.cantidad"
                                    type="number"
                                    min="1"
                                    class="input theme-input"
                                    placeholder="Ingrese la cantidad"
                                />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="cerrarModalStock" class="btn btn-secondary">
                                Cancelar
                            </button>
                            <button @click="enviarStock" class="btn btn-primary">
                                {{ stockForm.action === 'agregar' ? 'Agregar' : 'Disminuir' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background-color: var(--theme-bg-primary);
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-6);
    border-bottom: 1px solid var(--theme-border);
}

.modal-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--theme-text-primary);
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--theme-text-secondary);
}

.modal-body {
    padding: var(--spacing-6);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-3);
    padding: var(--spacing-6);
    border-top: 1px solid var(--theme-border);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-2);
}

.form-label {
    font-size: var(--font-size-sm);
    font-weight: 500;
    color: var(--theme-text-primary);
}

.theme-input {
    background-color: var(--theme-bg-primary) !important;
    color: var(--theme-text-primary) !important;
    border: 1px solid var(--theme-border) !important;
    padding: var(--spacing-2) var(--spacing-3);
    border-radius: 6px;
}

.theme-input:focus {
    border-color: var(--theme-primary) !important;
    outline: none;
    box-shadow: 0 0 0 3px var(--theme-primary-alpha) !important;
}

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
