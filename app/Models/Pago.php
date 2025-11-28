<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'credito_id',
        'monto',
        'fecha',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    /**
     * Relación: Un pago pertenece a un crédito
     */
    public function credito()
    {
        return $this->belongsTo(Credito::class);
    }
}
