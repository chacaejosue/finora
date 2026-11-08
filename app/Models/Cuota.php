<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cuota extends Model
{
    protected $table = 'cuotas';

    protected $fillable = [
        'venta_id',
        'numero',
        'monto',
        'monto_pagado',
        'fecha_vencimiento',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'monto' => 'decimal:2',
            'monto_pagado' => 'decimal:2',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function getMontoAplicadoAttribute(): float
    {
        return (float) $this->monto_pagado;
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max(0, (float) $this->monto - (float) $this->monto_pagado);
    }
}
