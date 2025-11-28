<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
    visitCount: Number,
});

const search = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');

function filterUsers() {
    router.get(route('restaurant.users.index'), {
        search: search.value,
        role: selectedRole.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas archivar este usuario?')) {
        router.delete(route('restaurant.users.destroy', userId));
    }
}

function getRoleBadgeClass(roleName) {
    const classes = {
        'administrador': 'bg-purple-100 text-purple-800',
        'almacenero': 'bg-blue-100 text-blue-800',
        'mesero': 'bg-green-100 text-green-800',
        'cliente': 'bg-gray-100 text-gray-800',
    };
    return classes[roleName] || 'bg-gray-100 text-gray-800';
}
</script>

<template>
    <AppLayout>
        <Head title="Gestión de Usuarios - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Gestión de Usuarios</h1>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                        Administra los usuarios del sistema de restaurante
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
                                @input="filterUsers"
                                type="text"
                                placeholder="Buscar por nombre o email..."
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                        </div>

                        <!-- Role Filter -->
                        <div class="w-full md:w-48">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-primary)">
                                Filtrar por rol
                            </label>
                            <select
                                v-model="selectedRole"
                                @change="filterUsers"
                                class="w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                <option value="">Todos los roles</option>
                                <option v-for="role in roles" :key="role.id" :value="role.name">
                                    {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                </option>
                            </select>
                        </div>

                        <!-- Create Button -->
                        <Link
                            :href="route('restaurant.users.create')"
                            class="btn-primary px-6 py-2 rounded-lg font-medium transition whitespace-nowrap"
                        >
                            + Crear Usuario
                        </Link>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Foto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id" class="border-t" style="border-color: var(--color-border)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ user.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ user.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="user.profile_photo_path" class="flex items-center">
                                        <img :src="user.profile_photo_path" :alt="user.name" class="w-10 h-10 rounded-full object-cover shadow-sm" />
                                    </div>
                                    <div v-else class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 text-gray-500 font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        v-for="role in user.roles"
                                        :key="role.id"
                                        :class="getRoleBadgeClass(role.name)"
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <Link
                                            :href="route('restaurant.users.edit', user.id)"
                                            class="text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="deleteUser(user.id)"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                        >
                                            Archivar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron usuarios
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="users.links.length > 3" class="px-6 py-4 border-t" style="border-color: var(--color-border)">
                        <div class="flex flex-wrap gap-1">
                            <Link
                                v-for="(link, index) in users.links"
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
