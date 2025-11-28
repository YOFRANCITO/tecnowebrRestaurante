<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\entrada;
use App\Models\salida;
use App\Models\Gestion;
use Carbon\Carbon;

class RegistrarSalidasAutomaticas extends Command
{
    /**
     * Nombre y firma del comando.
     *
     * @var string
     */
    protected $signature = 'app:registrar-salidas-automaticas';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Registra automáticamente la salida de usuarios que no marcaron salida después de 15 segundos (modo prueba).';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        // Obtiene la fecha y hora actual
        $now = Carbon::now();

        // Obtiene todas las entradas activas (estado = true)
        $entradas = entrada::where('estado', true)->get();

        // Contador de salidas registradas
        $contador = 0;

        // Recorre cada entrada activa
        foreach ($entradas as $entrada) {

            // Combina la fecha y hora de la entrada para obtener un objeto Carbon
            $fechaHoraEntrada = Carbon::parse($entrada->fecha . ' ' . $entrada->hora);

            // Calcula los segundos transcurridos con signo
            $segundosPasados = $fechaHoraEntrada->diffInSeconds($now, false); // false = mantener signo negativo si no ha pasado

            // Muestra en consola cuántos segundos han pasado desde la entrada
            $this->info("Evaluando entrada ID {$entrada->id} (usuario {$entrada->user_id}) - Han pasado: {$segundosPasados} segundos");

            // Verifica si han pasado al menos 15 segundos desde la hora de entrada
            if ($segundosPasados >= 15) {

                // Verifica si ya existe una salida activa para ese usuario y gestión
                $salidaActiva = salida::where('user_id', $entrada->user_id)
                    ->where('gestion_id', $entrada->gestion_id)
                    ->where('estado', false)
                    ->first();

                //$this->info('Salida Activa: ', $salidaActiva);
                dump($salidaActiva);
                // Si no hay salida activa, se registra una salida automática
                if (!$salidaActiva) {
                    salida::create([
                        'descripcion' => 'Salida automática por tiempo excedido',
                        'fecha' => $fechaHoraEntrada->copy()->addSeconds(15)->toDateString(),  // Fecha de la salida simulada
                        'hora' => $fechaHoraEntrada->copy()->addSeconds(15)->toTimeString(),   // Hora de la salida simulada
                        'user_id' => $entrada->user_id,
                        'tipoalerta_id' => 1,  // Ajustar según tus datos
                        'gestion_id' => $entrada->gestion_id,
                        'estado' => true,
                    ]);

                    // Marca la entrada como cerrada
                    $entrada->estado = false;
                    $entrada->save();

                    $contador++;
                    $this->info("Salida automática registrada para usuario {$entrada->user_id} (entrada ID {$entrada->id})");
                } else {
                    $this->info("Ya existe una salida activa para usuario {$entrada->user_id} (entrada ID {$entrada->id})");
                }
            } else {
                $this->info("Aún no han pasado 15 segundos para usuario {$entrada->user_id} (entrada ID {$entrada->id})");
            }
        }

        // Muestra el resultado final
        $this->info("Salidas automáticas registradas: $contador");
    }
}
