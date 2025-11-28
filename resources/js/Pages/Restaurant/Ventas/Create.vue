<script setup>
import { ref, computed, reactive } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    mesas: Array,
    productos: Array,
    planes: Array,
    visitCount: Number,
});

const page = usePage();
const user = page.props.auth.user;

// Función para verificar el rol del usuario
const hasRole = (roleName) => {
    return Array.isArray(user?.roles) && user.roles.some(r => r.name === roleName);
};

const isAdministrador = hasRole('administrador');
const isCliente = hasRole('cliente');

// Estado para el proceso en pasos (solo para clientes)
const paso = ref(0);
const steps = [
    { nombre: 'Productos', icono: '🍔' },
    { nombre: 'Mesa', icono: '🪑' },
    { nombre: 'Pago', icono: '💳' },
    { nombre: 'Confirmar', icono: '✅' },
];

// Carrito de compras
const carrito = ref([]);

// Formulario
const form = useForm({
    mesa_id: '',
    tipo_pago: 'Inmediato',
    plan_id: '',
    detalles: [],
});

// Computed
const total = computed(() => {
    return carrito.value.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
});

const mesaSeleccionada = computed(() => {
    return props.mesas.find(m => m.id == form.mesa_id);
});

// Funciones del carrito
function agregarAlCarrito(producto) {
    const existe = carrito.value.find(item => item.id === producto.id);
    if (existe) {
        existe.cantidad++;
    } else {
        carrito.value.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: producto.precio_venta,
            cantidad: 1,
        });
    }
}

function incrementar(item) {
    item.cantidad++;
}

function decrementar(item) {
    if (item.cantidad > 1) {
        item.cantidad--;
    }
}

function eliminar(item) {
    const index = carrito.value.indexOf(item);
    carrito.value.splice(index, 1);
}

function vaciarCarrito() {
    carrito.value = [];
}

// Navegación de pasos
function siguiente() {
    if (paso.value === 0 && carrito.value.length === 0) {
        alert('Agrega al menos un producto al carrito');
        return;
    }
    if (paso.value === 1 && !form.mesa_id) {
        alert('Selecciona una mesa');
        return;
    }
    if (paso.value === 2 && form.tipo_pago === 'Crédito' && !form.plan_id) {
        alert('Selecciona un plan de crédito');
        return;
    }
    paso.value++;
}

function anterior() {
    paso.value--;
}

// Submit
function submit() {
    // Convertir carrito a detalles
    form.detalles = carrito.value.map(item => ({
        producto_id: item.id,
        cantidad: item.cantidad,
    }));
    
    form.post(route('restaurant.ventas.store'));
}

// Para administradores - mantener funcionalidad antigua
const productoSeleccionado = ref('');
const cantidadSeleccionada = ref(1);

function agregarProductoAdmin() {
    if (!productoSeleccionado.value || cantidadSeleccionada.value <= 0) return;
    
    const existe = form.detalles.find(d => d.producto_id == productoSeleccionado.value);
    if (existe) {
        existe.cantidad = parseFloat(existe.cantidad) + parseFloat(cantidadSeleccionada.value);
    } else {
        form.detalles.push({
            producto_id: productoSeleccionado.value,
            cantidad: parseFloat(cantidadSeleccionada.value),
        });
    }
    
    productoSeleccionado.value = '';
    cantidadSeleccionada.value = 1;
}

function eliminarProductoAdmin(index) {
    form.detalles.splice(index, 1);
}

function getProducto(productoId) {
    return props.productos.find(p => p.id == productoId);
}

const totalAdmin = computed(() => {
    return form.detalles.reduce((sum, detalle) => {
        const producto = props.productos.find(p => p.id == detalle.producto_id);
        return sum + (producto ? producto.precio_venta * detalle.cantidad : 0);
    }, 0);
});
</script>

