<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    proveedores: Object,
    filters: Object,
    visitCount: Number,
});

const search = ref(props.filters.search || '');

function filterProveedores() {
    router.get(route('restaurant.proveedores.index'), { search: search.value }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function deleteProveedor(proveedorId) {
    if (confirm('¿Estás seguro de que deseas archivar este proveedor?')) {
        router.delete(route('restaurant.proveedores.destroy', proveedorId));
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Proveedores - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Gestión de Proveedores</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Administra los proveedores del restaurante
                    </p>
                </div>

                <!-- Search and Actions -->
                <div class="card rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">Buscar</label>
                            <input
                                v-model="search"
                                @input="filterProveedores"
                                type="text"
                                placeholder="Buscar por nombre..."
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                        </div>
                        <Link
                            :href="route('restaurant.proveedores.create')"
                            class="btn-primary px-6 py-2 rounded-lg font-medium transition whitespace-nowrap"
                        >
                            + Nuevo Proveedor
                        </Link>
                    </div>
                </div>

                <!-- Proveedores Table -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Correo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Celular</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proveedor in proveedores.data" :key="proveedor.id" class="border-t" style="border-color: var(--color-border)">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ proveedor.id }}</td>
                                    <td class="px-6 py-4 font-medium">{{ proveedor.nombre }}</td>
                                    <td class="px-6 py-4 text-sm">{{ proveedor.correo || '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ proveedor.celular || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <Link
                                            :href="route('restaurant.proveedores.edit', proveedor.id)"
                                            class="text-blue-600 hover:text-blue-800 font-medium mr-3"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="deleteProveedor(proveedor.id)"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                        >
                                            Archivar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="proveedores.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No se encontraron proveedores
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="proveedores.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <Link
                                v-for="(link, index) in proveedores.links"
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
