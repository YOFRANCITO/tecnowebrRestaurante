<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    proveedores: Array,
    insumos: Array,
    visitCount: Number,
});

const form = useForm({
    proveedor_id: '',
    detalles: [
        { insumo_id: '', cantidad: '', costo_unitario: '' }
    ],
});

const total = computed(() => {
    return form.detalles.reduce((sum, detalle) => {
        const cantidad = parseFloat(detalle.cantidad) || 0;
        const costo = parseFloat(detalle.costo_unitario) || 0;
        return sum + (cantidad * costo);
    }, 0).toFixed(2);
});

function agregarDetalle() {
    form.detalles.push({ insumo_id: '', cantidad: '', costo_unitario: '' });
}

function eliminarDetalle(index) {
    if (form.detalles.length > 1) {
        form.detalles.splice(index, 1);
    }
}

function getInsumoInfo(insumoId) {
    const insumo = props.insumos.find(i => i.id === parseInt(insumoId));
    return insumo || null;
}

function submit() {
    form.post(route('restaurant.compras.store'));
}
</script>

<template>
    <AppLayout>
        <Head title="Registrar Compra - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.compras.index')" class="text-2xl hover:opacity-75">←</Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Registrar Compra</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">Registra una nueva compra de insumos</p>
                        </div>
                    </div>
                </div>

                <div class="card rounded-lg shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Proveedor -->
                        <div>
                            <InputLabel for="proveedor_id" value="Proveedor (opcional)" />
                            <select id="proveedor_id" v-model="form.proveedor_id" class="mt-1 block w-full px-4 py-2 rounded-lg border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)">
                                <option value="">Sin proveedor</option>
                                <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">{{ proveedor.nombre }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.proveedor_id" />
                        </div>

                        <!-- Detalles -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold" style="color: var(--color-text-primary)">Detalles de la Compra</h3>
                                <button type="button" @click="agregarDetalle" class="btn-primary px-4 py-2 rounded-lg text-sm">
                                    + Agregar Insumo
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="px-4 py-2 text-left">Insumo *</th>
                                            <th class="px-4 py-2 text-left">Stock Actual</th>
                                            <th class="px-4 py-2 text-left">Cantidad *</th>
                                            <th class="px-4 py-2 text-left">Costo Unitario *</th>
                                            <th class="px-4 py-2 text-left">Subtotal</th>
                                            <th class="px-4 py-2 text-left">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(detalle, index) in form.detalles" :key="index" class="border-t">
                                            <td class="px-4 py-2">
                                                <select v-model="detalle.insumo_id" class="w-full px-2 py-1 rounded border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)" required>
                                                    <option value="">Seleccione...</option>
                                                    <option v-for="insumo in insumos" :key="insumo.id" :value="insumo.id">
                                                        {{ insumo.nombre }} {{ insumo.marca ? '(' + insumo.marca.nombre + ')' : '' }}
                                                    </option>
                                                </select>
                                                <InputError class="mt-1" :message="form.errors[`detalles.${index}.insumo_id`]" />
                                            </td>
                                            <td class="px-4 py-2 text-sm">
                                                <span v-if="detalle.insumo_id">
                                                    {{ getInsumoInfo(detalle.insumo_id)?.stock || 0 }} {{ getInsumoInfo(detalle.insumo_id)?.unidad_medida || '' }}
                                                </span>
                                                <span v-else>-</span>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input v-model="detalle.cantidad" type="number" step="0.01" min="0.01" class="w-full px-2 py-1 rounded border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)" required />
                                                <InputError class="mt-1" :message="form.errors[`detalles.${index}.cantidad`]" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <input v-model="detalle.costo_unitario" type="number" step="0.01" min="0" class="w-full px-2 py-1 rounded border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)" required />
                                                <InputError class="mt-1" :message="form.errors[`detalles.${index}.costo_unitario`]" />
                                            </td>
                                            <td class="px-4 py-2 font-medium">
                                                Bs. {{ ((parseFloat(detalle.cantidad) || 0) * (parseFloat(detalle.costo_unitario) || 0)).toFixed(2) }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <button type="button" @click="eliminarDetalle(index)" :disabled="form.detalles.length === 1" class="text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50 font-bold">
                                            <td colspan="4" class="px-4 py-3 text-right">TOTAL:</td>
                                            <td class="px-4 py-3">Bs. {{ total }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <InputError class="mt-2" :message="form.errors.detalles" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" class="btn-primary px-6 py-2 rounded-lg font-medium transition" :disabled="form.processing">
                                {{ form.processing ? 'Registrando...' : 'Registrar Compra' }}
                            </button>
                            <Link :href="route('restaurant.compras.index')" class="px-6 py-2 rounded-lg font-medium transition" style="background-color: var(--color-border); color: var(--color-text-primary)">
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