<template>
    <AppLayout>
        <Head :title="isCliente ? 'Realizar Pedido - Restaurante' : 'Nueva Venta - Restaurante'" />

        <div class="flex flex-col min-h-screen">
            <div class="flex-1">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('restaurant.ventas.index')" class="text-2xl hover:opacity-75">← </Link>
                        <div>
                            <h1 class="text-3xl font-bold" style="color: var(--color-text-primary)">
                                {{ isCliente ? '🍽️ Realizar Pedido' : 'Nueva Venta' }}
                            </h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-secondary)">
                                {{ isCliente ? 'Selecciona tus productos favoritos' : 'Registra una nueva venta' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- VISTA PARA CLIENTES (E-commerce) -->
                <div v-if="isCliente">
                    <!-- Stepper -->
                    <div class="flex items-center justify-center mb-8">
                        <div v-for="(step, index) in steps" :key="index" class="flex items-center">
                            <!-- Círculo del Paso -->
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold transition-all duration-300"
                                     :class="paso > index ? 'bg-green-500 text-white' : 
                                             paso === index ? 'bg-blue-500 text-white ring-4 ring-blue-200' : 
                                             'bg-gray-300 text-gray-600'">
                                    <span v-if="paso > index">✓</span>
                                    <span v-else>{{ index + 1 }}</span>
                                </div>
                                <span class="text-xs mt-2 font-medium" :class="paso === index ? 'text-blue-600' : 'text-gray-600'">
                                    {{ step.nombre }}
                                </span>
                            </div>
                            
                            <!-- Línea Conectora -->
                            <div v-if="index < steps.length - 1" 
                                 class="w-24 h-1 mx-2 transition-all duration-300"
                                 :class="paso > index ? 'bg-green-500' : 'bg-gray-300'">
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del Paso Actual -->
                    <div>
                        <!-- PASO 1: CATÁLOGO DE PRODUCTOS -->
                        <div v-if="paso === 0">
                            <h2 class="text-2xl font-bold mb-6">{{ steps[0].icono }} Selecciona tus Productos</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                                <div v-for="producto in productos" :key="producto.id" 
                                     class="card rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                                    
                                    <!-- Imagen del Producto -->
                                    <div class="relative h-32 bg-gradient-to-br from-orange-100 to-yellow-100">
                                        <img v-if="producto.imagen_url" 
                                             :src="producto.imagen_url" 
                                             :alt="producto.nombre"
                                             class="w-full h-full object-cover" />
                                        <div v-else class="flex items-center justify-center h-full text-4xl">
                                            🍔
                                        </div>
                                        
                                        <!-- Badge de Stock -->
                                        <div class="absolute top-1 right-1 px-2 py-0.5 rounded-full text-xs font-bold"
                                             :class="producto.stock > 10 ? 'bg-green-500 text-white' : producto.stock > 0 ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white'">
                                            {{ producto.stock }}
                                        </div>
                                    </div>
                                    
                                    <!-- Información del Producto -->
                                    <div class="p-3">
                                        <h3 class="font-bold text-sm mb-2 line-clamp-2">{{ producto.nombre }}</h3>
                                        
                                        <!-- Precio -->
                                        <div class="mb-3">
                                            <span class="text-xl font-bold text-green-600">
                                                Bs. {{ producto.precio_venta }}
                                            </span>
                                        </div>
                                        
                                        <!-- Botón Agregar -->
                                        <button @click="agregarAlCarrito(producto)"
                                                :disabled="producto.stock <= 0"
                                                class="w-full py-1.5 text-sm rounded-lg font-bold transition-all duration-200"
                                                :class="producto.stock > 0 
                                                    ? 'bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg' 
                                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                                            <span v-if="producto.stock > 0">🛒 Agregar</span>
                                            <span v-else>❌ Agotado</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PASO 2: SELECCIÓN DE MESA -->
                        <div v-if="paso === 1">
                            <h2 class="text-2xl font-bold mb-6">{{ steps[1].icono }} Selecciona tu Mesa</h2>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <button v-for="mesa in mesas" :key="mesa.id"
                                        @click="form.mesa_id = mesa.id"
                                        class="p-6 rounded-xl border-2 transition-all duration-200"
                                        :class="form.mesa_id === mesa.id 
                                            ? 'border-green-500 bg-green-50 shadow-lg transform scale-105' 
                                            : 'border-gray-300 hover:border-blue-400 hover:shadow-md'">
                                    
                                    <div class="text-4xl mb-2">🪑</div>
                                    <div class="font-bold text-lg">Mesa {{ mesa.codigo }}</div>
                                    <div class="text-sm text-gray-600">{{ mesa.capacidad }} personas</div>
                                    
                                    <div v-if="form.mesa_id === mesa.id" class="mt-2 text-green-600 font-bold">
                                        ✓ Seleccionada
                                    </div>
                                </button>
                            </div>
                            
                            <!-- Navegación -->
                            <div class="flex justify-between mt-8">
                                <button @click="anterior" 
                                        class="px-6 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 font-medium transition">
                                    ← Anterior
                                </button>
                                <button @click="siguiente" 
                                        class="px-6 py-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 font-medium transition">
                                    Siguiente → Método de Pago
                                </button>
                            </div>
                        </div>

                        <!-- PASO 3: MÉTODO DE PAGO -->
                        <div v-if="paso === 2">
                            <h2 class="text-2xl font-bold mb-6">{{ steps[2].icono }} Método de Pago</h2>
                            
                            <div class="max-w-2xl mx-auto space-y-4">
                                <button @click="form.tipo_pago = 'Inmediato'"
                                        class="w-full p-6 rounded-xl border-2 transition-all duration-200 text-left"
                                        :class="form.tipo_pago === 'Inmediato' 
                                            ? 'border-green-500 bg-green-50 shadow-lg' 
                                            : 'border-gray-300 hover:border-blue-400'">
                                    <div class="flex items-center gap-4">
                                        <div class="text-4xl">💵</div>
                                        <div class="flex-1">
                                            <div class="font-bold text-lg">Pago Inmediato</div>
                                            <div class="text-sm text-gray-600">Paga al momento de recibir tu pedido</div>
                                        </div>
                                        <div v-if="form.tipo_pago === 'Inmediato'" class="text-green-600 font-bold text-2xl">
                                            ✓
                                        </div>
                                    </div>
                                </button>

                                <button v-if="planes && planes.length > 0"
                                        @click="form.tipo_pago = 'Crédito'"
                                        class="w-full p-6 rounded-xl border-2 transition-all duration-200 text-left"
                                        :class="form.tipo_pago === 'Crédito' 
                                            ? 'border-green-500 bg-green-50 shadow-lg' 
                                            : 'border-gray-300 hover:border-blue-400'">
                                    <div class="flex items-center gap-4">
                                        <div class="text-4xl">💳</div>
                                        <div class="flex-1">
                                            <div class="font-bold text-lg">Pago a Crédito</div>
                                            <div class="text-sm text-gray-600">Paga en cuotas según el plan seleccionado</div>
                                        </div>
                                        <div v-if="form.tipo_pago === 'Crédito'" class="text-green-600 font-bold text-2xl">
                                            ✓
                                        </div>
                                    </div>
                                </button>

                                <!-- Selección de Plan de Crédito -->
                                <div v-if="form.tipo_pago === 'Crédito'" class="mt-4">
                                    <label class="block text-sm font-medium mb-2">Selecciona un Plan de Crédito</label>
                                    <select v-model="form.plan_id"
                                            class="w-full px-4 py-3 rounded-lg border"
                                            style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)">
                                        <option value="">-- Selecciona un plan --</option>
                                        <option v-for="plan in planes" :key="plan.id" :value="plan.id">
                                            {{ plan.nombre }} ({{ plan.tasa_interes_diario }}% diario - {{ plan.plazo_dias }} días)
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.plan_id" />
                                </div>
                            </div>
                            
                            <!-- Navegación -->
                            <div class="flex justify-between mt-8">
                                <button @click="anterior" 
                                        class="px-6 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 font-medium transition">
                                    ← Anterior
                                </button>
                                <button @click="siguiente" 
                                        class="px-6 py-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 font-medium transition">
                                    Siguiente → Confirmar
                                </button>
                            </div>
                        </div>

                        <!-- PASO 4: CONFIRMAR PEDIDO -->
                        <div v-if="paso === 3">
                            <div class="max-w-2xl mx-auto">
                                <div class="card rounded-2xl shadow-xl p-8">
                                    <div class="text-center mb-6">
                                        <div class="text-6xl mb-4">🎉</div>
                                        <h2 class="text-3xl font-bold mb-2">¡Confirma tu Pedido!</h2>
                                        <p class="text-gray-600">Revisa los detalles antes de confirmar</p>
                                    </div>
                                    
                                    <!-- Resumen -->
                                    <div class="space-y-4 mb-6">
                                        <div class="flex justify-between p-4 bg-gray-50 rounded-lg">
                                            <span class="font-medium">🪑 Mesa:</span>
                                            <span class="font-bold">{{ mesaSeleccionada?.codigo }}</span>
                                        </div>
                                        
                                        <div class="flex justify-between p-4 bg-gray-50 rounded-lg">
                                            <span class="font-medium">💳 Pago:</span>
                                            <span class="font-bold">{{ form.tipo_pago === 'Crédito' ? '💳 Crédito' : '💵 Inmediato' }}</span>
                                        </div>
                                        
                                        <div class="border-t pt-4">
                                            <h3 class="font-bold mb-3">🛒 Productos:</h3>
                                            <div v-for="item in carrito" :key="item.id" 
                                                 class="flex justify-between mb-2">
                                                <span>{{ item.nombre }} x{{ item.cantidad }}</span>
                                                <span class="font-bold">Bs. {{ (item.precio * item.cantidad).toFixed(2) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="border-t pt-4 flex justify-between text-2xl font-bold">
                                            <span>Total:</span>
                                            <span class="text-green-600">Bs. {{ total.toFixed(2) }}</span>
                                        </div>
                                    </div>
                                    
                                    <button @click="submit"
                                            :disabled="form.processing"
                                            class="w-full py-4 rounded-xl font-bold text-white text-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50">
                                        {{ form.processing ? 'Procesando...' : '✅ Confirmar Pedido' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carrito Flotante -->
                    <div v-if="carrito.length > 0 && paso < 3" 
                         class="fixed right-4 top-24 w-80 bg-white rounded-2xl shadow-2xl p-6 z-50 max-h-[80vh] overflow-y-auto">
                        
                        <!-- Header del Carrito -->
                        <div class="flex items-center justify-between mb-4 pb-4 border-b">
                            <h3 class="text-xl font-bold flex items-center gap-2">
                                🛒 Tu Carrito
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ carrito.length }}
                                </span>
                            </h3>
                            <button @click="vaciarCarrito" class="text-red-500 hover:text-red-700 text-xl">
                                🗑️
                            </button>
                        </div>
                        
                        <!-- Items del Carrito -->
                        <div class="space-y-4 mb-6">
                            <div v-for="item in carrito" :key="item.id" 
                                 class="flex gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold truncate">{{ item.nombre }}</h4>
                                    <p class="text-sm text-gray-600">Bs. {{ item.precio }}</p>
                                    
                                    <!-- Controles de Cantidad -->
                                    <div class="flex items-center gap-2 mt-2">
                                        <button @click="decrementar(item)" 
                                                class="w-7 h-7 rounded-full bg-red-500 text-white hover:bg-red-600 flex items-center justify-center font-bold">
                                            −
                                        </button>
                                        <span class="font-bold w-8 text-center">{{ item.cantidad }}</span>
                                        <button @click="incrementar(item)"
                                                class="w-7 h-7 rounded-full bg-green-500 text-white hover:bg-green-600 flex items-center justify-center font-bold">
                                            +
                                        </button>
                                        <button @click="eliminar(item)"
                                                class="ml-auto text-red-500 hover:text-red-700">
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Subtotal -->
                                <div class="text-right flex-shrink-0">
                                    <p class="font-bold text-green-600">
                                        Bs. {{ (item.precio * item.cantidad).toFixed(2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total -->
                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center text-xl font-bold mb-4">
                                <span>Total:</span>
                                <span class="text-green-600">Bs. {{ total.toFixed(2) }}</span>
                            </div>
                            
                            <!-- Botón Siguiente -->
                            <button v-if="paso === 0" @click="siguiente" 
                                    class="w-full py-3 rounded-lg bg-blue-500 text-white hover:bg-blue-600 font-bold transition shadow-lg hover:shadow-xl">
                                Siguiente → Elegir Mesa
                            </button>
                        </div>
                    </div>
                </div>

                <!-- VISTA PARA ADMINISTRADORES (Formulario tradicional) -->
                <form v-else @submit.prevent="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Formulario Principal -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Información de la Venta -->
                            <div class="card rounded-lg shadow-sm p-6">
                                <h2 class="text-xl font-bold mb-4">Información de la Venta</h2>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">🪑 Mesa *</label>
                                        <select v-model="form.mesa_id"
                                                class="w-full px-4 py-2 rounded-lg border"
                                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)"
                                                required>
                                            <option value="">-- Seleccione una mesa --</option>
                                            <option v-for="mesa in mesas" :key="mesa.id" :value="mesa.id">
                                                {{ mesa.codigo }} ({{ mesa.capacidad }} personas)
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.mesa_id" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Tipo de Pago *</label>
                                        <div class="mt-1 block w-full px-4 py-2 rounded-lg border bg-gray-100" style="border-color: var(--color-border); color: var(--color-text-primary)">
                                            <span class="font-medium">💵 Pago Inmediato</span>
                                            <p class="text-xs text-gray-500 mt-1">Como administrador, solo puedes usar pago inmediato</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Agregar Productos -->
                            <div class="card rounded-lg shadow-sm p-6">
                                <h2 class="text-xl font-bold mb-4">Productos</h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">🍔 Producto</label>
                                        <select v-model="productoSeleccionado"
                                                class="w-full px-4 py-2 rounded-lg border"
                                                style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)">
                                            <option value="">Seleccionar producto</option>
                                            <option v-for="producto in productos" :key="producto.id" :value="producto.id">
                                                {{ producto.nombre }} - Bs. {{ producto.precio_venta }} (Stock: {{ producto.stock_actual }})
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Cantidad</label>
                                        <input v-model="cantidadSeleccionada"
                                               type="number"
                                               min="1"
                                               step="0.01"
                                               class="w-full px-4 py-2 rounded-lg border"
                                               style="background-color: var(--color-bg-secondary); border-color: var(--color-border); color: var(--color-text-primary)" />
                                    </div>
                                </div>

                                <button type="button"
                                        @click="agregarProductoAdmin"
                                        class="btn-primary px-6 py-2 rounded-lg font-medium transition">
                                    + Agregar Producto
                                </button>

                                <!-- Lista de Productos Agregados -->
                                <div class="mt-6">
                                    <h3 class="text-lg font-bold mb-4">Productos Agregados</h3>
                                    <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Producto</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Precio</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Cantidad</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Subtotal</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(detalle, index) in form.detalles" :key="index" class="border-t">
                                                <td class="px-4 py-2">{{ getProducto(detalle.producto_id)?.nombre }}</td>
                                                <td class="px-4 py-2">Bs. {{ getProducto(detalle.producto_id)?.precio_venta }}</td>
                                                <td class="px-4 py-2">{{ detalle.cantidad }}</td>
                                                <td class="px-4 py-2 font-medium">
                                                    Bs. {{ (getProducto(detalle.producto_id)?.precio_venta * detalle.cantidad).toFixed(2) }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    <button type="button"
                                                            @click="eliminarProductoAdmin(index)"
                                                            class="text-red-600 hover:text-red-800">
                                                        🗑️
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="form.detalles.length === 0">
                                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                    No se han agregado productos
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div class="space-y-6">
                            <div class="card rounded-lg shadow-sm p-6 sticky top-4">
                                <h2 class="text-xl font-bold mb-4">Resumen</h2>
                                
                                <div class="flex justify-between items-center pb-4 border-b mb-4">
                                    <span class="text-lg font-medium">Total:</span>
                                    <span class="text-2xl font-bold text-green-600">Bs. {{ totalAdmin.toFixed(2) }}</span>
                                </div>

                                <div class="space-y-3">
                                    <button type="submit"
                                            :disabled="form.processing || form.detalles.length === 0"
                                            class="w-full btn-primary px-6 py-3 rounded-lg font-medium transition disabled:opacity-50">
                                        {{ form.processing ? 'Procesando...' : 'Registrar Venta' }}
                                    </button>
                                    <Link :href="route('restaurant.ventas.index')"
                                          class="block w-full text-center px-6 py-3 rounded-lg font-medium transition"
                                          style="background-color: var(--color-border); color: var(--color-text-primary)">
                                        Cancelar
                                    </Link>
                                </div>

                                <InputError class="mt-2" :message="form.errors.stock" />
                                <InputError class="mt-2" :message="form.errors.error" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <PageFooter :visitCount="visitCount" />
        </div>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
