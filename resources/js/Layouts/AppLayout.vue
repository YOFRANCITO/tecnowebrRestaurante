<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import GlobalSearch from '@/Components/GlobalSearch.vue';

const page = usePage();
const user = page.props.auth.user;

const showingNavigationDropdown = ref(false);
const currentTheme = ref('theme-light');
const showThemeSelector = ref(false);
const title = 'Sistema de Restaurante';

const themes = [
    {
        id: 'auto',
        name: 'Auto',
        icon: '🌗',
        description: 'Cambia automáticamente según la hora del día'
    },
    {
        id: 'theme-light',
        name: 'Claro',
        icon: '☀️',
        description: 'Tema claro y profesional'
    },
    {
        id: 'theme-dark',
        name: 'Oscuro',
        icon: '🌙',
        description: 'Tema oscuro para reducir fatiga visual'
    },
    {
        id: 'theme-kids',
        name: 'Niños',
        icon: '🎨',
        description: 'Tema colorido y divertido para niños'
    },
    {
        id: 'theme-young',
        name: 'Jóvenes',
        icon: '🚀',
        description: 'Tema vibrante y moderno para jóvenes'
    },
    {
        id: 'theme-adult',
        name: 'Adultos',
        icon: '💼',
        description: 'Tema sobrio y profesional para adultos'
    }
];

// Accesibilidad
const showAccessibilityPanel = ref(false);
const fontSize = ref('font-normal');
const contrast = ref('normal');

const fontSizes = [
    { id: 'font-small', name: 'Pequeño', size: '14px' },
    { id: 'font-normal', name: 'Normal', size: '16px' },
    { id: 'font-large', name: 'Grande', size: '18px' },
    { id: 'font-xlarge', name: 'Extra Grande', size: '20px' }
];

const contrastLevels = [
    { id: 'normal', name: 'Normal' },
    { id: 'contrast-high', name: 'Alto' },
    { id: 'contrast-very-high', name: 'Muy Alto' }
];

function getAutoTheme() {
    const hour = new Date().getHours();
    return (hour >= 6 && hour < 18) ? 'theme-light' : 'theme-dark';
}

function setTheme(newTheme) {
    currentTheme.value = newTheme;
    
    // Si es auto, determinar tema basado en hora
    const actualTheme = newTheme === 'auto' ? getAutoTheme() : newTheme;
    
    // Remover todas las clases de tema anteriores
    document.documentElement.classList.remove('theme-light', 'theme-dark', 'theme-kids', 'theme-young', 'theme-adult');
    
    // Agregar la nueva clase de tema
    if (actualTheme !== 'auto') {
        document.documentElement.classList.add(actualTheme);
    }
    
    localStorage.setItem('restaurant-theme', newTheme);
    showThemeSelector.value = false;
}

function setFontSize(newSize) {
    fontSize.value = newSize;
    document.documentElement.classList.remove('font-small', 'font-normal', 'font-large', 'font-xlarge');
    document.documentElement.classList.add(newSize);
    localStorage.setItem('restaurant-font-size', newSize);
}

function setContrast(newContrast) {
    contrast.value = newContrast;
    document.documentElement.classList.remove('contrast-high', 'contrast-very-high');
    if (newContrast !== 'normal') {
        document.documentElement.classList.add(newContrast);
    }
    localStorage.setItem('restaurant-contrast', newContrast);
}

function resetAccessibility() {
    setFontSize('font-normal');
    setContrast('normal');
}

