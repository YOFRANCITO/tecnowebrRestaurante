<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    creditos: Array,
    creditoSeleccionado: Object,
    visitCount: Number,
});

const form = useForm({
    credito_id: props.creditoSeleccionado?.id || '',
    monto: '',
});

const creditoActual = computed(() => {
    if (!form.credito_id) return null;
    return props.creditos.find(c => c.id == form.credito_id) || props.creditoSeleccionado;
});

function submit() {
    form.post(route('restaurant.pagos.store'));
}
</script>

<template>
    <AppLayout>
        <Head title="Realizar Pago - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.pagos.index')" class="text-2xl hover:opacity-75">← </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Realizar Pago</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Paga tus créditos pendientes
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Seleccionar Crédito -->
                        <div>
                            <InputLabel for="credito_id" value="Seleccionar Crédito *" />
                            <select
                                id="credito_id"
                                v-model="form.credito_id"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                                required
                            >
                                <option value="">-- Seleccione un crédito --</option>
                                <option v-for="credito in creditos" :key="credito.id" :value="credito.id">
                                    {{ credito.nro }} - Saldo: Bs. {{ credito.saldo_final }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.credito_id" />
                        </div>

                        <!-- Información del Crédito Seleccionado -->
                        <div v-if="creditoActual" class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-bold mb-2">Información del Crédito</h3>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-600">Número:</span>
                                    <span class="font-medium ml-2">{{ creditoActual.nro }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Plan:</span>
                                    <span class="font-medium ml-2">{{ creditoActual.plan?.nombre }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Saldo Inicial:</span>
                                    <span class="font-medium ml-2">Bs. {{ creditoActual.saldo_inicial }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Saldo Pendiente:</span>
                                    <span class="font-bold text-red-600 ml-2">Bs. {{ creditoActual.saldo_final }}</span>
                                </div>
                                <div v-if="creditoActual.intereses_acumulados" class="col-span-2">
                                    <span class="text-gray-600">Intereses Acumulados:</span>
                                    <span class="font-bold text-orange-600 ml-2">Bs. {{ creditoActual.intereses_acumulados.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Monto a Pagar -->
                        <div>
                            <InputLabel for="monto" value="Monto a pagar (Bs.) *" />
                            <TextInput
                                id="monto"
                                v-model="form.monto"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="mt-1 block w-full"
                                placeholder="Ingrese el monto"
                                required
                            />
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                El pago se aplicará primero a los intereses y luego al capital
                            </p>
                            <InputError class="mt-2" :message="form.errors.monto" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button
                                type="submit"
                                class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Procesando...' : 'Realizar Pago' }}
                            </button>
                            <Link
                                :href="route('restaurant.pagos.index')"
                                class="px-6 py-2 rounded-lg font-medium transition"
                                style="background-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>

            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
