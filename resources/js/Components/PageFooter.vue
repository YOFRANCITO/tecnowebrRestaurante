<template>
    <footer class="page-footer">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-sm">
                <span>© {{ currentYear }} Santo Pecatto - Sistema de Inventarios y Pagos</span>
            </div>
            
            <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Visitas a esta página: <strong>{{ visitCount }}</strong></span>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    visitCount: {
        type: Number,
        default: 0
    }
});

const currentYear = computed(() => new Date().getFullYear());
</script>

<style scoped>
.page-footer {
    margin-top: auto;
}
</style>
