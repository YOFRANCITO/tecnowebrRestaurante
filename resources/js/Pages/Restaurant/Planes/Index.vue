<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    planes: Object,
    filters: Object,
    visitCount: Number,
});

const search = ref(props.filters.search || '');

function filterPlanes() {
    router.get(route('restaurant.planes.index'), {
        search: search.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function deletePlan(planId) {
    if (confirm('¿Estás seguro de que deseas archivar este plan?')) {
        router.delete(route('restaurant.planes.destroy', planId));
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Planes de Crédito - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Planes de Crédito</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Administra los planes de crédito disponibles para los clientes
                    </p>
                </div>

                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Buscar</label>
                            <input
                                v-model="search"
                                @input="filterPlanes"
                                type="text"
                                placeholder="Buscar por nombre..."
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                        </div>
                        <Link
                            :href="route('restaurant.planes.create')"
                            class="btn-primary px-6 py-2 rounded-lg font-medium transition whitespace-nowrap"
                        >
                            + Crear Plan
                        </Link>
                    </div>
                </div>

                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tasa Diaria</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Plazo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="plan in planes.data" :key="plan.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ plan.nombre }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ plan.tasa_interes_diario }}% diario
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ plan.plazo_dias }} días
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('restaurant.planes.edit', plan.id)"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="deletePlan(plan.id)"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                        >
                                            Archivar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="planes.data.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron planes
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="planes.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in planes.links" :key="index">
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
