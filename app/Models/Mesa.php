<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use SoftDeletes;

    protected $table = 'mesas';

    protected $fillable = [
        'codigo',
        'capacidad',
    ];

    /**
     * Relación: Una mesa tiene muchas ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
