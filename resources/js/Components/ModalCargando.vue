<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50 backdrop-blur-sm">
    <div class="p-8 rounded-2xl shadow-2xl max-w-sm w-full mx-4"
         style="background-color: var(--bg-panel); color: var(--text-color)">
      <div class="text-center">
        <div class="w-16 h-16 mx-auto mb-4">
          <svg class="w-16 h-16 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Procesando datos</h3>
        <p class="text-gray-600">Extrayendo información del estudiante...</p>
      </div>
    </div>
  </div>
</template>
