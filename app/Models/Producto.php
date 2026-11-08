<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'proveedor_id',
        'marca_linea',
        'categoria_id',
        'codigo',
        'nombre',
        'descripcion',
        'precio_venta_actual',
        'precio_oferta',
        'en_oferta',
        'likes',
        'costo_promedio',
        'imagen_principal',
        'publicado',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio_venta_actual' => 'decimal:2',
            'precio_oferta' => 'decimal:2',
            'en_oferta' => 'boolean',
            'likes' => 'integer',
            'costo_promedio' => 'decimal:2',
            'publicado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Determina si el producto está en oferta/descuento.
     */
    public function getEsOfertaAttribute(): bool
    {
        return (bool) ($this->en_oferta || ($this->precio_oferta !== null && $this->precio_oferta < $this->precio_venta_actual));
    }

    /**
     * Devuelve el precio final que paga el cliente (con descuento si aplica).
     */
    public function getPrecioFinalAttribute(): float
    {
        return (float) ($this->es_oferta && $this->precio_oferta !== null ? $this->precio_oferta : $this->precio_venta_actual);
    }

    /**
     * Calcula el porcentaje de descuento si está en oferta (ej. 20 para 20%).
     */
    public function getDescuentoPorcentajeAttribute(): ?int
    {
        if (! $this->es_oferta || ! $this->precio_oferta || $this->precio_venta_actual <= 0) {
            return null;
        }

        $descuento = round((1 - ($this->precio_oferta / $this->precio_venta_actual)) * 100);

        return $descuento > 0 ? (int) $descuento : null;
    }

    /**
     * Scope para consultar solo productos en oferta.
     */
    public function scopeEnOferta($query)
    {
        return $query->where(function ($q) {
            $q->where('en_oferta', true)
                ->orWhere(function ($sub) {
                    $sub->whereNotNull('precio_oferta')
                        ->whereColumn('precio_oferta', '<', 'precio_venta_actual');
                });
        });
    }

    /**
     * Obtiene la URL pública de la imagen principal del producto.
     *
     * Reconoce tres formatos almacenados en `imagen_principal`:
     *  1. URL externa (http/https)         → se devuelve tal cual
     *  2. Ruta demo   (demo/productos/…)   → asset('images/demo/productos/…')
     *  3. Ruta storage (productos/…)       → asset('storage/productos/…')
     */
    public function getImagenUrlAttribute(): ?string
    {
        if (empty($this->imagen_principal)) {
            return null;
        }

        // URLs externas
        if (str_starts_with($this->imagen_principal, 'http://') || str_starts_with($this->imagen_principal, 'https://')) {
            return $this->imagen_principal;
        }

        // Imágenes demostrativas versionadas en public/images/demo/
        if (str_starts_with($this->imagen_principal, 'demo/')) {
            return asset('images/' . ltrim($this->imagen_principal, '/'));
        }

        // Imágenes subidas por el usuario en storage/app/public/
        return asset('storage/' . ltrim($this->imagen_principal, '/'));
    }

    /**
     * Proveedor que suministra el producto.
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /**
     * Compatibilidad para vistas que lean $producto->linea?->nombre a partir de marca_linea.
     */
    public function getLineaAttribute(): ?object
    {
        return $this->marca_linea ? (object) ['nombre' => $this->marca_linea] : null;
    }

    /**
     * Categoría a la que pertenece el producto.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    public function detallesPedido()
    {
        return $this->hasMany(DetallePedido::class, 'producto_id');
    }

    public function getStockFisicoAttribute(): int
    {
        return (int) $this->movimientosInventario()->sum('cantidad');
    }

    public function getCantidadReservadaAttribute(): int
    {
        return (int) DetallePedido::where('producto_id', $this->id)
            ->whereHas('pedido', function ($q) {
                $q->whereIn('estado', ['PENDIENTE', 'RESERVADO', 'PENDIENTE_ABASTECIMIENTO', 'LISTO_ENTREGA']);
            })
            ->sum('cantidad_reservada');
    }

    public function getStockDisponibleAttribute(): int
    {
        return max(0, $this->stock_fisico - $this->cantidad_reservada);
    }
}
