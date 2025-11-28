<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    lnNroTran: String,
    laQrImage: String,
    credito_id: Number,   // 👈 NECESARIO
});


const estadoTexto = ref("Consultando estado...");

async function consultarEstadoTransaccion(transaccionId) {
    let estado = 1;

    while (estado === 1) {
        const response = await axios.post(
            route('restaurant.pagos.consultar'),
            { pagofacilTransactionId: transaccionId }
        );

        const data = response.data;
        console.log("🔍 Estado recibido:", data);

        if (data.error) {
            estadoTexto.value = "❌ " + data.message;
            break;
        }

        const info = data.values;
        let estadoPago = info.paymentStatus ?? 1;

        switch (estadoPago) {
            case 1: estadoTexto.value = "⏳ Pendiente de pago..."; break;
            case 2: estadoTexto.value = "✅ Pago realizado correctamente"; break;
            case 3: estadoTexto.value = "❌ Pago cancelado"; break;
            case 4: estadoTexto.value = "⌛ Pago expirado"; break;
            case 5: estadoTexto.value = "⏳ Pago en revisión..."; break;
        }

        estado = estadoPago;

        if (estado === 2) {
            await axios.post(route('restaurant.pagos.completar'), {
                transaccion: transaccionId,
                credito_id: props.credito_id   // 👈 AGREGAR ESTO
            });



            window.location.href = route('restaurant.pagos.success', {
                transaccion: transaccionId,
            });


            break;
        }

        if ([3, 4, 5].includes(estado)) break;

        await new Promise(r => setTimeout(r, 3000));
    }
}

onMounted(() => consultarEstadoTransaccion(props.lnNroTran));
</script>

<template>
    <AppLayout>

        <Head title="Pago QR" />

        <div class="text-center mt-10">
            <h2 class="text-3xl font-bold mb-6">Transacción: {{ lnNroTran }}</h2>
            <img :src="laQrImage" class="w-80 mx-auto shadow rounded-xl" />
            <p class="text-xl mt-6">{{ estadoTexto }}</p>
        </div>
    </AppLayout>
</template>
