<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    movimientos: Object,
    visitCount: Number,
});

function restoreMovimiento(movimientoId) {
    if (confirm('¿Estás seguro de que deseas desarchivar este movimiento?')) {
        router.post(route('restaurant.movimientos.restore', movimientoId));
    }
}

function getTipoBadgeClass(tipo) {
    const classes = {
        'Ingreso': 'bg-green-100 text-green-800',
        'Salida': 'bg-red-100 text-red-800',
        'Ajuste': 'bg-yellow-100 text-yellow-800',
    };
    return classes[tipo] || 'bg-gray-100 text-gray-800';
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
        <Head title="Movimientos Archivados - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.movimientos.index')" class="text-2xl hover:opacity-75">
                            ← 
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Movimientos Archivados</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Lista de movimientos archivados
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Movimientos Table -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Insumo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Stock Ant.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Stock Nuevo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Motivo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="movimiento in movimientos.data" :key="movimiento.id" class="border-t" style="border-color: var(--color-border)">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getTipoBadgeClass(movimiento.tipo)"
                                            class="px-2 py-1 text-xs font-semibold rounded-full"
                                        >
                                            {{ movimiento.tipo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ movimiento.insumo.nombre }}</div>
                                        <div v-if="movimiento.insumo.marca" class="text-xs" style="color: var(--color-text-secondary)">
                                            {{ movimiento.insumo.marca.nombre }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ movimiento.cantidad }} {{ movimiento.insumo.unidad_medida }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ movimiento.stock_anterior }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ movimiento.stock_nuevo }}</td>
                                    <td class="px-6 py-4 text-sm max-w-xs truncate" :title="movimiento.motivo">
                                        {{ movimiento.motivo }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ formatDateTime(movimiento.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button
                                            @click="restoreMovimiento(movimiento.id)"
                                            class="text-green-600 hover:text-green-800 font-medium"
                                        >
                                            Desarchivar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="movimientos.data.length === 0">
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        No hay movimientos archivados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="movimientos.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <Link
                                v-for="(link, index) in movimientos.links"
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1 rounded',
                                    link.active ? 'btn-primary' : 'bg-gray-200 hover:bg-gray-300'
                                ]"
                                v-html="link.label"
                                :disabled="!link.url"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
