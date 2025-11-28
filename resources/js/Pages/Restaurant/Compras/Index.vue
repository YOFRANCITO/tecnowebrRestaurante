<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    compras: Object,
    proveedores: Array,
    filters: Object,
    visitCount: Number,
});

const proveedorId = ref(props.filters.proveedor_id || '');
const fechaDesde = ref(props.filters.fecha_desde || '');
const fechaHasta = ref(props.filters.fecha_hasta || '');
const expandedCompras = ref(new Set());

function filterCompras() {
    router.get(route('restaurant.compras.index'), {
        proveedor_id: proveedorId.value,
        fecha_desde: fechaDesde.value,
        fecha_hasta: fechaHasta.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function toggleExpand(compraId) {
    if (expandedCompras.value.has(compraId)) {
        expandedCompras.value.delete(compraId);
    } else {
        expandedCompras.value.add(compraId);
    }
}

function deleteCompra(compraId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta compra? Se validará que el stock lo permita.')) {
        router.delete(route('restaurant.compras.destroy', compraId));
    }
}

function formatDateTime(dateTime) {
    return new Date(dateTime).toLocaleString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Compras - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Gestión de Compras</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">Administra las compras de insumos</p>
                </div>

                <!-- Filters and Actions -->
                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Proveedor</label>
                            <select v-model="proveedorId" @change="filterCompras" class="w-full px-4 py-2 rounded-lg border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)">
                                <option value="">Todos</option>
                                <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">{{ proveedor.nombre }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Desde</label>
                            <input v-model="fechaDesde" @change="filterCompras" type="date" class="w-full px-4 py-2 rounded-lg border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Hasta</label>
                            <input v-model="fechaHasta" @change="filterCompras" type="date" class="w-full px-4 py-2 rounded-lg border" style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)" />
                        </div>
                    </div>
                    <Link :href="route('restaurant.compras.create')" class="btn-primary px-6 py-2 rounded-lg font-medium transition">
                        + Registrar Compra
                    </Link>
                </div>

                <!-- Compras Table -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Proveedor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="compra in compras.data" :key="compra.id">
                                    <tr class="border-t" style="border-color: var(--color-border)">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ compra.id }}</td>
                                        <td class="px-6 py-4">{{ compra.proveedor ? compra.proveedor.nombre : 'Sin proveedor' }}</td>
                                        <td class="px-6 py-4 font-medium">Bs. {{ compra.total }}</td>
                                        <td class="px-6 py-4 text-sm">{{ formatDateTime(compra.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button @click="toggleExpand(compra.id)" class="text-blue-600 hover:text-blue-800 font-medium mr-3">
                                                {{ expandedCompras.has(compra.id) ? 'Ocultar' : 'Ver' }} Detalles
                                            </button>
                                            <Link :href="route('restaurant.compras.edit', compra.id)" class="text-green-600 hover:text-green-800 font-medium mr-3">
                                                Editar
                                            </Link>
                                            <button @click="deleteCompra(compra.id)" class="text-red-600 hover:text-red-800 font-medium">
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="expandedCompras.has(compra.id)" class="bg-gray-50">
                                        <td colspan="5" class="px-6 py-4">
                                            <div class="text-sm">
                                                <strong>Detalles de la compra:</strong>
                                                <table class="w-full mt-2 border">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="px-4 py-2 text-left">Insumo</th>
                                                            <th class="px-4 py-2 text-left">Cantidad</th>
                                                            <th class="px-4 py-2 text-left">Costo Unit.</th>
                                                            <th class="px-4 py-2 text-left">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="detalle in compra.detalles" :key="detalle.id" class="border-t">
                                                            <td class="px-4 py-2">{{ detalle.insumo.nombre }}</td>
                                                            <td class="px-4 py-2">{{ detalle.cantidad }} {{ detalle.insumo.unidad_medida }}</td>
                                                            <td class="px-4 py-2">Bs. {{ detalle.costo_unitario }}</td>
                                                            <td class="px-4 py-2">Bs. {{ (detalle.cantidad * detalle.costo_unitario).toFixed(2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if="compras.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No se encontraron compras
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="compras.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <Link v-for="(link, index) in compras.links" :key="index" :href="link.url" :class="['px-3 py-1 rounded', link.active ? 'btn-primary' : 'bg-gray-200 hover:bg-gray-300']" v-html="link.label" :disabled="!link.url" />
                        </div>
                    </div>
                </div>
            </div>

            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
