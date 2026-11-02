<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaContable extends Model
{
    protected $table = 'cuentas_contables';

    protected $fillable = [
        'codigo',
        'nombre',
        'nivel',
        'tipo',
        'naturaleza',
        'padre_id',
        'permite_movimiento',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'nivel' => 'integer',
            'permite_movimiento' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(self::class, 'padre_id')->orderBy('codigo');
    }

    public function detallesAsiento(): HasMany
    {
        return $this->hasMany(DetalleAsiento::class, 'cuenta_contable_id');
    }

    public function detallesModeloContable(): HasMany
    {
        return $this->hasMany(DetalleModeloContable::class, 'cuenta_contable_id');
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeImputables(Builder $query): Builder
    {
        return $query->where('permite_movimiento', true)->where('activo', true);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->codigo} - {$this->nombre}";
    }
}
