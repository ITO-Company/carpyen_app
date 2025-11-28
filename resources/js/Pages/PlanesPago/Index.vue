<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";

const props = defineProps({
    planesPago: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            current_page: 1,
            last_page: 1,
        }),
    },
});

const expandidos = ref({});

const toggleExpand = (id) => {
    expandidos.value[id] = !expandidos.value[id];
};

const formatearFecha = (fecha) => {
    if (!fecha) return "";
    return new Date(fecha).toLocaleDateString("es-BO", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });
};

const formatearMoneda = (valor) => {
    return new Intl.NumberFormat("es-BO", {
        style: "currency",
        currency: "BOB",
    }).format(valor);
};

const eliminar = (planPago) => {
    if (confirm(`¿Eliminar plan de pago para "${planPago.proyecto.nombre}"?`)) {
        useForm({}).delete(`/planesPago/${planPago.id}`);
    }
};

const obtenerColorEstado = (estado) => {
    const colores = {
        activo: "bg-green-100 text-green-800",
        completado: "bg-blue-100 text-blue-800",
        mora: "bg-red-100 text-red-800",
    };
    return colores[estado] || "bg-gray-100 text-gray-800";
};
</script>

<template>
    <Head title="Planes de Pago" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl"
                    style="color: var(--theme-text-primary)"
                >
                    Planes de Pago
                </h2>
                <Link
                    href="/planesPago/create"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:shadow-lg transition duration-200"
                >
                    + Nuevo Plan de Pago
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tabla de Planes -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left font-semibold text-gray-700"
                                    >
                                        Proyecto
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-semibold text-gray-700"
                                    >
                                        Cliente
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold text-gray-700"
                                    >
                                        Deuda Total
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold text-gray-700"
                                    >
                                        Pagado
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold text-gray-700"
                                    >
                                        Saldo
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center font-semibold text-gray-700"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center font-semibold text-gray-700"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template
                                    v-if="
                                        planesPago.data &&
                                        planesPago.data.length > 0
                                    "
                                >
                                    <template
                                        v-for="plan in planesPago.data"
                                        :key="plan.id"
                                    >
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 text-gray-900">
                                                {{
                                                    plan.proyecto?.nombre ||
                                                    "N/A"
                                                }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-700">
                                                {{
                                                    plan.proyecto?.cliente
                                                        ?.nombre || "N/A"
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-right text-purple-600 font-semibold"
                                            >
                                                {{
                                                    formatearMoneda(
                                                        plan.deuda_total
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-right text-green-600 font-semibold"
                                            >
                                                {{
                                                    formatearMoneda(
                                                        plan.pagado_total
                                                    )
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-right text-orange-600 font-semibold"
                                            >
                                                {{
                                                    formatearMoneda(
                                                        plan.deuda_total -
                                                            plan.pagado_total
                                                    )
                                                }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    :class="[
                                                        'px-3 py-1 rounded-full text-xs font-semibold',
                                                        obtenerColorEstado(
                                                            plan.estado
                                                        ),
                                                    ]"
                                                >
                                                    {{
                                                        plan.estado?.toUpperCase()
                                                    }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 text-center space-x-2"
                                            >
                                                <Link
                                                    :href="`/planesPago/${plan.id}`"
                                                    class="inline-block px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
                                                >
                                                    Ver
                                                </Link>
                                                <Link
                                                    :href="`/planesPago/${plan.id}/edit`"
                                                    class="inline-block px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition"
                                                >
                                                    Editar
                                                </Link>
                                                <button
                                                    @click="eliminar(plan)"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
                                                >
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Fila expandible de pagos -->
                                        <tr
                                            v-if="expandidos[plan.id]"
                                            class="bg-gray-100 border-b"
                                        >
                                            <td colspan="7" class="px-6 py-4">
                                                <div
                                                    class="bg-white rounded-lg p-4"
                                                >
                                                    <h4
                                                        class="font-semibold text-gray-900 mb-3"
                                                    >
                                                        Pagos Asociados
                                                    </h4>
                                                    <table
                                                        class="w-full text-xs"
                                                    >
                                                        <thead
                                                            class="bg-gray-200"
                                                        >
                                                            <tr>
                                                                <th
                                                                    class="px-3 py-2 text-left"
                                                                >
                                                                    Fecha
                                                                </th>
                                                                <th
                                                                    class="px-3 py-2 text-right"
                                                                >
                                                                    Monto
                                                                </th>
                                                                <th
                                                                    class="px-3 py-2 text-center"
                                                                >
                                                                    Estado
                                                                </th>
                                                                <th
                                                                    class="px-3 py-2 text-center"
                                                                >
                                                                    Acciones
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr
                                                                v-for="pago in plan.pagos"
                                                                :key="pago.id"
                                                                class="border-b hover:bg-gray-50"
                                                            >
                                                                <td
                                                                    class="px-3 py-2"
                                                                >
                                                                    {{
                                                                        formatearFecha(
                                                                            pago.fecha
                                                                        )
                                                                    }}
                                                                </td>
                                                                <td
                                                                    class="px-3 py-2 text-right"
                                                                >
                                                                    {{
                                                                        formatearMoneda(
                                                                            pago.total
                                                                        )
                                                                    }}
                                                                </td>
                                                                <td
                                                                    class="px-3 py-2 text-center"
                                                                >
                                                                    <span
                                                                        :class="[
                                                                            'px-2 py-1 rounded text-xs font-semibold',
                                                                            pago.estado ===
                                                                            'completado'
                                                                                ? 'bg-green-100 text-green-800'
                                                                                : 'bg-yellow-100 text-yellow-800',
                                                                        ]"
                                                                    >
                                                                        {{
                                                                            pago.estado
                                                                        }}
                                                                    </span>
                                                                </td>
                                                                <td
                                                                    class="px-3 py-2 text-center"
                                                                >
                                                                    <Link
                                                                        :href="`/pagos/${pago.id}`"
                                                                        class="text-blue-600 hover:text-blue-800"
                                                                    >
                                                                        Detalles
                                                                    </Link>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <tr v-else>
                                    <td
                                        colspan="7"
                                        class="px-6 py-8 text-center text-gray-500"
                                    >
                                        No hay planes de pago creados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                <div
                    v-if="planesPago.links && planesPago.links.length > 0"
                    class="mt-4 flex justify-center gap-2"
                >
                    <template v-for="link in planesPago.links" :key="link.url">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-4 py-2 rounded transition',
                                link.active
                                    ? 'bg-purple-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300',
                            ]"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            :class="[
                                'px-4 py-2 rounded',
                                link.active
                                    ? 'bg-purple-600 text-white'
                                    : 'bg-gray-100 text-gray-500',
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
