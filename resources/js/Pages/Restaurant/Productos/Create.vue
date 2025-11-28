<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    visitCount: Number,
});

const form = useForm({
    nombre: '',
    costo: '',
    precio_venta: '',
    stock: 0,
    imagen: null,
});

const imagePreview = ref(null);

function handleImageChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.imagen = file;
        
        // Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function submit() {
    form.post(route('restaurant.productos.store'), {
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
        <Head title="Crear Producto - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.productos.index')" class="text-2xl hover:opacity-75">
                            ← 
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Crear Producto</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Registra un nuevo producto en el sistema
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="nombre" value="Nombre del producto *" />
                            <TextInput
                                id="nombre"
                                v-model="form.nombre"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.nombre" />
                        </div>

                        <!-- Costo -->
                        <div>
                            <InputLabel for="costo" value="Costo de producción (Bs.) *" />
                            <TextInput
                                id="costo"
                                v-model="form.costo"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.costo" />
                        </div>

                        <!-- Precio de Venta -->
                        <div>
                            <InputLabel for="precio_venta" value="Precio de venta (Bs.) *" />
                            <TextInput
                                id="precio_venta"
                                v-model="form.precio_venta"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full"
                                required
                            />
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                Debe ser mayor que el costo de producción
                            </p>
                            <InputError class="mt-2" :message="form.errors.precio_venta" />
                        </div>

                        <!-- Stock -->
                        <div>
                            <InputLabel for="stock" value="Stock inicial *" />
                            <TextInput
                                id="stock"
                                v-model="form.stock"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.stock" />
                        </div>

                        <!-- Imagen -->
                        <div>
                            <InputLabel for="imagen" value="Imagen del Producto (Opcional)" />
                            <input
                                id="imagen"
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
                                <img :src="imagePreview" alt="Preview" class="w-48 h-48 object-cover rounded-lg shadow-md" />
                            </div>
                            
                            <InputError class="mt-2" :message="form.errors.imagen" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button
                                type="submit"
                                class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Guardando...' : 'Crear Producto' }}
                            </button>
                            <Link
                                :href="route('restaurant.productos.index')"
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
