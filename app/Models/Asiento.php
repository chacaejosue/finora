<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Asiento extends Model
{
    protected $table = 'asientos';

    protected $fillable = [
        'tipo_comprobante_id',
        'numero',
        'fecha',
        'glosa',
        'estado',
        'contabilizable_type',
        'contabilizable_id',
        'total_debe',
        'total_haber',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'total_debe' => 'decimal:2',
            'total_haber' => 'decimal:2',
        ];
    }

    public function tipoComprobante(): BelongsTo
    {
        return $this->belongsTo(TipoComprobante::class, 'tipo_comprobante_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleAsiento::class, 'asiento_id');
    }

    public function contabilizable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeAprobados(Builder $query): Builder
    {
        return $query->where('estado', 'APROBADO');
    }

    public function scopePorPeriodo(Builder $query, string $desde, string $hasta): Builder
    {
        return $query->whereBetween('fecha', [$desde, $hasta]);
    }

    /**
     * Valida si el asiento cumple con el principio de partida doble.
     */
    public function getEstaCuadradoAttribute(): bool
    {
        return abs((float) $this->total_debe - (float) $this->total_haber) < 0.01;
    }
}
