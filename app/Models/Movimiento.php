<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimiento extends Model
{
    use SoftDeletes;

    protected $table = 'movimientos';

    protected $fillable = [
        'tipo',
        'cantidad',
        'motivo',
        'insumo_id',
        'stock_anterior',
        'stock_nuevo',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_nuevo' => 'decimal:2',
    ];

    /**
     * Relación: Un movimiento pertenece a un insumo
     */
    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    /**
     * Scope para ordenar por fecha descendente (más recientes primero)
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
