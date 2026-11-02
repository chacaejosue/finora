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
        'linea_id',
        'categoria_id',
        'codigo',
        'nombre',
        'descripcion',
        'precio_venta_actual',
        'costo_promedio',
        'imagen_principal',
        'publicado',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio_venta_actual' => 'decimal:2',
            'costo_promedio' => 'decimal:2',
            'publicado' => 'boolean',
            'activo' => 'boolean',
        ];
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
     * Línea comercial a la que pertenece el producto (opcional).
     */
    public function linea(): BelongsTo
    {
        return $this->belongsTo(Linea::class, 'linea_id');
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
