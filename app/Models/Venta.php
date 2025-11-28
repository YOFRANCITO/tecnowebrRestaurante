<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'total',
        'fecha_hora',
        'estado',
        'tipo_pago',
        'user_id',
        'mesa_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha_hora' => 'datetime',
    ];

    /**
     * Relación: Una venta pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una venta pertenece a una mesa
     */
    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    /**
     * Relación: Una venta tiene muchos detalles
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Relación: Una venta puede tener un crédito
     */
    public function credito()
    {
        return $this->hasOne(Credito::class);
    }

    /**
     * Scope para ordenar por fecha descendente (más recientes primero)
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('fecha_hora', 'desc');
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
}
