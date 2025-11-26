<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    :href="`/pagos/${pago.id}`"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-4"
                >
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                    Volver a Detalles del Pago
                </Link>
                <h1 class="text-4xl font-bold text-gray-900">Editar Pago</h1>
                <p class="text-gray-600 mt-2">
                    Bs {{ parseFloat(pago.total).toFixed(2) }}
                </p>
            </div>

            <form
                @submit.prevent="submitForm"
                class="bg-white rounded-lg shadow-lg p-8"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Información del Pago -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Información del Pago
                        </h2>

                        <!-- Plan de Pago -->
                        <div class="mb-6">
                            <label
                                for="plan_pago_id"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Plan de Pago <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.plan_pago_id"
                                id="plan_pago_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="">Seleccionar un plan...</option>
                                <option
                                    v-for="plan in planesPago"
                                    :key="plan.id"
                                    :value="plan.id"
                                >
                                    {{ plan.proyecto.nombre }}
                                </option>
                            </select>
                            <p
                                v-if="errors.plan_pago_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.plan_pago_id }}
                            </p>
                        </div>

                        <!-- Fecha -->
                        <div class="mb-6">
                            <label
                                for="fecha"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Fecha de Pago
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.fecha"
                                type="date"
                                id="fecha"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <p
                                v-if="errors.fecha"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.fecha }}
                            </p>
                        </div>

                        <!-- Monto -->
                        <div class="mb-6">
                            <label
                                for="total"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Monto (Bs) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model.number="form.total"
                                type="number"
                                step="0.01"
                                id="total"
                                placeholder="0.00"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <p
                                v-if="errors.total"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.total }}
                            </p>
                        </div>

                        <!-- Método de Pago -->
                        <div class="mb-6">
                            <label
                                for="metodo_pago"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Método de Pago
                            </label>
                            <input
                                v-model="form.metodo_pago"
                                type="text"
                                id="metodo_pago"
                                placeholder="Ej: Transferencia, Efectivo, Cheque"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <p
                                v-if="errors.metodo_pago"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.metodo_pago }}
                            </p>
                        </div>
                    </div>

                    <!-- Estado del Pago -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Estado del Pago
                        </h2>

                        <div class="mb-6">
                            <label
                                for="estado"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.estado"
                                id="estado"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="pendiente">Pendiente</option>
                                <option value="completado">Completado</option>
                                <option value="fallido">Fallido</option>
                            </select>
                            <p
                                v-if="errors.estado"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.estado }}
                            </p>
                        </div>

                        <!-- Información Adicional -->
                        <div
                            class="bg-indigo-50 p-6 rounded-lg border border-indigo-200"
                        >
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                Información Adicional
                            </h3>

                            <div class="space-y-3">
                                <div>
                                    <p
                                        class="text-sm text-gray-600 font-semibold"
                                    >
                                        Proyecto
                                    </p>
                                    <p class="text-gray-900 font-semibold">
                                        {{ pago.plan_pago.proyecto.nombre }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-sm text-gray-600 font-semibold"
                                    >
                                        Cliente
                                    </p>
                                    <p class="text-gray-900 font-semibold">
                                        {{
                                            pago.plan_pago.proyecto.cliente
                                                .nombre
                                        }}
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-indigo-300">
                                    <p
                                        class="text-sm text-gray-600 font-semibold mb-2"
                                    >
                                        Progreso del Plan
                                    </p>
                                    <div
                                        class="w-full bg-indigo-200 rounded-full h-3 overflow-hidden"
                                    >
                                        <div
                                            :style="{
                                                width:
                                                    (parseFloat(
                                                        pago.plan_pago
                                                            .pagado_total
                                                    ) /
                                                        parseFloat(
                                                            pago.plan_pago
                                                                .deuda_total
                                                        )) *
                                                        100 +
                                                    '%',
                                            }"
                                            class="h-full bg-indigo-600 transition-all duration-300"
                                        />
                                    </div>
                                    <p class="text-xs text-gray-600 mt-2">
                                        Bs
                                        {{
                                            parseFloat(
                                                pago.plan_pago.pagado_total
                                            ).toFixed(2)
                                        }}
                                        de Bs
                                        {{
                                            parseFloat(
                                                pago.plan_pago.deuda_total
                                            ).toFixed(2)
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex gap-4 mt-8 pt-8 border-t border-gray-200">
                    <button
                        type="submit"
                        :disabled="processing"
                        class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition font-semibold"
                    >
                        <span v-if="!processing">Guardar Cambios</span>
                        <span v-else>Procesando...</span>
                    </button>
                    <Link
                        :href="`/pagos/${pago.id}`"
                        class="flex-1 px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold text-center"
                    >
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from "@inertiajs/vue3";

const props = defineProps({
    pago: Object,
    planesPago: Array,
    errors: Object,
});

const form = useForm({
    plan_pago_id: props.pago.plan_pago_id,
    fecha: props.pago.fecha,
    total: parseFloat(props.pago.total),
    estado: props.pago.estado,
    metodo_pago: props.pago.metodo_pago,
});

const submitForm = () => {
    form.patch(`/pagos/${props.pago.id}`);
};
</script>

<style scoped></style>
