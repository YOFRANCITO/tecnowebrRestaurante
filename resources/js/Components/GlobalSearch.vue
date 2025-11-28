<template>
    <div class="global-search-container" ref="searchContainer">
        <!-- Input de búsqueda -->
        <div class="search-input-wrapper">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                ref="searchInput"
                v-model="query"
                @input="handleSearch"
                @keydown="handleKeydown"
                @focus="showResults = true"
                type="text"
                placeholder="Buscar en todo el sistema... (Ctrl+K)"
                class="search-input"
            />
            <span v-if="loading" class="loading-spinner">⏳</span>
            <kbd v-else class="keyboard-shortcut">Ctrl+K</kbd>
        </div>

        <!-- Dropdown de resultados -->
        <transition name="fade">
            <div v-if="showResults && (results.length > 0 || query.length >= 2)" class="search-results">
                <!-- Resultados -->
                <div v-if="results.length > 0">
                    <div v-for="(group, groupIndex) in results" :key="group.type" class="result-group">
                        <div class="group-header">
                            <span class="group-icon">{{ group.icon }}</span>
                            <span class="group-label">{{ group.label }}</span>
                            <span class="group-count">{{ group.items.length }}</span>
                        </div>
                        <a
                            v-for="(item, itemIndex) in group.items"
                            :key="item.id"
                            :href="item.url"
                            :class="{ 'active': isSelected(groupIndex, itemIndex) }"
                            @mouseenter="setSelected(groupIndex, itemIndex)"
                            @click="handleResultClick"
                            class="result-item"
                        >
                            <div class="item-content">
                                <div class="item-title" v-html="highlightMatch(item.title)"></div>
                                <div class="item-subtitle">{{ item.subtitle }}</div>
                            </div>
                            <svg class="item-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Sin resultados -->
                <div v-else-if="query.length >= 2 && !loading" class="no-results">
                    <span class="no-results-icon">🔍</span>
                    <p class="no-results-text">No se encontraron resultados para "{{ query }}"</p>
                    <p class="no-results-hint">Intenta con otros términos de búsqueda</p>
                </div>

                <!-- Footer con atajos -->
                <div v-if="results.length > 0" class="search-footer">
                    <div class="footer-hint">
                        <kbd>↑↓</kbd> Navegar
                        <kbd>Enter</kbd> Seleccionar
                        <kbd>Esc</kbd> Cerrar
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { debounce } from 'lodash';

const query = ref('');
const results = ref([]);
const showResults = ref(false);
const loading = ref(false);
const selectedGroup = ref(0);
const selectedItem = ref(0);
const searchContainer = ref(null);
const searchInput = ref(null);

// Manejar búsqueda con debounce
const handleSearch = debounce(async () => {
    if (query.value.length < 2) {
        results.value = [];
        return;
    }

    loading.value = true;

    try {
        const response = await fetch(`/api/global-search?q=${encodeURIComponent(query.value)}`);
        
        // Log completo para debugging
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Error response:', text);
            throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
        }
        
        const data = await response.json();
        results.value = data.results || [];
        selectedGroup.value = 0;
        selectedItem.value = 0;
    } catch (error) {
        console.error('Error en búsqueda global:', error);
        console.error('Error stack:', error.stack);
        results.value = [];
    } finally {
        loading.value = false;
    }
}, 300);

// Verificar si un item está seleccionado
const isSelected = (groupIndex, itemIndex) => {
    return selectedGroup.value === groupIndex && selectedItem.value === itemIndex;
};

// Establecer item seleccionado
const setSelected = (groupIndex, itemIndex) => {
    selectedGroup.value = groupIndex;
    selectedItem.value = itemIndex;
};

// Obtener total de items
const getTotalItems = () => {
    return results.value.reduce((total, group) => total + group.items.length, 0);
};

// Navegar al siguiente item
const navigateDown = () => {
    if (results.value.length === 0) return;

    const currentGroup = results.value[selectedGroup.value];
    
    if (selectedItem.value < currentGroup.items.length - 1) {
        selectedItem.value++;
    } else if (selectedGroup.value < results.value.length - 1) {
        selectedGroup.value++;
        selectedItem.value = 0;
    }
};

