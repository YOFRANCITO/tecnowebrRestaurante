// composables/useCooldown.js
import { ref } from 'vue'

export function useCooldown(tiempoEsperaMs = 4000) {
    // Estado reactivo
    const puedeEscanear = ref(true)
    const contadorCooldown = ref(0)
    
    // Variables internas
    let intervaloCooldown = null
    let timeoutCooldown = null

    /**
     * Inicia el contador visual de cooldown
     */
    const iniciarContadorVisual = () => {
        const segundos = Math.ceil(tiempoEsperaMs / 1000)
        contadorCooldown.value = segundos

        // Limpiar intervalo anterior si existe
        if (intervaloCooldown) {
            clearInterval(intervaloCooldown)
        }

        intervaloCooldown = setInterval(() => {
            contadorCooldown.value--

            if (contadorCooldown.value <= 0) {
                clearInterval(intervaloCooldown)
                intervaloCooldown = null
            }
        }, 1000)
    }

    /**
     * Bloquea el escaneo temporalmente
     * @param {Function} onStart - Callback cuando inicia el cooldown
     * @param {Function} onEnd - Callback cuando termina el cooldown
     */
    const bloquearEscaneoTemporal = (onStart = null, onEnd = null) => {
        // Bloquear escaneo
        puedeEscanear.value = false
        
        // Iniciar contador visual
        iniciarContadorVisual()

        // Ejecutar callback de inicio si existe
        if (onStart && typeof onStart === 'function') {
            onStart(tiempoEsperaMs / 1000)
        }

        // Limpiar timeout anterior si existe
        if (timeoutCooldown) {
            clearTimeout(timeoutCooldown)
        }

        // Programar el fin del cooldown
        timeoutCooldown = setTimeout(() => {
            puedeEscanear.value = true
            contadorCooldown.value = 0
            
            // Ejecutar callback de fin si existe
            if (onEnd && typeof onEnd === 'function') {
                onEnd()
            }
        }, tiempoEsperaMs)
    }

    /**
     * Resetea el estado del cooldown
     */
    const resetearCooldown = () => {
        puedeEscanear.value = true
        contadorCooldown.value = 0
        
        // Limpiar timers
        if (intervaloCooldown) {
            clearInterval(intervaloCooldown)
            intervaloCooldown = null
        }
        
        if (timeoutCooldown) {
            clearTimeout(timeoutCooldown)
            timeoutCooldown = null
        }
    }

    /**
     * Verifica si puede ejecutar una acción
     * @returns {boolean}
     */
    const puedeEjecutar = () => {
        return puedeEscanear.value
    }

    /**
     * Obtiene el tiempo restante en segundos
     * @returns {number}
     */
    const tiempoRestante = () => {
        return contadorCooldown.value
    }

    /**
     * Limpia todos los timers (para usar en onBeforeUnmount)
     */
    const limpiarTimers = () => {
        if (intervaloCooldown) {
            clearInterval(intervaloCooldown)
            intervaloCooldown = null
        }
        
        if (timeoutCooldown) {
            clearTimeout(timeoutCooldown)
            timeoutCooldown = null
        }
    }

    return {
        // Estado reactivo
        puedeEscanear,
        contadorCooldown,
        
        // Métodos
        bloquearEscaneoTemporal,
        resetearCooldown,
        puedeEjecutar,
        tiempoRestante,
        limpiarTimers
    }
}