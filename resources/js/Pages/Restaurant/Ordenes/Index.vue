<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    ordenes: Object,
    filters: Object,
    visitCount: Number,
});

const estado = ref(props.filters.estado || '');

function filterOrdenes() {
    router.get(route('restaurant.ordenes.index'), {
        estado: estado.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function marcarEntregado(ordenId) {
    if (confirm('¿Marcar esta orden como entregada?')) {
        router.put(route('restaurant.ordenes.update', ordenId));
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Órdenes - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Gestión de Órdenes</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Órdenes pendientes de entrega (las pendientes aparecen primero)
                    </p>
                </div>

                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Filtrar por Estado</label>
                            <select
                                v-model="estado"
                                @change="filterOrdenes"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                <option value="">Todos</option>
                                <option value="PENDIENTE">Pendiente</option>
                                <option value="ENTREGADO">Entregado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Mesa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Productos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="orden in ordenes.data" :key="orden.id" class="border-t" :class="orden.estado === 'PENDIENTE' ? 'bg-yellow-50' : ''" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ new Date(orden.fecha_hora).toLocaleTimeString('es-BO') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-lg">{{ orden.mesa.codigo }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ orden.user.name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div v-for="detalle in orden.detalles" :key="detalle.id">
                                            {{ detalle.cantidad }}x {{ detalle.producto.nombre }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">Bs. {{ orden.total }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span :class="orden.estado === 'PENDIENTE' ? 'text-yellow-600 font-bold' : 'text-green-600'">
                                        {{ orden.estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button
                                        v-if="orden.estado === 'PENDIENTE'"
                                        @click="marcarEntregado(orden.id)"
                                        class="btn-primary px-4 py-2 rounded-lg font-medium"
                                    >
                                        Marcar Entregado
                                    </button>
                                    <span v-else class="text-green-600">✓ Entregado</span>
                                </td>
                            </tr>
                            <tr v-if="ordenes.data.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No hay órdenes
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="ordenes.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in ordenes.links" :key="index">
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
