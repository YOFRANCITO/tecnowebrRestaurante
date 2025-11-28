<template>


    <!-- Cara Frontal -->
    <div class="absolute inset-0 rounded-xl overflow-hidden card-shadow flex flex-col items-center justify-center p-6 bg-white"
         style="backface-visibility: hidden;">
      <h3 class="text-xl font-bold mb-2 text-blue-900">Usuario Externo</h3>
      <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 10a4 4 0 100-8 4 4 0 000 8zM2 18a8 8 0 1116 0H2z" />
      </svg>
      <div class="mt-4 space-y-1 text-sm text-gray-700 text-left w-full">
        <p><span class="font-semibold">Nombre:</span> {{ estudianteData?.name || '—' }}</p>
        <p><span class="font-semibold">Email:</span> {{ estudianteData?.email || '—' }}</p>
        <p><span class="font-semibold">Celular:</span> {{ estudianteData?.celular || '—' }}</p>
      </div>
      <p class="text-xs text-gray-500 mt-4">Haz clic para ver más información</p>
    </div>

    <!-- Cara Trasera -->
    <div class="absolute inset-0 rounded-xl overflow-hidden card-shadow flex flex-col justify-between p-6 bg-gradient-to-br from-blue-50 to-blue-100"
         style="backface-visibility: hidden; transform: rotateY(180deg);">
      <div>
        <h3 class="text-lg font-bold text-red-600 text-center">Acceso Temporal</h3>
        <p class="text-sm mt-2 text-blue-800 text-center">
          Este carnet digital proporciona acceso limitado al sistema universitario como invitado o usuario externo.
        </p>
      </div>
      <div class="text-xs text-gray-700 text-center mt-6">
        <p>Universidad Autónoma Gabriel René Moreno</p>
        <p>Santa Cruz - Bolivia</p>
      </div>
      <div class="absolute inset-0 pointer-events-none opacity-5">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-400 rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-red-400 rounded-full"></div>
      </div>
    </div>


  <p class="text-center text-sm text-gray-500 mt-4 font-medium">
    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd"
            d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 9H17a1 1 0 110 2h-5.586l4.293 4.293a1 1 0 010 1.414z"
            clip-rule="evenodd" />
    </svg>
    Haz clic para voltear
  </p>
</template>

<script setup>
defineProps({
  estudianteData: Object,
  flipped: Boolean
})

const emit = defineEmits(['toggleFlip'])

function toggleFlip() {
  emit('toggleFlip')
}
</script>


