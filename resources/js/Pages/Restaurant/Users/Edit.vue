<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
    roles: Array,
    visitCount: Number,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.roles[0]?.name || 'cliente',
    foto: null,
});

const imagePreview = ref(props.user.profile_photo_path || null);

function handleImageChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.foto = file;
        
        // Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function submit() {
    // Agregar _method al form para method spoofing
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('restaurant.users.update', props.user.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Editar Usuario - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.users.index')" class="text-2xl hover:opacity-75">
                            ←
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Editar Usuario</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Actualiza la información de {{ user.name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Nombre completo *" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Correo electrónico *" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>



                        <!-- Password -->
                        <div>
                            <InputLabel for="password" value="Nueva contraseña" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                Dejar en blanco para mantener la contraseña actual. Mínimo 8 caracteres si deseas cambiarla.
                            </p>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Password Confirmation -->
                        <div v-if="form.password">
                            <InputLabel for="password_confirmation" value="Confirmar nueva contraseña" />
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Foto de Perfil -->
                        <div>
                            <InputLabel for="foto" value="Foto de Perfil (Opcional)" />
                            
                            <!-- Foto Actual -->
                            <div v-if="user.profile_photo_path && !imagePreview" class="mt-2 mb-4">
                                <p class="text-sm font-medium mb-2">Foto Actual:</p>
                                <img :src="user.profile_photo_path" :alt="user.name" class="w-32 h-32 object-cover rounded-full shadow-md" />
                            </div>
                            
                            <input
                                id="foto"
                                type="file"
                                accept="image/*"
                                @change="handleImageChange"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                            <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WEBP. Máximo 2MB. Dejar vacío para mantener la foto actual.</p>
                            
                            <!-- Preview Nueva Foto -->
                            <div v-if="imagePreview && imagePreview !== user.profile_photo_path" class="mt-4">
                                <p class="text-sm font-medium mb-2">Nueva Foto:</p>
                                <img :src="imagePreview" alt="Preview" class="w-32 h-32 object-cover rounded-full shadow-md" />
                            </div>
                            
                            <InputError class="mt-2" :message="form.errors.foto" />
                        </div>

                        <!-- Role -->
                        <div>
                            <InputLabel for="role" value="Rol *" />
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                                required
                            >
                                <option v-for="role in roles" :key="role.id" :value="role.name">
                                    {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.role" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button
                                type="submit"
                                class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Usuario' }}
                            </button>
                            <Link
                                :href="route('restaurant.users.index')"
                                class="px-6 py-2 rounded-lg font-medium transition"
                                style="background-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
