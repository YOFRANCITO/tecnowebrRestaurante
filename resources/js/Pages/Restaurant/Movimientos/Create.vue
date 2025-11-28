<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    insumos: Array,
    visitCount: Number,
});

const form = useForm({
    tipo: 'Ingreso',
    cantidad: '',
    motivo: '',
    insumo_id: '',
});

const insumoSeleccionado = computed(() => {
    if (!form.insumo_id) return null;
    return props.insumos.find(i => i.id === parseInt(form.insumo_id));
});

const stockInsuficiente = computed(() => {
    if (form.tipo !== 'Salida' || !insumoSeleccionado.value) return false;
    return parseFloat(form.cantidad) > parseFloat(insumoSeleccionado.value.stock);
});

function submit() {
    form.post(route('restaurant.movimientos.store'), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Registrar Movimiento - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.movimientos.index')" class="text-2xl hover:opacity-75">
                            ← 
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Registrar Movimiento</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Registra un movimiento de inventario
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Tipo -->
                        <div>
                            <InputLabel for="tipo" value="Tipo de movimiento *" />
                            <select
                                id="tipo"
                                v-model="form.tipo"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                                required
                            >
                                <option value="Ingreso">Ingreso</option>
                                <option value="Salida">Salida</option>
                                <option value="Ajuste">Ajuste</option>
                            </select>
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                <span v-if="form.tipo === 'Ingreso'">Aumenta el stock del insumo</span>
                                <span v-else-if="form.tipo === 'Salida'">Disminuye el stock del insumo</span>
                                <span v-else>Establece el stock a un valor específico</span>
                            </p>
                            <InputError class="mt-2" :message="form.errors.tipo" />
                        </div>

                        <!-- Insumo -->
                        <div>
                            <InputLabel for="insumo_id" value="Insumo *" />
                            <select
                                id="insumo_id"
                                v-model="form.insumo_id"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                                required
                            >
                                <option value="">Seleccione un insumo</option>
                                <option v-for="insumo in insumos" :key="insumo.id" :value="insumo.id">
                                    {{ insumo.nombre }} 
                                    <span v-if="insumo.marca">({{ insumo.marca.nombre }})</span>
                                    - Stock: {{ insumo.stock }} {{ insumo.unidad_medida }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.insumo_id" />
                        </div>

                        <!-- Stock Actual (info) -->
                        <div v-if="insumoSeleccionado" class="p-4 rounded-lg" style="background-color: var(--color-bg-secondary)">
                            <div class="text-sm">
                                <strong>Stock actual:</strong> {{ insumoSeleccionado.stock }} {{ insumoSeleccionado.unidad_medida }}
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div>
                            <InputLabel for="cantidad" :value="form.tipo === 'Ajuste' ? 'Nuevo stock *' : 'Cantidad *'" />
                            <TextInput
                                id="cantidad"
                                v-model="form.cantidad"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="mt-1 block w-full"
                                required
                            />
                            <p v-if="form.tipo === 'Ajuste'" class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                Este será el nuevo stock del insumo
                            </p>
                            <InputError class="mt-2" :message="form.errors.cantidad" />
                            
                            <!-- Advertencia de stock insuficiente -->
                            <div v-if="stockInsuficiente" class="mt-2 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                                ⚠️ La cantidad excede el stock disponible ({{ insumoSeleccionado.stock }} {{ insumoSeleccionado.unidad_medida }})
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div>
                            <InputLabel for="motivo" value="Motivo *" />
                            <textarea
                                id="motivo"
                                v-model="form.motivo"
                                rows="3"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                                required
                                placeholder="Describe el motivo del movimiento..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.motivo" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button
                                type="submit"
                                class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Registrando...' : 'Registrar Movimiento' }}
                            </button>
                            <Link
                                :href="route('restaurant.movimientos.index')"
                                class="px-6 py-2 rounded-lg font-medium transition"
                                style="background-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
