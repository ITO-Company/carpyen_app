<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    href="/planesPago"
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
                    Volver a Planes de Pago
                </Link>
                <h1 class="text-4xl font-bold text-gray-900">
                    Editar Plan de Pago
                </h1>
                <p class="text-gray-600 mt-2">{{ planPago.proyecto.nombre }}</p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-8">
                <!-- Card de Información Básica -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        Información del Plan
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Proyecto -->
                        <div>
                            <label
                                for="proyecto_id"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Proyecto <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.proyecto_id"
                                id="proyecto_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="">
                                    Seleccionar un proyecto...
                                </option>
                                <option
                                    v-for="proyecto in proyectos"
                                    :key="proyecto.id"
                                    :value="proyecto.id"
                                >
                                    {{ proyecto.nombre }} -
                                    {{ proyecto.cliente.nombre }}
                                </option>
                            </select>
                            <p
                                v-if="errors.proyecto_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.proyecto_id }}
                            </p>
                        </div>

                        <!-- Deuda Total -->
                        <div>
                            <label
                                for="deuda_total"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Deuda Total (Bs)
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model.number="form.deuda_total"
                                type="number"
                                step="0.01"
                                id="deuda_total"
                                placeholder="0.00"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                @input="validarSumaPagos"
                            />
                            <p
                                v-if="errors.deuda_total"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.deuda_total }}
                            </p>
                        </div>

                        <!-- Número de Deudas -->
                        <div>
                            <label
                                for="numero_deudas"
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Número de Deudas
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model.number="form.numero_deudas"
                                type="number"
                                min="1"
                                id="numero_deudas"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <p
                                v-if="errors.numero_deudas"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ errors.numero_deudas }}
                            </p>
                        </div>

                        <!-- Resumen -->
                        <div
                            class="bg-indigo-50 p-4 rounded-lg border border-indigo-200"
                        >
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-semibold"
                                    >Número de pagos:</span
                                >
                                {{ form.pagos.length }}
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-semibold"
                                    >Total planificado:</span
                                >
                                <span class="text-indigo-600 font-bold"
                                    >Bs
                                    {{ totalPagosCalculado.toFixed(2) }}</span
                                >
                            </p>
                            <p
                                v-if="diferenciaTotal !== 0"
                                :class="[
                                    'text-sm font-semibold',
                                    diferenciaTotal === 0
                                        ? 'text-green-600'
                                        : 'text-red-600',
                                ]"
                            >
                                <span v-if="diferenciaTotal > 0"
                                    >Diferencia: +Bs
                                    {{ diferenciaTotal.toFixed(2) }}</span
                                >
                                <span v-else
                                    >Diferencia: -Bs
                                    {{
                                        Math.abs(diferenciaTotal).toFixed(2)
                                    }}</span
                                >
                            </p>
                            <p
                                v-else
                                class="text-sm text-green-600 font-semibold"
                            >
                                ✓ Suma correcta
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card de Pagos -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Detalle de Pagos
                        </h2>
                        <button
                            type="button"
                            @click="agregarPago"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold"
                        >
                            <svg
                                class="w-5 h-5 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            Agregar Pago
                        </button>
                    </div>

                    <p v-if="errors.pagos" class="text-red-500 text-sm mb-4">
                        {{ errors.pagos }}
                    </p>

                    <!-- Tabla de Pagos -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead
                                class="bg-gray-100 border-b-2 border-gray-300"
                            >
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                    >
                                        #
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                    >
                                        Fecha
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                    >
                                        Monto (Bs)
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                    >
                                        Método de Pago
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-sm font-semibold text-gray-700"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-if="form.pagos.length === 0"
                                    class="text-center py-8"
                                >
                                    <td
                                        colspan="6"
                                        class="px-4 py-8 text-gray-500"
                                    >
                                        No hay pagos agregados. Haga clic en
                                        "Agregar Pago" para comenzar.
                                    </td>
                                </tr>
                                <tr
                                    v-for="(pago, index) in form.pagos"
                                    :key="pago.id || index"
                                    class="hover:bg-gray-50 transition"
                                >
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            v-model="pago.fecha"
                                            type="date"
                                            :min="todayDate"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            @change="validarSumaPagos"
                                        />
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            v-model.number="pago.total"
                                            type="number"
                                            step="0.01"
                                            placeholder="0.00"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            @input="validarSumaPagos"
                                        />
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            v-model="pago.metodo_pago"
                                            type="text"
                                            placeholder="Ej: Transferencia, Efectivo"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        />
                                    </td>
                                    <td class="px-4 py-3">
                                        <select
                                            v-model="pago.estado"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        >
                                            <option value="pendiente">
                                                Pendiente
                                            </option>
                                            <option value="completado">
                                                Completado
                                            </option>
                                            <option value="fallido">
                                                Fallido
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            @click="removerPago(index)"
                                            class="inline-flex items-center px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p
                        v-if="errors['pagos.*']"
                        class="text-red-500 text-sm mt-4"
                    >
                        {{ errors["pagos.*"] }}
                    </p>
                </div>

                <!-- Botones de Acción -->
                <div class="flex gap-4">
                    <button
                        type="submit"
                        :disabled="processing || diferenciaTotal !== 0"
                        class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition font-semibold"
                    >
                        <span v-if="!processing">Guardar Cambios</span>
                        <span v-else>Procesando...</span>
                    </button>
                    <Link
                        href="/planesPago"
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
import { ref, computed } from "vue";

const props = defineProps({
    planPago: Object,
    proyectos: Array,
    errors: Object,
});

const form = useForm({
    proyecto_id: props.planPago.proyecto_id,
    deuda_total: parseFloat(props.planPago.deuda_total),
    numero_deudas: props.planPago.numero_deudas,
    pagos: props.planPago.pagos.map((pago) => ({
        id: pago.id,
        fecha: pago.fecha,
        total: parseFloat(pago.total),
        metodo_pago: pago.metodo_pago,
        estado: pago.estado,
    })),
});

const todayDate = ref(new Date().toISOString().split("T")[0]);

const totalPagosCalculado = computed(() => {
    return form.pagos.reduce((sum, pago) => sum + (pago.total || 0), 0);
});

const diferenciaTotal = computed(() => {
    return totalPagosCalculado.value - form.deuda_total;
});

const agregarPago = () => {
    form.pagos.push({
        fecha: "",
        total: 0,
        metodo_pago: "",
        estado: "pendiente",
    });
};

const removerPago = (index) => {
    form.pagos.splice(index, 1);
};

const validarSumaPagos = () => {
    // Validación automática de la suma
};

const submitForm = () => {
    if (diferenciaTotal.value !== 0) {
        alert("La suma de los pagos debe ser igual a la deuda total");
        return;
    }

    form.patch(`/planesPago/${props.planPago.id}`);
};
</script>

<style scoped></style>
