<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeloContable extends Model
{
    protected $table = 'modelos_contables';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo_comprobante_id',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function tipoComprobante(): BelongsTo
    {
        return $this->belongsTo(TipoComprobante::class, 'tipo_comprobante_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleModeloContable::class, 'modelo_contable_id');
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
