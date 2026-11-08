<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_id',
        'registrado_por_usuario_id',
        'canal_origen',
        'fecha',
        'fecha_entrega',
        'entregado_por_usuario_id',
        'recibido_por',
        'observaciones_entrega',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'fecha_entrega' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por_usuario_id');
    }

    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'entregado_por_usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function venta(): HasOne
    {
        return $this->hasOne(Venta::class, 'pedido_id');
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->detalles->sum(fn ($d) => $d->cantidad * $d->precio_acordado);
    }
}
