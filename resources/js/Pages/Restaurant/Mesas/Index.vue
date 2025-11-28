<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    mesas: Object,
    filters: Object,
    visitCount: Number,
});

const search = ref(props.filters.search || '');

function filterMesas() {
    router.get(route('restaurant.mesas.index'), {
        search: search.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function deleteMesa(mesaId) {
    if (confirm('¿Estás seguro de que deseas archivar esta mesa?')) {
        router.delete(route('restaurant.mesas.destroy', mesaId));
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Mesas - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Gestión de Mesas</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Administra las mesas del restaurante
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
                                @input="filterMesas"
                                type="text"
                                placeholder="Buscar por código..."
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                        </div>

                        <!-- Create Button -->
                        <Link
                            :href="route('restaurant.mesas.create')"
                            class="btn-primary px-6 py-2 rounded-lg font-medium transition whitespace-nowrap"
                        >
                            + Crear Mesa
                        </Link>
                    </div>
                </div>

                <!-- Mesas Table -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Capacidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="mesa in mesas.data" :key="mesa.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ mesa.codigo }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ mesa.capacidad }} personas
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('restaurant.mesas.edit', mesa.id)"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="deleteMesa(mesa.id)"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                        >
                                            Archivar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="mesas.data.length === 0">
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron mesas
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="mesas.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, index) in mesas.links" :key="index">
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
                                    :class="[
                                        'px-3 py-1 rounded',
                                        link.active ? 'btn-primary' : 'bg-gray-200'
                                    ]"
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
