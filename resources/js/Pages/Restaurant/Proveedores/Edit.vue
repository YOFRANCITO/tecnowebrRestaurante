<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    proveedor: Object,
    visitCount: Number,
});

const form = useForm({
    nombre: props.proveedor.nombre,
    detalles: props.proveedor.detalles || '',
    correo: props.proveedor.correo || '',
    celular: props.proveedor.celular || '',
});

function submit() {
    form.put(route('restaurant.proveedores.update', props.proveedor.id));
}
</script>

<template>
    <AppLayout>
        <Head title="Editar Proveedor - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.proveedores.index')" class="text-2xl hover:opacity-75">←</Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Editar Proveedor</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">Modifica los datos del proveedor</p>
                        </div>
                    </div>
                </div>

                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="nombre" value="Nombre *" />
                            <TextInput id="nombre" v-model="form.nombre" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.nombre" />
                        </div>

                        <div>
                            <InputLabel for="detalles" value="Detalles" />
                            <textarea
                                id="detalles"
                                v-model="form.detalles"
                                rows="3"
                                class="mt-1 block w-full px-4 py-2 rounded-lg border"
                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.detalles" />
                        </div>

                        <div>
                            <InputLabel for="correo" value="Correo" />
                            <TextInput id="correo" v-model="form.correo" type="email" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.correo" />
                        </div>

                        <div>
                            <InputLabel for="celular" value="Celular" />
                            <TextInput id="celular" v-model="form.celular" type="text" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.celular" />
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" class="btn-primary px-6 py-2 rounded-lg font-medium transition" :disabled="form.processing">
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Proveedor' }}
                            </button>
                            <Link :href="route('restaurant.proveedores.index')" class="px-6 py-2 rounded-lg font-medium transition" style="background-color: var(--color-border); color: var(--color-text-primary)">
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>

            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>
