<template>

<div
  class="absolute inset-0 professional-card rounded-xl overflow-hidden card-shadow flex"
  :style="{ backfaceVisibility: 'hidden', transform: 'rotateY(0deg)' }"
>
            <div class="flex h-full">
                <!-- Sección Izquierda -->
                <div
                    class="flex flex-col items-center justify-between bg-gradient-to-b from-blue-50 to-blue-100 w-[40%] p-4">
                    <!-- Logo y Título -->
                    <div class="text-center">
                        <div class="text-blue-800 font-black text-center">
                            <div class="text-2xl tracking-tight">CARNET</div>
                            <div class="text-xs tracking-widest font-bold">UNIVERSITARIO</div>
                        </div>
                        <div class="mt-1 text-xs text-blue-600 font-semibold">UAGRM</div>
                    </div>

                    <!-- Foto del Estudiante -->
                    <div class="photo-frame flex items-center justify-center" style="width: 35mm; height: 35mm;">
                        <div
                            class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center rounded">
                            <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Código QR pequeño -->
                    <div class="w-12 h-12 bg-gray-200 border border-gray-300 rounded flex items-center justify-center">
                        <div class="w-8 h-8 bg-black opacity-20"></div>
                    </div>
                </div>

                <!-- Sección Derecha -->
                <div class="relative w-[60%] p-4 bg-white">
                    <!-- Header de Facultad -->
                    <div
                        class="bg-gradient-to-r from-red-600 to-red-700 text-white px-3 py-1 text-[0.5rem] font-bold text-center clip-path-trapezoid absolute right-0 top-0 shadow-md">
                        {{ estudianteData.FACULTAD }}
                    </div>

                    <!-- Información del Estudiante -->
                    <div class="mt-8 space-y-3 text-xs">
                        <div class="border-l-3 border-blue-600 pl-2">
                            <span class="text-red-600 font-bold block text-xs">ESTUDIANTE:</span>
                            <div class="font-bold text-blue-800 text-sm leading-tight">{{
                                estudianteData.ESTUDIANTE }}</div>
                        </div>

                        <div class="border-l-3 border-blue-600 pl-2">
                            <span class="text-red-600 font-bold block text-xs">CARRERA:</span>
                            <div class="font-semibold text-blue-800 text-xs leading-tight">{{
                                estudianteData.CARRERA }}</div>
                        </div>

                        <div class="flex space-x-4">
                            <div class="border-l-3 border-blue-600 pl-2 flex-1">
                                <span class="text-red-600 font-bold block text-xs">REGISTRO:</span>
                                <div class="font-bold text-blue-800">{{ estudianteData.REGISTRO }}</div>
                            </div>

                            <div class="border-l-3 border-blue-600 pl-2 flex-1">
                                <span class="text-red-600 font-bold block text-xs">C.I.:</span>
                                <div class="font-bold text-blue-800">{{ estudianteData['DOCUMENTO DE IDENTIDAD'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logos y Elementos Decorativos -->
                    <div class="absolute bottom-3 right-3 flex items-center space-x-2">
                        <img class="h-8 w-8 rounded-full shadow-sm"
                            src="https://scontent-lim1-1.xx.fbcdn.net/v/t39.30808-6/254492065_4876233149083206_1107629317053811200_n.jpg?nc_cat=104&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=KpJMXcNRa8QQ7kNvwGp8QzO&_nc_oc=Adnqlykj79OX8pxkiNcyv1uFZBvU995Ocybax2XXrGKr-fI4MSUFgXWu9Qmy5pOFl8&_nc_zt=23&_nc_ht=scontent-lim1-1.xx&_nc_gid=lVQAFLn9WCNtx4Y8nqgzNQ&oh=00_AfMskGLqAQgKMEMYjKXt5wszEXVnxdM_0jpVLEHo9c1lkg&oe=684BC23D"
                            alt="UAGRM" />
                        <div class="w-8 h-8 bg-gradient-to-brflex items-center justify-center shadow-sm">
                            <img src="https://www.ficct.uagrm.edu.bo:3000/uploads/faculty/Escudo_FICCT.png" alt="FICCT"
                                class="w-7 h-9" />
                        </div>
                    </div>

                    <!-- Elementos decorativos de fondo -->
                    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-5">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-600 rounded-full">
                        </div>
                        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-red-600 rounded-full">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reverso de la tarjeta -->
               <div
              class="absolute inset-0 professional-card rounded-xl overflow-hidden card-shadow flex"
              :style="{ backfaceVisibility: 'hidden', transform: 'rotateY(180deg)' }"
            >
            <!-- Sección Izquierda del Reverso -->
            <div
                class="flex flex-col items-center justify-center bg-gradient-to-b from-blue-50 to-blue-100 w-[40%] p-4">
                <!-- Código de Barras -->
                <div class="bg-black w-24 h-16 rounded mb-4 flex items-center justify-center">
                    <div class="text-white text-xs font-mono">||||||||||</div>
                </div>

                <!-- Información de Validez -->
                <div class="text-center text-xs text-blue-800">
                    <div class="font-bold">VÁLIDO HASTA</div>
                    <div class="font-bold text-sm">2025</div>
                </div>
            </div>

            <!-- Sección Derecha del Reverso -->
            <div class="relative w-[60%] p-4 bg-white">
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-7 py-1 text-[0.8rem] font-bold text-center clip-path-trapezoid absolute right-0 top-0 shadow-md">
                    INFORMACIÓN ACADÉMICA
                </div>

                <!-- Información Detallada -->
                <div class="mt-10 space-y-4 text-xs">
                    <div class="flex items-start space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="flex-1">
                            <span class="text-red-600 font-bold block">REGISTRO ACADÉMICO:</span>
                            <div class="font-bold text-blue-800">{{ estudianteData.REGISTRO }}</div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <div class="flex-1">
                            <span class="text-red-600 font-bold block">FACULTAD:</span>
                            <div class="font-semibold text-blue-800 text-[0.5rem] leading-tight">{{
                                estudianteData.FACULTAD }}</div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        <div class="flex-1">
                            <span class="text-red-600 font-bold block">PROGRAMA:</span>
                            <div class="font-semibold text-blue-800 text-xs leading-tight">{{
                                estudianteData.CARRERA }}</div>
                        </div>
                    </div>
                </div>

                <!-- Footer del reverso -->
                <div class="absolute bottom-3 left-4 right-4">
                    <div class="text-center text-xs text-gray-600 border-t pt-2">
                        <div class="font-bold">Universidad Autónoma Gabriel René Moreno</div>
                        <div>Santa Cruz - Bolivia</div>
                    </div>
                </div>

                <!-- Elementos decorativos del reverso -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-5">
                    <div class="absolute -top-10 -left-10 w-32 h-32 bg-red-600 rounded-full"></div>
                    <div class="absolute -bottom-10 -right-10 w-24 h-24 bg-blue-600 rounded-full"></div>
                </div>
            </div>
        </div>
  
    <!-- Instrucción para voltear -->
    <p class="text-center text-sm text-gray-500 mt-4 font-medium">
        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 9H17a1 1 0 110 2h-5.586l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        Haz clic para voltear el carnet
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
