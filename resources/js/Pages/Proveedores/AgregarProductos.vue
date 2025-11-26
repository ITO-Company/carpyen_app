<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    proveedor: Object,
    productos: Array
});

const items = ref([{
    producto_id: '',
    cantidad: 1,
    precio_unitario: 0,
    es_nuevo: false,
    nombre_nuevo: '',
    tipo_nuevo: '',
    unidad_medida_nueva: 'unidad'
}]);

const form = useForm({
    items: []
});

const erroresVisibles = ref({});
const mostrarErrores = ref(false);

const productosDisponibles = computed(() => {
    return props.productos.map(p => ({
        id: p.id,
        nombre: p.nombre,
        precio: p.precio_unitario
    }));
});

const agregarFila = () => {
    items.value.push({
        producto_id: '',
        cantidad: 1,
        precio_unitario: 0,
        es_nuevo: false,
        nombre_nuevo: '',
        tipo_nuevo: '',
        unidad_medida_nueva: 'unidad'
    });
    // Limpiar errores de validación anterior
    erroresVisibles.value = {};
    mostrarErrores.value = false;
};

const iniciarConExistente = () => {
    items.value = [];
    agregarFila();
};

const iniciarConNuevo = () => {
    items.value = [];
    agregarFila();
};

const eliminarFila = (index) => {
    if (items.value.length > 1) {
        items.value.splice(index, 1);
        // Limpiar errores asociados a esta fila
        delete erroresVisibles.value[`items.${index}`];
    }
};

const obtenerNombreProducto = (productoId) => {
    const producto = props.productos.find(p => p.id == productoId);
    return producto ? producto.nombre : '';
};

const obtenerPrecioProducto = (productoId) => {
    const producto = props.productos.find(p => p.id == productoId);
    return producto ? producto.precio_unitario : 0;
};

const onProductoChange = (index) => {
    if (items.value[index].producto_id) {
        items.value[index].precio_unitario = obtenerPrecioProducto(items.value[index].producto_id);
    }
    // Limpiar error de producto si se selecciona uno
    delete erroresVisibles.value[`items.${index}.producto_id`];
};

const validarFila = (index) => {
    const item = items.value[index];
    const errores = {};

    if (item.es_nuevo) {
        if (!item.nombre_nuevo || item.nombre_nuevo.trim() === '') {
            errores[`items.${index}.nombre_nuevo`] = 'El nombre del producto es obligatorio';
        }
    } else {
        if (!item.producto_id) {
            errores[`items.${index}.producto_id`] = 'Debe seleccionar un producto';
        }
    }

    if (!item.cantidad || item.cantidad < 1) {
        errores[`items.${index}.cantidad`] = 'La cantidad debe ser al menos 1';
    }

    if (item.precio_unitario === '' || item.precio_unitario < 0) {
        errores[`items.${index}.precio_unitario`] = 'El precio no puede ser negativo';
    }

    return errores;
};

const submit = () => {
    mostrarErrores.value = false;
    erroresVisibles.value = {};

    // Validar todas las filas
    let tieneErrores = false;
    items.value.forEach((item, index) => {
        const erroresDestaFila = validarFila(index);
        if (Object.keys(erroresDestaFila).length > 0) {
            tieneErrores = true;
            Object.assign(erroresVisibles.value, erroresDestaFila);
        }
    });

    if (tieneErrores) {
        mostrarErrores.value = true;
        return;
    }

    // Si pasa validación, enviar
    form.items = items.value;
    form.post(route('proveedores.guardarProductos', { id: props.proveedor.id }), {
        onSuccess: () => {
            // El redirect se hace automáticamente desde el controlador
        },
        onError: (errors) => {
            // Los errores del servidor se mostrarán en form.errors
            mostrarErrores.value = true;
            erroresVisibles.value = errors;
        }
    });
};

const calcularTotal = (cantidad, precio) => {
    return (cantidad * precio).toFixed(2);
};

const obtenerErroresFila = (index) => {
    const errores = [];
    Object.entries(erroresVisibles.value).forEach(([key, value]) => {
        if (key.startsWith(`items.${index}.`)) {
            errores.push(value);
        }
    });
    return errores;
};
</script>

