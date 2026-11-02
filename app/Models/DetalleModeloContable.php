<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleModeloContable extends Model
{
    protected $table = 'detalles_modelo_contable';

    protected $fillable = [
        'modelo_contable_id',
        'cuenta_contable_id',
        'lado',
        'porcentaje',
        'tipo_monto',
        'glosa_defecto',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'decimal:2',
        ];
    }

    public function modeloContable(): BelongsTo
    {
        return $this->belongsTo(ModeloContable::class, 'modelo_contable_id');
    }

    public function cuentaContable(): BelongsTo
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contable_id');
    }
}
