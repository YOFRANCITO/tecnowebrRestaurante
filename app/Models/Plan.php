<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $table = 'planes';

    protected $fillable = [
        'nombre',
        'tasa_interes_diario',
        'plazo_dias',
    ];

    protected $casts = [
        'tasa_interes_diario' => 'decimal:2',
    ];

    /**
     * Relación: Un plan tiene muchos créditos
     */
    public function creditos()
    {
        return $this->hasMany(Credito::class);
    }
}