// Navegar al item anterior
const navigateUp = () => {
    if (results.value.length === 0) return;

    if (selectedItem.value > 0) {
        selectedItem.value--;
    } else if (selectedGroup.value > 0) {
        selectedGroup.value--;
        selectedItem.value = results.value[selectedGroup.value].items.length - 1;
    }
};

// Navegar al resultado seleccionado
const navigateToSelected = () => {
    if (results.value.length === 0) return;

    const group = results.value[selectedGroup.value];
    const item = group?.items[selectedItem.value];
    
    if (item) {
        window.location.href = item.url;
    }
};

// Manejar teclas
const handleKeydown = (e) => {
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        navigateDown();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        navigateUp();
    } else if (e.key === 'Enter') {
        e.preventDefault();
        navigateToSelected();
    } else if (e.key === 'Escape') {
        showResults.value = false;
        searchInput.value?.blur();
    }
};

// Manejar clic en resultado
const handleResultClick = () => {
    showResults.value = false;
    query.value = '';
};

// Resaltar coincidencias
const highlightMatch = (text) => {
    if (!query.value) return text;
    
    const regex = new RegExp(`(${query.value})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
};

// Cerrar al hacer clic fuera
const handleClickOutside = (e) => {
    if (searchContainer.value && !searchContainer.value.contains(e.target)) {
        showResults.value = false;
    }
};

// Atajo de teclado global (Ctrl+K / Cmd+K)
const handleGlobalKeydown = (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.value?.focus();
        showResults.value = true;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleGlobalKeydown);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleGlobalKeydown);
});

// Limpiar resultados cuando se cierra
watch(showResults, (newVal) => {
    if (!newVal) {
        selectedGroup.value = 0;
        selectedItem.value = 0;
    }
});
</script>

<style scoped>
.global-search-container {
    position: relative;
    width: 100%;
    max-width: 600px;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 12px;
    width: 20px;
    height: 20px;
    color: var(--text-secondary);
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 10px 100px 10px 40px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-secondary);
    color: var(--text-primary);
    font-size: 14px;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.loading-spinner {
    position: absolute;
    right: 12px;
    font-size: 16px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.keyboard-shortcut {
    position: absolute;
    right: 12px;
    padding: 4px 8px;
    background: rgba(0, 0, 0, 0.05);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 11px;
    font-family: monospace;
    color: var(--text-secondary);
}

.search-results {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    max-height: 500px;
    overflow-y: auto;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    z-index: 1000;
}

.result-group {
    border-bottom: 1px solid var(--border-color);
}

.result-group:last-child {
    border-bottom: none;
}

.group-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px 8px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.group-icon {
    font-size: 16px;
}

.group-count {
    margin-left: auto;
    padding: 2px 8px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 12px;
    font-size: 11px;
}

.result-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    text-decoration: none;
    color: var(--text-primary);
    transition: background 0.15s;
    cursor: pointer;
}

.result-item:hover,
.result-item.active {
    background: rgba(102, 126, 234, 0.08);
}

.item-content {
    flex: 1;
    min-width: 0;
}

.item-title {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.item-title :deep(mark) {
    background: rgba(102, 126, 234, 0.2);
    color: #667eea;
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: 600;
}

.item-subtitle {
    font-size: 12px;
    color: var(--text-secondary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.item-arrow {
    width: 16px;
    height: 16px;
    color: var(--text-secondary);
    opacity: 0;
    transition: opacity 0.15s;
}

.result-item:hover .item-arrow,
.result-item.active .item-arrow {
    opacity: 1;
}

.no-results {
    padding: 40px 20px;
    text-align: center;
}

.no-results-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 16px;
    opacity: 0.5;
}

.no-results-text {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.no-results-hint {
    font-size: 12px;
    color: var(--text-secondary);
}

.search-footer {
    padding: 12px 16px;
    border-top: 1px solid var(--border-color);
    background: rgba(0, 0, 0, 0.02);
}

.footer-hint {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 11px;
    color: var(--text-secondary);
}

.footer-hint kbd {
    padding: 3px 6px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 3px;
    font-family: monospace;
    font-size: 10px;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s, transform 0.15s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
