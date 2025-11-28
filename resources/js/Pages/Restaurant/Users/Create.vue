<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    roles: Array,
    visitCount: Number,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'cliente', // Rol por defecto
    foto: null,
});

const imagePreview = ref(null);

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
    form.post(route('restaurant.users.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        },
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Crear Usuario - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.users.index')" class="text-2xl hover:opacity-75">
                            ← 
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Crear Usuario</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Registra un nuevo usuario en el sistema
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
                            <InputLabel for="password" value="Contraseña *" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                            />
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                Mínimo 8 caracteres
                            </p>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <InputLabel for="password_confirmation" value="Confirmar contraseña *" />
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Foto de Perfil -->
                        <div>
                            <InputLabel for="foto" value="Foto de Perfil (Opcional)" />
                            <input
                                id="foto"
                                type="file"
                                accept="image/*"
                                @change="handleImageChange"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            />
                            <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WEBP. Máximo 2MB</p>
                            
                            <!-- Preview -->
                            <div v-if="imagePreview" class="mt-4">
                                <p class="text-sm font-medium mb-2">Vista Previa:</p>
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
                                {{ form.processing ? 'Creando...' : 'Crear Usuario' }}
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
