<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    estadisticas: Object,
    evolucionCreditos: Array,
    creditosPorPlan: Array,
    creditosPendientesDetalle: Object,
    visitCount: Number,
});

const formatNumber = (num) => {
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-BO');
};
</script>

<template>
    <AppLayout>
        <Head title="Estadísticas de Créditos" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.reportes.dashboard')" class="text-2xl hover:opacity-75">←</Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Estadísticas de Créditos</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Análisis detallado de créditos y recuperación
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a :href="route('restaurant.reportes.creditos.export.excel')" 
                           class="btn-primary px-4 py-2 rounded-lg flex items-center gap-2">
                            📊 Excel
                        </a>
                        <a :href="route('restaurant.reportes.creditos.export.pdf')" 
                           class="px-4 py-2 rounded-lg flex items-center gap-2" 
                           style="background-color: #ef4444; color: white;">
                            📄 PDF
                        </a>
                    </div>
                </div>

                <!-- Estadísticas Principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="card rounded-lg shadow-sm p-6">
                        <p class="text-sm" style="color: var(--color-text-secondary)">Total Prestado</p>
                        <p class="text-2xl font-bold mt-1">Bs. {{ formatNumber(estadisticas.totalPrestado) }}</p>
                    </div>
                    <div class="card rounded-lg shadow-sm p-6">
                        <p class="text-sm" style="color: var(--color-text-secondary)">Total Recuperado</p>
                        <p class="text-2xl font-bold mt-1 text-green-600">Bs. {{ formatNumber(estadisticas.totalRecuperado) }}</p>
                    </div>
                    <div class="card rounded-lg shadow-sm p-6">
                        <p class="text-sm" style="color: var(--color-text-secondary)">Saldo Pendiente</p>
                        <p class="text-2xl font-bold mt-1 text-orange-600">Bs. {{ formatNumber(estadisticas.saldoPendiente) }}</p>
                    </div>
                    <div class="card rounded-lg shadow-sm p-6">
                        <p class="text-sm" style="color: var(--color-text-secondary)">Tasa de Recuperación</p>
                        <p class="text-2xl font-bold mt-1 text-blue-600">{{ estadisticas.tasaRecuperacion }}%</p>
                    </div>
                </div>

                <!-- Estado de Créditos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="card rounded-lg shadow-sm p-6">
                        <p class="text-sm" style="color: var(--color-text-secondary)">Créditos Pagados</p>
                        <p class="text-3xl font-bold mt-1 text-green-600">{{ estadisticas.creditosPagados }}</p>
                    </div>
                    <div class="card rounded-lg shadow-sm p-6">
                        <p class="text-sm" style="color: var(--color-text-secondary)">Créditos Pendientes</p>
                        <p class="text-3xl font-bold mt-1 text-orange-600">{{ estadisticas.creditosPendientes }}</p>
                    </div>
                    <div class="card rounded-lg shadow-sm p-6">
                        <p class="text-sm" style="color: var(--color-text-secondary)">Créditos en Mora</p>
                        <p class="text-3xl font-bold mt-1 text-red-600">{{ estadisticas.creditosEnMora }}</p>
                    </div>
                </div>

                <!-- Créditos por Plan -->
                <div class="card rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b" style="border-color: var(--color-border)">
                        <h2 class="text-xl font-bold">Créditos por Plan</h2>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(plan, index) in creditosPorPlan" :key="index" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ plan.nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ plan.cantidad }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    Bs. {{ formatNumber(plan.total) }}
                                </td>
                            </tr>
                            <tr v-if="creditosPorPlan.length === 0">
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    No hay datos disponibles
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Créditos Pendientes Detalle -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b" style="border-color: var(--color-border)">
                        <h2 class="text-xl font-bold">Créditos Pendientes</h2>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Saldo Inicial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Saldo Pendiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="credito in creditosPendientesDetalle.data" :key="credito.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ credito.nro }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ credito.user?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ formatDate(credito.fecha) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ credito.plan?.nombre || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    Bs. {{ formatNumber(credito.saldo_inicial) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-orange-600">
                                    Bs. {{ formatNumber(credito.saldo_final) }}
                                </td>
                            </tr>
                            <tr v-if="creditosPendientesDetalle.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    ✓ No hay créditos pendientes
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="creditosPendientesDetalle.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in creditosPendientesDetalle.links" :key="index">
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
