// resources/js/composables/useSound.js
import Swal from 'sweetalert2'

export const useSound = () => {
    // Variable para controlar el tiempo de espera
    let ultimoMarcado = 0
    const TIEMPO_ESPERA = 4000 // 2 segundos en milisegundos

    // Función para verificar si puede marcar asistencia
    const puedeMarcarAsistencia = () => {
        const ahora = Date.now()
        const tiempoTranscurrido = ahora - ultimoMarcado
        
        if (tiempoTranscurrido < TIEMPO_ESPERA) {
            const tiempoRestante = Math.ceil((TIEMPO_ESPERA - tiempoTranscurrido) / 1000)
            Swal.fire({
                title: 'Espera un momento',
                text: `Debes esperar ${tiempoRestante} segundos antes de marcar otra vez`,
                icon: 'warning',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            })
            return false
        }
        
        ultimoMarcado = ahora
        return true
    }
    // Función para reproducir sonido de entrada
    const reproducirSonidoEntrada = () => {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)()
        const oscillator = audioContext.createOscillator()
        const gainNode = audioContext.createGain()
        
        oscillator.connect(gainNode)
        gainNode.connect(audioContext.destination)
        
        oscillator.frequency.value = 1000 // Frecuencia alta para entrada
        oscillator.type = 'sine'
        
        // Sonido más fuerte y con doble beep
        gainNode.gain.setValueAtTime(0.8, audioContext.currentTime)
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2)
        
        oscillator.start(audioContext.currentTime)
        oscillator.stop(audioContext.currentTime + 0.2)
        
        // Segundo beep para entrada
        setTimeout(() => {
            const oscillator2 = audioContext.createOscillator()
            const gainNode2 = audioContext.createGain()
            
            oscillator2.connect(gainNode2)
            gainNode2.connect(audioContext.destination)
            
            oscillator2.frequency.value = 1200
            oscillator2.type = 'sine'
            
            gainNode2.gain.setValueAtTime(0.8, audioContext.currentTime)
            gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2)
            
            oscillator2.start(audioContext.currentTime)
            oscillator2.stop(audioContext.currentTime + 0.2)
        }, 150)
    }

    // Función para reproducir sonido de salida
    const reproducirSonidoSalida = () => {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)()
        const oscillator = audioContext.createOscillator()
        const gainNode = audioContext.createGain()
        
        oscillator.connect(gainNode)
        gainNode.connect(audioContext.destination)
        
        oscillator.frequency.value = 800 // Frecuencia más baja para salida
        oscillator.type = 'sine'
        
        // Sonido más largo y descendente para salida
        gainNode.gain.setValueAtTime(0.8, audioContext.currentTime)
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.8)
        
        // Hacer que la frecuencia descienda gradualmente
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime)
        oscillator.frequency.exponentialRampToValueAtTime(400, audioContext.currentTime + 0.8)
        
        oscillator.start(audioContext.currentTime)
        oscillator.stop(audioContext.currentTime + 0.8)
    }

    // Función para mostrar SweetAlert de entrada exitosa
    const mostrarAlertaEntradaExitosa = () => {
        // Verificar si puede marcar antes de proceder
        if (!puedeMarcarAsistencia()) {
            return false
        }

        Swal.fire({
            title: '¡Entrada Marcada!',
            text: 'Entrada marcada con éxito',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#10B981',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'animate__animated animate__fadeInDown'
            }
        }).then(() => {
            reproducirSonidoEntrada()
        })
        
        return true
    }

    // Función para mostrar SweetAlert de salida exitosa
    const mostrarAlertaSalidaExitosa = () => {
        // Verificar si puede marcar antes de proceder
        if (!puedeMarcarAsistencia()) {
            return false
        }

        Swal.fire({
            title: '¡Salida Marcada!',
            text: 'Salida marcada con éxito',
            icon: 'info',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3B82F6',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'animate__animated animate__fadeInDown'
            }
        }).then(() => {
            reproducirSonidoSalida()
        })
        
        return true
    }

    return {
        reproducirSonidoEntrada,
        reproducirSonidoSalida,
        mostrarAlertaEntradaExitosa,
        mostrarAlertaSalidaExitosa,
        puedeMarcarAsistencia
    }
}