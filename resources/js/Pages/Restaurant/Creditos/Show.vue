<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    credito: Object,
    interesesAcumulados: Number,
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
        <Head title="Detalle de Crédito - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.creditos.index')" class="text-2xl hover:opacity-75">← </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Detalle de Crédito {{ credito.nro }}</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Plan: {{ credito.plan.nombre }} ({{ credito.plan.tasa_interes_diario }}% diario)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resumen del Crédito -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="card rounded-lg shadow-sm p-6">
                        <div class="text-sm" style="color: var(--color-text-secondary)">Saldo Inicial</div>
                        <div class="text-2xl font-bold mt-1">Bs. {{ credito.saldo_inicial }}</div>
                    </div>
                    <div class="card rounded-lg shadow-sm p-6">
                        <div class="text-sm" style="color: var(--color-text-secondary)">Intereses Acumulados</div>
                        <div class="text-2xl font-bold mt-1 text-orange-600">Bs. {{ interesesAcumulados.toFixed(2) }}</div>
                    </div>
                    <div class="card rounded-lg shadow-sm p-6">
                        <div class="text-sm" style="color: var(--color-text-secondary)">Saldo Pendiente</div>
                        <div class="text-2xl font-bold mt-1 text-red-600">Bs. {{ credito.saldo_final }}</div>
                    </div>
                </div>

                <!-- Botón para Realizar Pago (solo clientes) -->
                <div v-if="hasRole('cliente')" class="mb-6">
                    <Link
                        :href="route('restaurant.pagos.create', { credito_id: credito.id })"
                        class="btn-primary px-6 py-3 rounded-lg font-medium transition inline-block"
                    >
                        💳 Realizar Pago
                    </Link>
                </div>

                <!-- Plan de Cuotas Sugerido -->
                <div class="card rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b" style="border-color: var(--color-border)">
                        <h2 class="text-xl font-bold">Plan de Cuotas Sugerido</h2>
                        
                        <!-- Para Cliente: Mensaje informativo -->
                        <p v-if="hasRole('cliente')" class="text-sm mt-1" style="color: var(--color-text-secondary)">
                            Este es un plan sugerido. Puedes pagar cuando desees.
                        </p>
                        
                        <!-- Para Administrador: Información del cliente -->
                        <div v-else class="mt-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium" style="color: var(--color-text-secondary)">Cliente:</span>
                                <span class="font-bold text-lg">{{ credito.venta?.user?.name || 'N/A' }}</span>
                            </div>
                            <div class="text-sm" style="color: var(--color-text-secondary)">
                                {{ credito.venta?.user?.email || '' }}
                            </div>
                        </div>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cuota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Saldo Inicial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Interés</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Capital</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cuota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Saldo Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="cuota in credito.cuotas" :key="cuota.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ cuota.nro }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ new Date(cuota.fecha).toLocaleDateString('es-BO') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">Bs. {{ cuota.saldo_inicial }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600">Bs. {{ cuota.interes }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">Bs. {{ cuota.capital }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">Bs. {{ cuota.cuota }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">Bs. {{ cuota.saldo_final }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Historial de Pagos -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b" style="border-color: var(--color-border)">
                        <h2 class="text-xl font-bold">Historial de Pagos</h2>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pago in credito.pagos" :key="pago.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ new Date(pago.fecha).toLocaleString('es-BO') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-green-600">Bs. {{ pago.monto }}</td>
                            </tr>
                            <tr v-if="credito.pagos.length === 0">
                                <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                    No hay pagos registrados
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
