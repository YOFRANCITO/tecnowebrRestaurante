<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    insumo: Object,
    marcas: Array,
    visitCount: Number,
});

const form = useForm({
    nombre: props.insumo.nombre,
    stock: props.insumo.stock,
    unidad_medida: props.insumo.unidad_medida,
    marca_id: props.insumo.marca_id || '',
});

function submit() {
    form.put(route('restaurant.insumos.update', props.insumo.id));
}
</script>

<template>
    <AppLayout>
        <Head title="Editar Insumo - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.insumos.index')" class="text-2xl hover:opacity-75">
                            ← 
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Editar Insumo</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Modifica los datos del insumo
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="nombre" value="Nombre del insumo *" />
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

                        <!-- Stock -->
                        <div>
                            <InputLabel for="stock" value="Stock *" />
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

                        <!-- Unidad de Medida -->
                        <div>
                            <InputLabel for="unidad_medida" value="Unidad de medida *" />
                            <TextInput
                                id="unidad_medida"
                                v-model="form.unidad_medida"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.unidad_medida" />
                        </div>

                        <!-- Marca -->
                        <div>
                            <InputLabel for="marca_id" value="Marca (opcional)" />
                            <select
                                id="marca_id"
                                v-model="form.marca_id"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            >
                                <option value="">Sin marca</option>
                                <option v-for="marca in marcas" :key="marca.id" :value="marca.id">
                                    {{ marca.nombre }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.marca_id" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button
                                type="submit"
                                class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Insumo' }}
                            </button>
                            <Link
                                :href="route('restaurant.insumos.index')"
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