<template>
    <Head :title="`Agregar Productos - ${proveedor.nombre}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold text-xl leading-tight" style="color: var(--theme-text-primary)">
                    Agregar Productos
                </h2>
                <p class="text-sm" style="color: var(--theme-text-secondary)">
                    {{ proveedor.nombre }}
                </p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('proveedores.show', { id: proveedor.id })" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Volver al Proveedor
                    </Link>
                </div>

                <!-- Alert de errores generales -->
                <div v-if="form.errors && Object.keys(form.errors).length > 0" style="background-color: var(--theme-error-alpha); border: 1px solid var(--theme-error); border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                    <p style="color: var(--theme-error); font-weight: 600; margin-bottom: 8px;">Errores en la validación:</p>
                    <ul style="color: var(--theme-error); margin: 0; padding-left: 20px;">
                        <li v-for="(error, key) in form.errors" :key="key" style="margin-bottom: 4px;">
                            {{ Array.isArray(error) ? error[0] : error }}
                        </li>
                    </ul>
                </div>

                <!-- Alert de errores locales de validación -->
                <div v-if="mostrarErrores && Object.keys(erroresVisibles).length > 0" style="background-color: var(--theme-warning-alpha); border: 1px solid var(--theme-warning); border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                    <p style="color: var(--theme-warning); font-weight: 600; margin-bottom: 8px;">Por favor corrija los siguientes errores:</p>
                    <ul style="color: var(--theme-warning); margin: 0; padding-left: 20px;">
                        <li v-for="(error, key) in erroresVisibles" :key="key" style="margin-bottom: 4px;">
                            {{ Array.isArray(error) ? error[0] : error }}
                        </li>
                    </ul>
                </div>

                <!-- Formulario principal -->
                <div class="card fade-in">
                    <form @submit.prevent="submit">
                        <!-- Tabla de productos -->
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--theme-border)">
                                        <th style="text-align: left; padding: 12px; color: var(--theme-text-primary); font-weight: 600; width: 40%;">
                                            Producto
                                        </th>
                                        <th style="text-align: center; padding: 12px; color: var(--theme-text-primary); font-weight: 600; width: 15%;">
                                            Cantidad
                                        </th>
                                        <th style="text-align: center; padding: 12px; color: var(--theme-text-primary); font-weight: 600; width: 15%;">
                                            Precio Unitario
                                        </th>
                                        <th style="text-align: right; padding: 12px; color: var(--theme-text-primary); font-weight: 600; width: 15%;">
                                            Total
                                        </th>
                                        <th style="text-align: center; padding: 12px; color: var(--theme-text-primary); font-weight: 600; width: 10%;">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in items" :key="index" :style="{ borderBottom: '1px solid var(--theme-border)', backgroundColor: obtenerErroresFila(index).length > 0 ? 'var(--theme-error-alpha)' : 'transparent' }">
                                        <!-- Producto -->
                                        <td style="padding: 12px;">
                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                <!-- Toggle entre existente y nuevo -->
                                                <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--theme-text-secondary);">
                                                    <input
                                                        v-model="item.es_nuevo"
                                                        type="checkbox"
                                                        style="cursor: pointer;"
                                                    />
                                                    Producto Nuevo
                                                </label>

                                                <!-- Selector de producto existente -->
                                                <select
                                                    v-if="!item.es_nuevo"
                                                    v-model="item.producto_id"
                                                    @change="onProductoChange(index)"
                                                    class="input theme-input"
                                                    :style="{
                                                        width: '100%',
                                                        borderColor: erroresVisibles[`items.${index}.producto_id`] ? 'var(--theme-error)' : 'var(--theme-border)'
                                                    }"
                                                >
                                                    <option value="">Seleccionar producto</option>
                                                    <option v-for="p in productosDisponibles" :key="p.id" :value="p.id">
                                                        {{ p.nombre }}
                                                    </option>
                                                </select>

                                                <!-- Campos para producto nuevo -->
                                                <div v-else style="display: flex; flex-direction: column; gap: 8px;">
                                                    <input
                                                        v-model="item.nombre_nuevo"
                                                        type="text"
                                                        placeholder="Nombre del producto"
                                                        class="input theme-input"
                                                        :style="{
                                                            width: '100%',
                                                            borderColor: erroresVisibles[`items.${index}.nombre_nuevo`] ? 'var(--theme-error)' : 'var(--theme-border)'
                                                        }"
                                                    />
                                                    <input
                                                        v-model="item.tipo_nuevo"
                                                        type="text"
                                                        placeholder="Tipo (ej: Madera, Metal)"
                                                        class="input theme-input"
                                                        style="width: 100%;"
                                                    />
                                                    <select
                                                        v-model="item.unidad_medida_nueva"
                                                        class="input theme-input"
                                                        style="width: 100%;"
                                                    >
                                                        <option value="unidad">Unidad</option>
                                                        <option value="metro">Metro</option>
                                                        <option value="kilo">Kilo</option>
                                                        <option value="litro">Litro</option>
                                                    </select>
                                                </div>

                                                <!-- Mostrar errores de esta fila -->
                                                <div v-if="obtenerErroresFila(index).length > 0" style="margin-top: 8px;">
                                                    <div v-for="(error, idx) in obtenerErroresFila(index)" :key="idx" style="color: var(--theme-error); font-size: 12px; margin-bottom: 4px;">
                                                        ⚠️ {{ error }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Cantidad -->
                                        <td style="padding: 12px; text-align: center;">
                                            <input
                                                v-model.number="item.cantidad"
                                                type="number"
                                                min="1"
                                                class="input theme-input"
                                                :style="{
                                                    width: '100%',
                                                    textAlign: 'center',
                                                    borderColor: erroresVisibles[`items.${index}.cantidad`] ? 'var(--theme-error)' : 'var(--theme-border)'
                                                }"
                                            />
                                        </td>

                                        <!-- Precio Unitario -->
                                        <td style="padding: 12px; text-align: center;">
                                            <input
                                                v-model.number="item.precio_unitario"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                class="input theme-input"
                                                :style="{
                                                    width: '100%',
                                                    textAlign: 'center',
                                                    borderColor: erroresVisibles[`items.${index}.precio_unitario`] ? 'var(--theme-error)' : 'var(--theme-border)'
                                                }"
                                            />
                                        </td>

                                        <!-- Total -->
                                        <td style="padding: 12px; text-align: right; color: var(--theme-text-primary); font-weight: 600;">
                                            Bs. {{ calcularTotal(item.cantidad, item.precio_unitario) }}
                                        </td>

                                        <!-- Acciones -->
                                        <td style="padding: 12px; text-align: center;">
                                            <button
                                                v-if="items.length > 1"
                                                @click="eliminarFila(index)"
                                                type="button"
                                                class="btn-icon btn-icon-delete"
                                                title="Eliminar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón agregar fila -->
                        <div class="mb-6">
                            <button
                                @click="agregarFila"
                                type="button"
                                class="btn btn-secondary"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Agregar Otra Fila
                            </button>
                        </div>

                        <!-- Resumen de totales -->
                        <div class="mb-6 p-4" style="background-color: var(--theme-bg-secondary); border-radius: 8px;">
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                                <div>
                                    <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">Total Items</p>
                                    <p class="text-lg font-semibold" style="color: var(--theme-text-primary);">{{ items.filter(i => (i.es_nuevo ? i.nombre_nuevo : i.producto_id)).length }}</p>
                                </div>
                                <div>
                                    <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">Cantidad Total</p>
                                    <p class="text-lg font-semibold" style="color: var(--theme-text-primary);">
                                        {{ items.filter(i => (i.es_nuevo ? i.nombre_nuevo : i.producto_id)).reduce((sum, i) => sum + i.cantidad, 0) }}
                                    </p>
                                </div>
                                <div>
                                    <p style="color: var(--theme-text-secondary); font-size: 12px; text-transform: uppercase;">Valor Total</p>
                                    <p class="text-lg font-semibold" style="color: var(--theme-text-primary);">
                                        Bs. {{ items.filter(i => (i.es_nuevo ? i.nombre_nuevo : i.producto_id)).reduce((sum, i) => sum + (i.cantidad * i.precio_unitario), 0).toFixed(2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="form-actions">
                            <Link :href="route('proveedores.show', { id: proveedor.id })" class="btn btn-secondary">
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Productos</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-3);
    padding-top: var(--spacing-6);
    border-top: 1px solid var(--theme-border);
}

.theme-input {
    background-color: var(--theme-bg-primary) !important;
    color: var(--theme-text-primary) !important;
    border: 1px solid var(--theme-border) !important;
    padding: var(--spacing-2) var(--spacing-3);
    border-radius: 6px;
    font-size: 14px;
}

.theme-input::placeholder {
    color: var(--theme-text-secondary) !important;
    opacity: 0.7;
}

.theme-input:focus {
    border-color: var(--theme-primary) !important;
    outline: none;
    box-shadow: 0 0 0 3px var(--theme-primary-alpha) !important;
}

.btn-icon-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--theme-error);
    background-color: var(--theme-error-alpha);
    border: none;
}

.btn-icon-delete:hover {
    background-color: var(--theme-error);
    color: white;
}

.text-lg {
    font-size: var(--font-size-lg);
}

.font-semibold {
    font-weight: 600;
}

.mb-6 {
    margin-bottom: var(--spacing-6);
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.max-w-6xl {
    max-width: 72rem;
}

.overflow-x-auto {
    overflow-x: auto;
}
</style>
