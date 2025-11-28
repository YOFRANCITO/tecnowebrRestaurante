<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    creditos: Object,
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
        <Head title="Mis Créditos - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">
                        {{ hasRole('cliente') ? 'Mis Créditos' : 'Créditos' }}
                    </h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        {{ hasRole('cliente') ? 'Créditos pendientes de pago' : 'Todos los créditos del sistema' }}
                    </p>
                </div>

                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nro. Crédito</th>
                                <th v-if="hasRole('administrador')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Saldo Inicial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Saldo Pendiente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="credito in creditos.data" :key="credito.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ credito.nro }}</div>
                                </td>
                                <td v-if="hasRole('administrador')" class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ credito.venta?.user?.name || 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ credito.venta?.user?.email || '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ new Date(credito.fecha).toLocaleDateString('es-BO') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ credito.plan.nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    Bs. {{ credito.saldo_inicial }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-red-600">Bs. {{ credito.saldo_final }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <Link
                                        :href="route('restaurant.creditos.show', credito.id)"
                                        class="text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                        Ver Detalle
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="creditos.data.length === 0">
                                <td :colspan="hasRole('administrador') ? 7 : 6" class="px-6 py-8 text-center text-gray-500">
                                    {{ hasRole('cliente') ? 'No tienes créditos pendientes' : 'No hay créditos registrados' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="creditos.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in creditos.links" :key="index">
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
