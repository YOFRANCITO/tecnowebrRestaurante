<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    ventas: Object,
    filters: Object,
    visitCount: Number,
});

const page = usePage();
const user = page.props.auth.user;

// Función para verificar el rol del usuario
const hasRole = (roleName) => {
    return Array.isArray(user?.roles) && user.roles.some(r => r.name === roleName);
};

const isCliente = hasRole('cliente');
const isAdministrador = hasRole('administrador');

const estado = ref(props.filters.estado || '');
const tipoPago = ref(props.filters.tipo_pago || '');

function filterVentas() {
    router.get(route('restaurant.ventas.index'), {
        estado: estado.value,
        tipo_pago: tipoPago.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout>
        <Head :title="isCliente ? 'Mis Compras - Restaurante' : 'Gestión de Ventas - Restaurante'" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Título según rol -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">
                        {{ isCliente ? '🛒 Mis Compras' : '💰 Gestión de Ventas' }}
                    </h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        {{ isCliente ? 'Historial de tus pedidos en el restaurante' : 'Historial de ventas del restaurante' }}
                    </p>
                </div>

                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Estado</label>
                            <select
                                v-model="estado"
                                @change="filterVentas"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                <option value="">Todos</option>
                                <option value="PENDIENTE">Pendiente</option>
                                <option value="ENTREGADO">Entregado</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Tipo de Pago</label>
                            <select
                                v-model="tipoPago"
                                @change="filterVentas"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                <option value="">Todos</option>
                                <option value="Inmediato">Inmediato</option>
                                <option value="Crédito">Crédito</option>
                            </select>
                        </div>
                        <Link
                            :href="route('restaurant.ventas.create')"
                            class="btn-primary px-6 py-2 rounded-lg font-medium transition whitespace-nowrap"
                        >
                            {{ isCliente ? '🍽️ Realizar Pedido' : '+ Nueva Venta' }}
                        </Link>
                    </div>
                </div>

                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha/Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Mesa</th>
                                <th v-if="isAdministrador" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipo Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="venta in ventas.data" :key="venta.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ new Date(venta.fecha_hora).toLocaleString('es-BO') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">🪑 {{ venta.mesa.codigo }}</div>
                                </td>
                                <td v-if="isAdministrador" class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ venta.user.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-lg text-green-600">Bs. {{ venta.total }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium"
                                          :class="venta.tipo_pago === 'Crédito' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'">
                                        {{ venta.tipo_pago === 'Crédito' ? '💳 Crédito' : '💵 Inmediato' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium"
                                          :class="venta.estado === 'PENDIENTE' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'">
                                        {{ venta.estado === 'PENDIENTE' ? '🟡 Pendiente' : '✅ Entregado' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="ventas.data.length === 0">
                                <td :colspan="isAdministrador ? 6 : 5" class="px-6 py-8 text-center text-gray-500">
                                    {{ isCliente ? 'No has realizado compras aún' : 'No se encontraron ventas' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="ventas.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in ventas.links" :key="index">
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