onMounted(() => {
    // Cargar tema
    const savedTheme = localStorage.getItem('restaurant-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = savedTheme || (prefersDark ? 'theme-dark' : 'theme-light');
    setTheme(theme);
    
    // Cargar accesibilidad
    const savedFontSize = localStorage.getItem('restaurant-font-size') || 'font-normal';
    const savedContrast = localStorage.getItem('restaurant-contrast') || 'normal';
    setFontSize(savedFontSize);
    setContrast(savedContrast);
    
    // Si el tema es auto, actualizar cada hora
    if (savedTheme === 'auto') {
        setInterval(() => {
            if (currentTheme.value === 'auto') {
                setTheme('auto');
            }
        }, 60000); // Revisar cada minuto
    }
});

const logout = () => {
    router.post(route('logout'));
};

const hasRole = (roleName) => {
    return Array.isArray(user?.roles) && user.roles.some(r => r.name === roleName);
};

// Buscador global
const searchQuery = ref('');
const searchResults = ref([]);
const searchActive = ref(false);

// Ítems del sidebar para búsqueda (adaptado al sistema de restaurante)
const sidebarItems = ref([]);

// Construir dinámicamente los items del sidebar según el rol del usuario
onMounted(() => {
    const items = [];
    
    // Administrador
    if (hasRole('administrador')) {
        items.push(
            { nombre: 'Usuarios', ruta: route('restaurant.users.index') },
            { nombre: 'Reportes', ruta: route('restaurant.reportes.dashboard') },
            { nombre: 'Ventas', ruta: route('restaurant.reportes.ventas') },
            { nombre: 'Compras', ruta: route('restaurant.reportes.compras') },
            { nombre: 'Créditos Stats', ruta: route('restaurant.reportes.creditos') },
            { nombre: 'Mesas', ruta: route('restaurant.mesas.index') },
            { nombre: 'Planes', ruta: route('restaurant.planes.index') },
            { nombre: 'Mis Ventas', ruta: route('restaurant.ventas.index') },
            { nombre: 'Mis Créditos', ruta: route('restaurant.creditos.index') },
            { nombre: 'Mis Pagos', ruta: route('restaurant.pagos.index') }
        );
    }
    
    // Almacenero
    if (hasRole('administrador') || hasRole('almacenero')) {
        items.push(
            { nombre: 'Marcas', ruta: route('restaurant.marcas.index') },
            { nombre: 'Insumos', ruta: route('restaurant.insumos.index') },
            { nombre: 'Movimientos', ruta: route('restaurant.movimientos.index') },
            { nombre: 'Proveedores', ruta: route('restaurant.proveedores.index') },
            { nombre: 'Compras', ruta: route('restaurant.compras.index') },
            { nombre: 'Productos', ruta: route('restaurant.productos.index') }
        );
    }
    
    // Cliente
    if (hasRole('cliente')) {
        items.push(
            { nombre: 'Comprar', ruta: route('restaurant.ventas.index') },
            { nombre: 'Mis Créditos', ruta: route('restaurant.creditos.index') },
            { nombre: 'Mis Pagos', ruta: route('restaurant.pagos.index') }
        );
    }
    
    // Mesero
    if (hasRole('mesero')) {
        items.push(
            { nombre: 'Órdenes', ruta: route('restaurant.ordenes.index') }
        );
    }
    
    sidebarItems.value = items;
});

// Filtrar los ítems del sidebar según el texto
function updateSearchResults() {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        searchActive.value = false;
        return;
    }
    // Busca por coincidencia en el nombre
    searchResults.value = sidebarItems.value.filter(item =>
        item.nombre.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
    searchActive.value = searchResults.value.length > 0;
}

// Navegar al hacer click en un resultado
function goToSidebarItem(item) {
    searchQuery.value = '';
    searchResults.value = [];
    searchActive.value = false;
    router.visit(item.ruta);
}

</script>

<template>
    <div>

        <Head :title="title" />

        <div class="min-h-screen flex theme-container">
            <!-- Sidebar -->

            <aside class="w-64 sidebar text-white p-6 hidden md:flex flex-col shadow-xl min-h-screen">
                <div class="mb-8 flex items-center gap-2">
                    <Link :href="route('restaurant.reportes.dashboard')">
                    <ApplicationMark class="h-10 w-auto" />
                    </Link>
                    <span class="font-bold text-lg tracking-wide">Restaurante</span>
                </div>

                <!-- Theme Selector -->
                <div class="mb-6 relative">
                    <button @click="showThemeSelector = !showThemeSelector"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition theme-selector-button">
                        <span class="text-lg">{{themes.find(t => t.id === currentTheme)?.icon}}</span>
                        <div class="flex-1 text-left">
                            <div class="font-medium">{{themes.find(t => t.id === currentTheme)?.name}}</div>
                            <div class="text-xs opacity-75">Cambiar tema</div>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': showThemeSelector }"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <!-- Theme Dropdown -->
                    <div v-if="showThemeSelector"
                        class="absolute top-full left-0 right-0 mt-2 theme-dropdown rounded-lg shadow-lg z-50">
                        <div class="p-2 space-y-1">
                            <button v-for="theme in themes" :key="theme.id" @click="setTheme(theme.id)"
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg transition theme-option"
                                :class="{ 'theme-option-active': currentTheme === theme.id }">
                                <span class="text-lg">{{ theme.icon }}</span>
                                <div class="flex-1 text-left">
                                    <div class="font-medium">{{ theme.name }}</div>
                                    <div class="text-xs opacity-75">{{ theme.description }}</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <nav class="flex-1 space-y-2">
                    <!-- Gestión de Usuarios del Restaurante (solo administrador) -->
                    <Link v-if="hasRole('administrador')" :href="route('restaurant.users.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.users.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    🍽️ Usuarios
                    </Link>

                    <!-- Gestión de Marcas (administrador y almacenero) -->
                    <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.marcas.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.marcas.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    🏷️ Marcas
                    </Link>

                    <!-- Gestión de Insumos (administrador y almacenero) -->
                    <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.insumos.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.insumos.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    📦 Insumos
                    </Link>

                    <!-- Gestión de Movimientos (administrador y almacenero) -->
                    <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.movimientos.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.movimientos.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    📋 Movimientos
                    </Link>

                    <!-- Gestión de Proveedores (administrador y almacenero) -->
                    <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.proveedores.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.proveedores.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    🏭 Proveedores
                    </Link>

                    <!-- Gestión de Compras (administrador y almacenero) -->
                    <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.compras.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.compras.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    🛒 Compras
                    </Link>

                    <!-- Gestión de Productos (SOLO administrador) -->
                    <Link v-if="hasRole('administrador')" :href="route('restaurant.productos.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.productos.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    🍔 Productos
                    </Link>

                    <!-- Reportes y Estadísticas (solo administrador) -->
                    <Link v-if="hasRole('administrador')" :href="route('restaurant.reportes.dashboard')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.reportes.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    📊 Reportes
                    </Link>

                    <!-- Gestión de Mesas (solo administrador) -->
                    <Link v-if="hasRole('administrador')" :href="route('restaurant.mesas.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.mesas.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    🪑 Mesas
                    </Link>

                    <!-- Gestión de Planes (solo administrador) -->
                    <Link v-if="hasRole('administrador')" :href="route('restaurant.planes.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.planes.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    📋 Planes de Crédito
                    </Link>

                    <!-- Gestión de Ventas (administrador y cliente) -->
                    <Link v-if="hasRole('administrador') || hasRole('cliente')" :href="route('restaurant.ventas.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.ventas.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    💰 {{ hasRole('cliente') ? 'Comprar' : 'Ventas' }}
                    </Link>

                    <!-- Gestión de Órdenes (solo mesero) -->
                    <Link v-if="hasRole('mesero')" :href="route('restaurant.ordenes.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.ordenes.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    📝 Órdenes
                    </Link>

                    <!-- Mis Créditos (administrador y cliente) -->
                    <Link v-if="hasRole('administrador') || hasRole('cliente')" :href="route('restaurant.creditos.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.creditos.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    💳 {{ hasRole('cliente') ? 'Mis Créditos' : 'Créditos' }}
                    </Link>

                    <!-- Mis Pagos (administrador y cliente) -->
                    <Link v-if="hasRole('administrador') || hasRole('cliente')" :href="route('restaurant.pagos.index')"
                        class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                        :class="{ 'nav-link-active': route().current('restaurant.pagos.*') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    💵 {{ hasRole('cliente') ? 'Mis Pagos' : 'Pagos' }}
                    </Link>
                </nav>

                <div class="mt-auto pt-6 border-t sidebar-footer">
                    <span class="text-xs opacity-75">© 2024 Biblioteca</span>
                </div>
            </aside>

            <!-- Sidebar móvil -->
            <aside
                v-if="showingNavigationDropdown"
                class="fixed inset-0 z-50 bg-black bg-opacity-40 flex md:hidden"
            >
                <div class="w-64 bg-gradient-to-b from-indigo-900 to-indigo-700 text-white p-6 flex flex-col shadow-xl min-h-screen">
                    <div class="mb-8 flex items-center gap-2">
                        <Link :href="route('dashboard')" @click="showingNavigationDropdown = false">
                            <ApplicationMark class="h-10 w-auto" />
                        </Link>
                        <span class="font-bold text-lg tracking-wide">Biblioteca</span>
                    </div>

                    <!-- Selector de tema (COPIA Y PEGA ESTE BLOQUE AQUÍ) -->
                    <div class="mb-6 relative">
                        <button @click="showThemeSelector = !showThemeSelector"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition theme-selector-button">
                            <span class="text-lg">{{themes.find(t => t.id === currentTheme)?.icon}}</span>
                            <div class="flex-1 text-left">
                                <div class="font-medium">{{themes.find(t => t.id === currentTheme)?.name}}</div>
                                <div class="text-xs opacity-75">Cambiar tema</div>
                            </div>
                            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': showThemeSelector }"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div v-if="showThemeSelector"
                            class="absolute top-full left-0 right-0 mt-2 theme-dropdown rounded-lg shadow-lg z-50">
                            <div class="p-2 space-y-1">
                                <button v-for="theme in themes" :key="theme.id" @click="setTheme(theme.id)"
                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg transition theme-option"
                                    :class="{ 'theme-option-active': currentTheme === theme.id }">
                                    <span class="text-lg">{{ theme.icon }}</span>
                                    <div class="flex-1 text-left">
                                        <div class="font-medium">{{ theme.name }}</div>
                                        <div class="text-xs opacity-75">{{ theme.description }}</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Fin del selector de tema -->

                    <nav class="flex-1 space-y-2">
                        <!-- Todos los roles -->
                        <Link :href="route('dashboard')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('dashboard') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 12l9-9 9 9M4 10v10h16V10" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Dashboard
                        </Link>

                        <Link :href="route('qr.mostrar')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('mi-qr') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 12l9-9 9 9M4 10v10h16V10" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Mi QR
                        </Link>

                        <!-- Solo admin y administrativo -->
                        <Link v-if="hasRole('admin') || hasRole('administrativo')" :href="route('gestion.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('gestion.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Gestiones
                        </Link>

                        <!-- Solo admin -->
                        <Link v-if="hasRole('admin')" :href="route('usuarios.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('usuarios.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Usuarios
                        </Link>

                        <!-- Solo admin y administrativo -->
                        <Link v-if="hasRole('admin') || hasRole('administrativo')" :href="route('asistencia.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('asistencia.index') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Asistencia E
                        </Link>

                        <Link v-if="hasRole('admin') || hasRole('administrativo')" :href="route('asistencia.index2')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('asistencia.index2') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Asistencia S
                        </Link>

                        <!-- Solo admin -->
                        <Link v-if="hasRole('admin')" :href="route('roles.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('roles.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Roles
                        </Link>

                        <!-- Solo admin y administrativo -->
                        <Link v-if="hasRole('admin') || hasRole('administrativo')" :href="route('entrada.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('entrada.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Asistencia Entrada
                        </Link>

                        <Link v-if="hasRole('admin') || hasRole('administrativo')" :href="route('salida.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('salidas.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Asistencia Salida
                        </Link>

                        <!-- NUEVO: Gestión de Usuarios del Restaurante (solo administrador) -->
                        <Link v-if="hasRole('administrador')" :href="route('restaurant.users.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('restaurant.users.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        🍽️ Usuarios Restaurante
                        </Link>

                        <!-- Gestión de Marcas (administrador y almacenero) -->
                        <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.marcas.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('restaurant.marcas.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        🏷️ Marcas
                        </Link>

                        <!-- Gestión de Insumos (administrador y almacenero) -->
                        <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.insumos.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('restaurant.insumos.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        📦 Insumos
                        </Link>

                        <!-- Gestión de Movimientos (administrador y almacenero) -->
                        <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.movimientos.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('restaurant.movimientos.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        📋 Movimientos
                        </Link>

                        <!-- Gestión de Proveedores (administrador y almacenero) -->
                        <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.proveedores.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('restaurant.proveedores.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        🏭 Proveedores
                        </Link>

                        <!-- Gestión de Compras (administrador y almacenero) -->
                        <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.compras.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('restaurant.compras.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        🛒 Compras
                        </Link>

                        <!-- Gestión de Productos (administrador y almacenero) -->
                        <Link v-if="hasRole('administrador') || hasRole('almacenero')" :href="route('restaurant.productos.index')"
                            class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg transition"
                            :class="{ 'nav-link-active': route().current('restaurant.productos.*') }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        🍔 Productos
                        </Link>
                    </nav>
                    <div class="mt-auto pt-6 border-t sidebar-footer">
                        <span class="text-xs opacity-75">© 2024 Biblioteca</span>
                    </div>
                </div>
                <!-- Clic fuera del menú cierra el sidebar -->
                <div class="flex-1" @click="showingNavigationDropdown = false"></div>
            </aside>


            <!-- Main content HEADER -->
            <div class="flex-1 flex flex-col">
                <!-- Top nav -->
                <nav class="top-nav backdrop-blur border-b px-6 py-3 flex items-center justify-between shadow-sm">
                    <div class="block md:hidden">
                        <button class="text-2xl nav-toggle"
                            @click="showingNavigationDropdown = !showingNavigationDropdown">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- Buscador INPUT-->
                    <div class="flex items-center justify-between gap-4 w-full">

                        <div class = "flex justify-center items-center">
                            <span class="text-sm font-medium user-name">{{ $page.props.auth.user.name }}</span>
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center gap-2 px-3 py-1 rounded transition profile-button">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                        Perfil
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.show')">Perfil</DropdownLink>
                                    <form @submit.prevent="logout">
                                        <DropdownLink as="button">Cerrar sesión</DropdownLink>
                                    </form>
                                </template>
                            </Dropdown>

                        </div>


                        <!-- Buscador Global -->
                        <GlobalSearch />

                    </div>


                </nav>

                <!-- Page content -->
                <main class="flex-1 p-8 main-content">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
