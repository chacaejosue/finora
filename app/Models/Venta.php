<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'pedido_id',
        'registrado_por_usuario_id',
        'fecha',
        'forma_pago',
        'metodo_pago',
        'numero_cuotas',
        'estado',
        'descuento',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'descuento' => 'decimal:2',
            'numero_cuotas' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por_usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'venta_id');
    }

    public function credito(): HasOne
    {
        return $this->hasOne(Credito::class, 'venta_id');
    }

    public function asientos(): MorphMany
    {
        return $this->morphMany(Asiento::class, 'contabilizable');
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->detalles->sum(fn ($d) => $d->cantidad * $d->precio_unitario);
    }

    public function getTotalAttribute(): float
    {
        $totalCalculado = $this->subtotal - (float) $this->descuento;

        return max(0, $totalCalculado);
    }

    public function getTotalPagadoAttribute(): float
    {
        return (float) $this->pagos()->where('estado', 'REGISTRADO')->sum('monto');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max(0, $this->total - $this->total_pagado);
    }
}
