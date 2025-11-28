<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoFacil extends Model
{
    protected $table = 'pagos_facil';

    protected $fillable = [
        'pagofacil_transaction_id',
        'email',
        'monto',
        'moneda',
        'estado',
        'descripcion',
        'usuario_id',
        'credito_id',
    ];
}
