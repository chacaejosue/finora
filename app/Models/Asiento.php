<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Asiento extends Model
{
    protected $table = 'asientos';

    public const TIPOS_COMPROBANTE = [
        'ING' => [
            'id' => 'ING',
            'codigo' => 'ING',
            'nombre' => 'Comprobante de Ingreso',
            'descripcion' => 'Registra las entradas reales y efectivas de fondos a Caja o Bancos.',
        ],
        'EGR' => [
            'id' => 'EGR',
            'codigo' => 'EGR',
            'nombre' => 'Comprobante de Egreso',
            'descripcion' => 'Registra las salidas reales de fondos por desembolsos o pagos mediante caja o banco.',
        ],
        'TRS' => [
            'id' => 'TRS',
            'codigo' => 'TRS',
            'nombre' => 'Comprobante de Traspaso',
            'descripcion' => 'Registra transacciones devengadas que no mueven efectivo de inmediato.',
        ],
        'APE' => [
            'id' => 'APE',
            'codigo' => 'APE',
            'nombre' => 'Comprobante de Apertura',
            'descripcion' => 'Registra el balance inicial de apertura o reapertura de gestión contable.',
        ],
    ];

    protected $fillable = [
        'tipo_comprobante',
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

    public static function catalogoTiposComprobante(): Collection
    {
        return collect(self::TIPOS_COMPROBANTE)->map(fn (array $item) => (object) $item)->values();
    }

    public static function obtenerTipoComprobante(string $codigo): object
    {
        $cod = strtoupper(trim($codigo));
        $info = self::TIPOS_COMPROBANTE[$cod] ?? [
            'id' => $cod,
            'codigo' => $cod,
            'nombre' => 'Comprobante',
            'descripcion' => '',
        ];

        return new class($info['id'], $info['codigo'], $info['nombre'], $info['descripcion'])
        {
            public function __construct(
                public string $id,
                public string $codigo,
                public string $nombre,
                public string $descripcion,
            ) {}

            public function __toString(): string
            {
                return $this->codigo;
            }
        };
    }

    public function getTipoComprobanteAttribute(?string $value): object
    {
        return self::obtenerTipoComprobante($value ?: 'TRS');
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
