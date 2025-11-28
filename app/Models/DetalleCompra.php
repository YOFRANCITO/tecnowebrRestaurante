<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compra';

    protected $fillable = [
        'compra_id',
        'insumo_id',
        'costo_unitario',
        'cantidad',
        'movimiento_id',
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'cantidad' => 'decimal:2',
    ];

    /**
     * Relación: Un detalle pertenece a una compra
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Relación: Un detalle pertenece a un insumo
     */
    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    /**
     * Relación: Un detalle puede tener un movimiento asociado
     */
    public function movimiento()
    {
        return $this->belongsTo(Movimiento::class);
    }
}
