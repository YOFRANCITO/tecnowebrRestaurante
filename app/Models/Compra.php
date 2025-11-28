<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use SoftDeletes;

    protected $table = 'compras';

    protected $fillable = [
        'total',
        'proveedor_id',
        'user_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /**
     * Relación: Una compra pertenece a un proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación: Una compra pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una compra tiene muchos detalles
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Scope para ordenar por fecha descendente (más recientes primero)
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
