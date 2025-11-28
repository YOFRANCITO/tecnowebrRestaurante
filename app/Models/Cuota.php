<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuotas';

    protected $fillable = [
        'credito_id',
        'nro',
        'fecha',
        'saldo_inicial',
        'interes',
        'capital',
        'cuota',
        'saldo_final',
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
     * Relación: Una cuota pertenece a un crédito
     */
    public function credito()
    {
        return $this->belongsTo(Credito::class);
    }
}
