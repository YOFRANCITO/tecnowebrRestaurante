<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    compras: Object,
    totalGeneral: Number,
    proveedores: Array,
    filters: Object,
    visitCount: Number,
});

const filtros = ref({
    fecha_desde: props.filters.fecha_desde || '',
    fecha_hasta: props.filters.fecha_hasta || '',
    proveedor_id: props.filters.proveedor_id || '',
});

function aplicarFiltros() {
    router.get(route('restaurant.reportes.compras'), filtros.value, {
        preserveState: true,
        preserveScroll: true,
    });
}

function limpiarFiltros() {
    filtros.value = {
        fecha_desde: '',
        fecha_hasta: '',
        proveedor_id: '',
    };
    aplicarFiltros();
}

const formatNumber = (num) => {
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-BO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Reporte de Compras" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6 flex items-center gap-4">
                    <Link :href="route('restaurant.reportes.dashboard')" class="text-2xl hover:opacity-75">←</Link>
                    <div>
                        <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Reporte de Compras</h1>
                        <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                            Reporte detallado de compras con filtros
                        </p>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-bold mb-4">Filtros</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Fecha Desde</label>
                            <input
                                v-model="filtros.fecha_desde"
                                type="date"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Fecha Hasta</label>
                            <input
                                v-model="filtros.fecha_hasta"
                                type="date"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Proveedor</label>
                            <select
                                v-model="filtros.proveedor_id"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                <option value="">Todos</option>
                                <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                                    {{ proveedor.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button @click="aplicarFiltros" class="btn-primary px-6 py-2 rounded-lg">
                            Aplicar Filtros
                        </button>
                        <button @click="limpiarFiltros" class="px-6 py-2 rounded-lg" style="background-color: var(--color-border); color: var(--color-text-primary)">
                            Limpiar
                        </button>
                    </div>
                </div>

                <!-- Total General -->
                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold">Total General</h2>
                        <div class="flex items-center gap-4">
                            <p class="text-3xl font-bold text-blue-600">Bs. {{ formatNumber(totalGeneral) }}</p>
                            <div class="flex gap-2">
                                <a :href="route('restaurant.reportes.compras.export.excel', filters)" 
                                   class="btn-primary px-4 py-2 rounded-lg flex items-center gap-2">
                                    📊 Excel
                                </a>
                                <a :href="route('restaurant.reportes.compras.export.pdf', filters)" 
                                   class="px-4 py-2 rounded-lg flex items-center gap-2" 
                                   style="background-color: #ef4444; color: white;">
                                    📄 PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Compras -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Proveedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="compra in compras.data" :key="compra.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ formatDate(compra.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ compra.proveedor?.nombre || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ compra.user?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    Bs. {{ formatNumber(compra.total) }}
                                </td>
                            </tr>
                            <tr v-if="compras.data.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron compras
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="compras.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in compras.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="['px-3 py-1 rounded', link.active ? 'btn-primary' : 'bg-gray-200 hover:bg-gray-300']"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    :class="['px-3 py-1 rounded', link.active ? 'btn-primary' : 'bg-gray-200']"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
