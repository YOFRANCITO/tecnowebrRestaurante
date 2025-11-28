<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    transaccion: Number,
    monto: {
        type: Number,
        default: 0,
    },
});
</script>

<template>
    <AppLayout>
        <div class="max-w-lg mx-auto mt-16 bg-green-50 p-8 rounded-xl shadow text-center">
            <h1 class="text-3xl font-bold text-green-700 mb-4">¡Pago Exitoso! 🎉</h1>

            <div class="text-left space-y-2 text-gray-700">
                <p><strong>Nro Transacción:</strong> {{ transaccion }}</p>
                <p><strong>Monto Pagado:</strong> Bs. {{ Number(monto).toFixed(2) }}</p>
            </div>

            <div class="mt-6">
                <Link 
                    :href="route('restaurant.pagos.index')" 
                    class="text-blue-600 hover:underline text-lg"
                >
                    Volver a mis pagos
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
