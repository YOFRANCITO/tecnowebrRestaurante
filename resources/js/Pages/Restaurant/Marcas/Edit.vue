<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    marca: Object,
    visitCount: Number,
});

const form = useForm({
    nombre: props.marca.nombre,
});

function submit() {
    form.put(route('restaurant.marcas.update', props.marca.id));
}
</script>

<template>
    <AppLayout>
        <Head title="Editar Marca - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.marcas.index')" class="text-2xl hover:opacity-75">
                            ← 
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Editar Marca</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Modifica los datos de la marca
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="nombre" value="Nombre de la marca *" />
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

                        <!-- Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button
                                type="submit"
                                class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Marca' }}
                            </button>
                            <Link
                                :href="route('restaurant.marcas.index')"
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
