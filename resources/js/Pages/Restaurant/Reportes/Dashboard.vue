<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import PieChart from '@/Components/Charts/PieChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';

const props = defineProps({
    metricas: Object,
    ventasPorTipo: Array,
    ventasPorDia: Array,
    productosMasVendidos: Array,
    insumosStockBajo: Array,
    estadisticasCreditos: Object,
    visitCount: Number,
});

// Formatear número con separadores de miles
const formatNumber = (num) => {
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
};

// Preparar datos para gráfica de torta (ventas por tipo de pago)
const ventasPorTipoData = computed(() => {
    if (!props.ventasPorTipo || props.ventasPorTipo.length === 0) {
        return { labels: [], data: [] };
    }
    return {
        labels: props.ventasPorTipo.map(v => v.tipo_pago),
        data: props.ventasPorTipo.map(v => parseFloat(v.total))
    };
});

// Preparar datos para gráfica de líneas (ventas por día)
const ventasPorDiaData = computed(() => {
    if (!props.ventasPorDia || props.ventasPorDia.length === 0) {
        return { labels: [], datasets: [] };
    }
    return {
        labels: props.ventasPorDia.map(v => {
            const date = new Date(v.fecha);
            return date.toLocaleDateString('es-BO', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Ventas (Bs.)',
            data: props.ventasPorDia.map(v => parseFloat(v.total)),
            borderColor: 'rgba(59, 130, 246, 1)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)'
        }]
    };
});

// Preparar datos para gráfica de barras (productos más vendidos)
const productosMasVendidosData = computed(() => {
    if (!props.productosMasVendidos || props.productosMasVendidos.length === 0) {
        return { labels: [], datasets: [] };
    }
    const top5 = props.productosMasVendidos.slice(0, 5);
    return {
        labels: top5.map(p => p.nombre),
        datasets: [{
            label: 'Cantidad Vendida',
            data: top5.map(p => parseFloat(p.total_vendido)),
            backgroundColor: 'rgba(16, 185, 129, 0.6)',
            borderColor: 'rgba(16, 185, 129, 1)'
        }]
    };
});

</script>

<template>
    <AppLayout>
        <Head title="Dashboard - Reportes y Estadísticas" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Dashboard - Reportes y Estadísticas</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Vista general del negocio
                    </p>
                </div>

                <!-- Métricas Principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Ventas del Mes -->
                    <div class="card rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" style="color: var(--color-text-secondary)">Ventas del Mes</p>
                                <p class="text-2xl font-bold mt-1" style="color: var(--color-text-primary)">
                                    Bs. {{ formatNumber(metricas.ventasMes) }}
                                </p>
                            </div>
                            <div class="text-4xl">💰</div>
                        </div>
                    </div>

                    <!-- Compras del Mes -->
                    <div class="card rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" style="color: var(--color-text-secondary)">Compras del Mes</p>
                                <p class="text-2xl font-bold mt-1" style="color: var(--color-text-primary)">
                                    Bs. {{ formatNumber(metricas.comprasMes) }}
                                </p>
                            </div>
                            <div class="text-4xl">🛒</div>
                        </div>
                    </div>

                    <!-- Créditos Pendientes -->
                    <div class="card rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" style="color: var(--color-text-secondary)">Créditos Pendientes</p>
                                <p class="text-2xl font-bold mt-1 text-orange-600">
                                    Bs. {{ formatNumber(metricas.creditosPendientes) }}
                                </p>
                            </div>
                            <div class="text-4xl">💳</div>
                        </div>
                    </div>

                    <!-- Órdenes Pendientes -->
                    <div class="card rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm" style="color: var(--color-text-secondary)">Órdenes Pendientes</p>
                                <p class="text-2xl font-bold mt-1 text-yellow-600">
                                    {{ metricas.ordenesPendientes }}
                                </p>
                            </div>
                            <div class="text-4xl">📝</div>
                        </div>
                    </div>
                </div>

                <!-- Enlaces a Reportes Detallados -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <Link :href="route('restaurant.reportes.ventas')" class="card rounded-lg shadow-sm p-6 hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl">📊</div>
                            <div>
                                <h3 class="font-bold text-lg">Reporte de Ventas</h3>
                                <p class="text-sm" style="color: var(--color-text-secondary)">Ver reporte detallado</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('restaurant.reportes.compras')" class="card rounded-lg shadow-sm p-6 hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl">📦</div>
                            <div>
                                <h3 class="font-bold text-lg">Reporte de Compras</h3>
                                <p class="text-sm" style="color: var(--color-text-secondary)">Ver reporte detallado</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('restaurant.reportes.creditos')" class="card rounded-lg shadow-sm p-6 hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl">💵</div>
                            <div>
                                <h3 class="font-bold text-lg">Estadísticas de Créditos</h3>
                                <p class="text-sm" style="color: var(--color-text-secondary)">Ver estadísticas</p>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Estadísticas de Créditos -->
                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Resumen de Créditos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <p class="text-sm" style="color: var(--color-text-secondary)">Total Prestado</p>
                            <p class="text-lg font-bold">Bs. {{ formatNumber(estadisticasCreditos.totalPrestado) }}</p>
                        </div>
                        <div>
                            <p class="text-sm" style="color: var(--color-text-secondary)">Total Recuperado</p>
                            <p class="text-lg font-bold text-green-600">Bs. {{ formatNumber(estadisticasCreditos.totalRecuperado) }}</p>
                        </div>
                        <div>
                            <p class="text-sm" style="color: var(--color-text-secondary)">Saldo Pendiente</p>
                            <p class="text-lg font-bold text-orange-600">Bs. {{ formatNumber(estadisticasCreditos.saldoPendiente) }}</p>
                        </div>
                        <div>
                            <p class="text-sm" style="color: var(--color-text-secondary)">Tasa de Recuperación</p>
                            <p class="text-lg font-bold text-blue-600">{{ estadisticasCreditos.tasaRecuperacion }}%</p>
                        </div>
                        <div>
                            <p class="text-sm" style="color: var(--color-text-secondary)">Créditos en Mora</p>
                            <p class="text-lg font-bold text-red-600">{{ estadisticasCreditos.creditosEnMora }}</p>
                        </div>
                    </div>
                </div>

                <!-- Gráficas -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Gráfica de Torta: Ventas por Tipo de Pago -->
                    <div class="card rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-bold mb-4">Ventas por Tipo de Pago</h2>
                        <PieChart 
                            v-if="ventasPorTipoData.labels.length > 0"
                            :labels="ventasPorTipoData.labels"
                            :data="ventasPorTipoData.data"
                            :colors="['#10b981', '#f59e0b']"
                        />
                        <p v-else class="text-center text-gray-500 py-8">No hay datos disponibles</p>
                    </div>

                    <!-- Gráfica de Líneas: Ventas por Día (últimos 30 días) -->
                    <div class="card rounded-lg shadow-sm p-6 lg:col-span-2">
                        <h2 class="text-lg font-bold mb-4">Ventas Últimos 30 Días</h2>
                        <LineChart 
                            v-if="ventasPorDiaData.labels.length > 0"
                            :labels="ventasPorDiaData.labels"
                            :datasets="ventasPorDiaData.datasets"
                        />
                        <p v-else class="text-center text-gray-500 py-8">No hay datos disponibles</p>
                    </div>
                </div>

                <!-- Gráfica de Barras: Top 5 Productos -->
                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-bold mb-4">Top 5 Productos Más Vendidos</h2>
                    <div class="max-w-3xl mx-auto">
                        <BarChart 
                            v-if="productosMasVendidosData.labels.length > 0"
                            :labels="productosMasVendidosData.labels"
                            :datasets="productosMasVendidosData.datasets"
                            :horizontal="true"
                        />
                        <p v-else class="text-center text-gray-500 py-8">No hay datos disponibles</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Productos Más Vendidos -->
                    <div class="card rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b" style="border-color: var(--color-border)">
                            <h2 class="text-xl font-bold">Top 10 Productos Más Vendidos</h2>
                        </div>
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(producto, index) in productosMasVendidos" :key="index" class="border-t" style="border-color: var(--color-border)">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium">{{ producto.nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ producto.total_vendido }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        Bs. {{ formatNumber(producto.total_ingresos) }}
                                    </td>
                                </tr>
                                <tr v-if="productosMasVendidos.length === 0">
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                        No hay datos disponibles
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Insumos con Stock Bajo -->
                    <div class="card rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b" style="border-color: var(--color-border)">
                            <h2 class="text-xl font-bold">Insumos con Stock Bajo</h2>
                            <p class="text-sm" style="color: var(--color-text-secondary)">Menos de 20 unidades</p>
                        </div>
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Insumo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(insumo, index) in insumosStockBajo" :key="index" class="border-t" style="border-color: var(--color-border)">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium">{{ insumo.nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-red-600 font-bold">{{ insumo.stock }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ insumo.unidad_medida }}
                                    </td>
                                </tr>
                                <tr v-if="insumosStockBajo.length === 0">
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                        ✓ Todos los insumos tienen stock suficiente
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
