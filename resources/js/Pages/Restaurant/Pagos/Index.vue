<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    pagos: Object,
    visitCount: Number,
});

const page = usePage();
const user = page.props.auth.user;

// Función para verificar el rol del usuario
const hasRole = (roleName) => {
    return Array.isArray(user?.roles) && user.roles.some(r => r.name === roleName);
};
</script>

<template>
    <AppLayout>
        <Head title="Mis Pagos - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">
                        {{ hasRole('cliente') ? 'Mis Pagos' : 'Pagos' }}
                    </h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Historial de pagos realizados
                    </p>
                </div>

                <!-- Solo los clientes pueden crear pagos -->
                <div v-if="hasRole('cliente')" class="mb-6">
                    <Link
                        :href="route('restaurant.pagos.create')"
                        class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                    >
                        + Realizar Pago
                    </Link>
                </div>

                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th v-if="hasRole('administrador')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Crédito</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pago in pagos.data" :key="pago.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ new Date(pago.fecha).toLocaleString('es-BO') }}
                                </td>
                                <td v-if="hasRole('administrador')" class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ pago.credito?.venta?.user?.name || 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ pago.credito?.venta?.user?.email || '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ pago.credito.nro }}</div>
                                    <div class="text-sm" style="color: var(--color-text-secondary)">
                                        Venta: Bs. {{ pago.credito.venta.total }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-green-600">Bs. {{ pago.monto }}</div>
                                </td>
                            </tr>
                            <tr v-if="pagos.data.length === 0">
                                <td :colspan="hasRole('administrador') ? 4 : 3" class="px-6 py-8 text-center text-gray-500">
                                    {{ hasRole('cliente') ? 'No has realizado pagos' : 'No hay pagos registrados' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="pagos.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in pagos.links" :key="index">
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
