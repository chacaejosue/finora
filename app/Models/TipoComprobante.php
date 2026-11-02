<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoComprobante extends Model
{
    protected $table = 'tipos_comprobante';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function asientos(): HasMany
    {
        return $this->hasMany(Asiento::class, 'tipo_comprobante_id');
    }

    public function modelosContables(): HasMany
    {
        return $this->hasMany(ModeloContable::class, 'tipo_comprobante_id');
    }
}
