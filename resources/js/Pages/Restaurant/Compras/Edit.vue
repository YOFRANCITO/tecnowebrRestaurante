<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    compra: Object,
    proveedores: Array,
    insumos: Array,
    visitCount: Number,
});

const form = useForm({
    proveedor_id: props.compra.proveedor_id || '',
    detalles: props.compra.detalles.map(d => ({
        id: d.id,
        insumo_id: d.insumo_id,
        cantidad: d.cantidad,
        costo_unitario: d.costo_unitario,
        cantidad_original: d.cantidad, // Para detectar cambios
    })),
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
    const detalle = form.detalles[index];
    if (detalle.id) {
        // Es un detalle existente - verificar stock
        const insumo = getInsumoInfo(detalle.insumo_id);
        if (insumo && insumo.stock < detalle.cantidad) {
            if (!confirm(`ADVERTENCIA: El stock actual del insumo "${insumo.nombre}" (${insumo.stock} ${insumo.unidad_medida}) es menor a la cantidad de este detalle (${detalle.cantidad}). No se podrá eliminar. ¿Continuar de todos modos?`)) {
                return;
            }
        }
    }
    form.detalles.splice(index, 1);
}

function getInsumoInfo(insumoId) {
    const insumo = props.insumos.find(i => i.id === parseInt(insumoId));
    return insumo || null;
}

function getCantidadReducida(detalle) {
    if (!detalle.id || !detalle.cantidad_original) return 0;
    const reduccion = parseFloat(detalle.cantidad_original) - parseFloat(detalle.cantidad || 0);
    return reduccion > 0 ? reduccion : 0;
}

function stockInsuficiente(detalle) {
    if (!detalle.id) return false;
    const reduccion = getCantidadReducida(detalle);
    if (reduccion <= 0) return false;
    
    const insumo = getInsumoInfo(detalle.insumo_id);
    return insumo && insumo.stock < reduccion;
}

function submit() {
    // Validar antes de enviar
    let hayErrores = false;
    form.detalles.forEach((detalle, index) => {
        if (stockInsuficiente(detalle)) {
            const insumo = getInsumoInfo(detalle.insumo_id);
            const reduccion = getCantidadReducida(detalle);
            alert(`Error en detalle ${index + 1}: Stock insuficiente del insumo "${insumo.nombre}". Stock actual: ${insumo.stock}, necesario: ${reduccion}`);
            hayErrores = true;
        }
    });

    if (hayErrores) return;

    form.put(route('restaurant.compras.update', props.compra.id));
}
</script>

<template>
    <AppLayout>
        <Head title="Editar Compra - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.compras.index')" class="text-2xl hover:opacity-75">←</Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Editar Compra #{{ compra.id }}</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">Modifica los detalles de la compra</p>
                        </div>
                    </div>
                </div>

                <!-- Advertencia -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Importante:</strong> Al reducir cantidades o eliminar detalles, se validará que el stock actual del insumo lo permita.
                            </p>
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
                                            <th class="px-4 py-2 text-left">Costo Unit. *</th>
                                            <th class="px-4 py-2 text-left">Subtotal</th>
                                            <th class="px-4 py-2 text-left">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(detalle, index) in form.detalles" :key="index" class="border-t" :class="{ 'bg-red-50': stockInsuficiente(detalle) }">
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
                                                <div v-if="getCantidadReducida(detalle) > 0" class="text-xs mt-1" :class="stockInsuficiente(detalle) ? 'text-red-600' : 'text-yellow-600'">
                                                    ⚠️ Reducción: {{ getCantidadReducida(detalle) }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input v-model="detalle.costo_unitario" type="number" step="0.01" min="0" class="w-full px-2 py-1 rounded border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)" required />
                                                <InputError class="mt-1" :message="form.errors[`detalles.${index}.costo_unitario`]" />
                                            </td>
                                            <td class="px-4 py-2 font-medium">
                                                Bs. {{ ((parseFloat(detalle.cantidad) || 0) * (parseFloat(detalle.costo_unitario) || 0)).toFixed(2) }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <button type="button" @click="eliminarDetalle(index)" class="text-red-600 hover:text-red-800">
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
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Compra' }}
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
