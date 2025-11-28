<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    tasa_interes_diario: '',
    plazo_dias: '',
});

function submit() {
    form.post(route('restaurant.planes.store'), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Crear Plan de Crédito - Restaurante" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.planes.index')" class="text-2xl hover:opacity-75">← </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">Crear Plan de Crédito</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                Registra un nuevo plan de crédito para clientes
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card rounded-lg shadow-sm p-6 max-w-2xl">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="nombre" value="Nombre del plan *" />
                            <TextInput
                                id="nombre"
                                v-model="form.nombre"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Ej: Plan 30 días, Plan 60 días"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.nombre" />
                        </div>

                        <div>
                            <InputLabel for="tasa_interes_diario" value="Tasa de interés diario (%) *" />
                            <TextInput
                                id="tasa_interes_diario"
                                v-model="form.tasa_interes_diario"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="mt-1 block w-full"
                                placeholder="Ej: 1.00 para 1%"
                                required
                            />
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                Se aplica sobre el saldo pendiente del crédito
                            </p>
                            <InputError class="mt-2" :message="form.errors.tasa_interes_diario" />
                        </div>

                        <div>
                            <InputLabel for="plazo_dias" value="Plazo en días *" />
                            <TextInput
                                id="plazo_dias"
                                v-model="form.plazo_dias"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                                placeholder="Ej: 30, 60, 90"
                                required
                            />
                            <p class="text-xs mt-1" style="color: var(--color-text-secondary)">
                                Días límite para pagar el crédito
                            </p>
                            <InputError class="mt-2" :message="form.errors.plazo_dias" />
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <button
                                type="submit"
                                class="btn-primary px-6 py-2 rounded-lg font-medium transition"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Guardando...' : 'Crear Plan' }}
                            </button>
                            <Link
                                :href="route('restaurant.planes.index')"
                                class="px-6 py-2 rounded-lg font-medium transition"
                                style="background-color: var(--color-border); color: var(--color-text-primary)"
                            >
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
