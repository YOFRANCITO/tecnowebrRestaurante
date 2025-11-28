<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marca extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación: Una marca tiene muchos insumos
     */
    public function insumos()
    {
        return $this->hasMany(Insumo::class);
    }
}
