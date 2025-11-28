<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Credito extends Model
{
    use SoftDeletes;

    protected $table = 'creditos';

    protected $fillable = [
        'nro',
        'fecha',
        'saldo_inicial',
        'interes',
        'capital',
        'cuota',
        'saldo_final',
        'venta_id',
        'plan_id',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'saldo_inicial' => 'decimal:2',
        'interes' => 'decimal:2',
        'capital' => 'decimal:2',
        'cuota' => 'decimal:2',
        'saldo_final' => 'decimal:2',
    ];

    /**
     * Relación: Un crédito pertenece a una venta
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación: Un crédito pertenece a un plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relación: Un crédito pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un crédito tiene muchas cuotas
     */
    public function cuotas()
    {
        return $this->hasMany(Cuota::class);
    }

    /**
     * Relación: Un crédito tiene muchos pagos
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Genera un número de crédito único
     */
    public static function generarNumeroCredito()
    {
        $year = date('Y');
        $ultimoCredito = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $numero = $ultimoCredito ? intval(substr($ultimoCredito->nro, -5)) + 1 : 1;
        
        return 'CRE-' . $year . '-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calcula los intereses acumulados desde la última actualización
     * Fórmula: capital * (tasa_interes_diario / 100) * dias_transcurridos
     */
    public function calcularInteresesDiarios()
    {
        if ($this->capital <= 0) {
            return 0;
        }

        $ultimoPago = $this->pagos()->latest('fecha')->first();
        $fechaBase = $ultimoPago ? Carbon::parse($ultimoPago->fecha) : Carbon::parse($this->fecha);
        $diasTranscurridos = $fechaBase->diffInDays(Carbon::now());

        $tasaDiaria = $this->plan->tasa_interes_diario / 100;
        $interesesNuevos = $this->capital * $tasaDiaria * $diasTranscurridos;

        return round($interesesNuevos, 2);
    }

    /**
     * Actualiza el saldo del crédito
     */
    public function actualizarSaldo()
    {
        $this->saldo_final = $this->capital + $this->interes;
        $this->save();
    }

    /**
     * Genera las cuotas sugeridas para el crédito
     * Las cuotas son solo una guía, el cliente puede pagar cuando quiera
     */
    public function generarCuotas()
    {
        $plan = $this->plan;
        $numeroCuotas = ceil($plan->plazo_dias / 7); // Cuotas semanales
        $cuotaSugerida = $this->saldo_inicial / $numeroCuotas;

        $saldoRestante = $this->saldo_inicial;
        $fechaCuota = Carbon::parse($this->fecha);

        for ($i = 1; $i <= $numeroCuotas; $i++) {
            $fechaCuota = $fechaCuota->addDays(7); // Cada 7 días
            
            // Calcular interés para esta cuota
            $tasaDiaria = $plan->tasa_interes_diario / 100;
            $interesCuota = $saldoRestante * $tasaDiaria * 7;
            
            // Capital a amortizar
            $capitalCuota = min($cuotaSugerida, $saldoRestante);
            
            // Cuota total
            $cuotaTotal = $capitalCuota + $interesCuota;
            
            // Saldo después de esta cuota
            $saldoRestante -= $capitalCuota;

            Cuota::create([
                'credito_id' => $this->id,
                'nro' => $i,
                'fecha' => $fechaCuota,
                'saldo_inicial' => $saldoRestante + $capitalCuota,
                'interes' => round($interesCuota, 2),
                'capital' => round($capitalCuota, 2),
                'cuota' => round($cuotaTotal, 2),
                'saldo_final' => round($saldoRestante, 2),
            ]);
        }
    }

    /**
     * Scope para créditos pendientes (con saldo > 0)
     */
    public function scopePendientes($query)
    {
        return $query->where('saldo_final', '>', 0);
    }
}
