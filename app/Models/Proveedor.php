<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'detalles',
        'correo',
        'celular',
    ];

    /**
     * Relación: Un proveedor tiene muchas compras
     */
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
