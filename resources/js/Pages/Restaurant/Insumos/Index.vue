<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    insumos: Object,
    marcas: Array,
    filters: Object,
    visitCount: Number,
});

const search = ref(props.filters.search || '');
const selectedMarca = ref(props.filters.marca_id || '');

function filterInsumos() {
    router.get(route('restaurant.insumos.index'), {
        search: search.value,
        marca_id: selectedMarca.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function deleteInsumo(insumoId) {
    if (confirm('¿Estás seguro de que deseas archivar este insumo?')) {
        router.delete(route('restaurant.insumos.destroy', insumoId));
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Insumos - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Gestión de Insumos</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Administra los insumos del restaurante
                    </p>
                </div>

                <!-- Filters and Actions -->
                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <!-- Search -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">
                                Buscar
                            </label>
                            <input
                                v-model="search"
                                @input="filterInsumos"
                                type="text"
                                placeholder="Buscar por nombre..."
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                        </div>

                        <!-- Marca Filter -->
                        <div class="w-full md:w-48">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">
                                Filtrar por marca
                            </label>
                            <select
                                v-model="selectedMarca"
                                @change="filterInsumos"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                <option value="">Todas las marcas</option>
                                <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
                                    {{ marca.nombre }}
                                </option>
                            </select>
                        </div>

                        <!-- Create Button -->
                        <Link
                            :href="route('restaurant.insumos.create')"
                            class="btn-primary px-6 py-2 rounded-lg font-medium transition whitespace-nowrap"
                        >
                            + Crear Insumo
                        </Link>
                    </div>
                </div>

                <!-- Insumos Table -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Unidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Marca</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="insumo in insumos.data" :key="insumo.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ insumo.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ insumo.nombre }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ insumo.stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ insumo.unidad_medida }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span v-if="insumo.marca" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ insumo.marca.nombre }}
                                    </span>
                                    <span v-else class="text-gray-400">Sin marca</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('restaurant.insumos.edit', insumo.id)"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="deleteInsumo(insumo.id)"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                        >
                                            Archivar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="insumos.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron insumos
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="insumos.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in insumos.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1 rounded',
                                        link.active ? 'btn-primary' : 'bg-gray-200 hover:bg-gray-300'
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="px-3 py-1 rounded bg-gray-100 text-gray-400 cursor-not-allowed"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
