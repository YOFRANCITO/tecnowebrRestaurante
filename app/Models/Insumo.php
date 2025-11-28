<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insumo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'stock',
        'unidad_medida',
        'marca_id',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
    ];

    /**
     * Relación: Un insumo pertenece a una marca
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
