<?php

namespace App\Observers;

use App\Models\MovimientoInventario;
use App\Services\ContabilidadService;

class MovimientoInventarioObserver
{
    public function __construct(private ContabilidadService $contabilidad) {}

    /**
     * Completa automáticamente los campos del Kardex Valorado antes de guardar.
     */
    public function creating(MovimientoInventario $movimiento): void
    {
        $cantidadAbs = abs((int) $movimiento->cantidad);

        if ((float) $movimiento->costo_unitario <= 0) {
            if ($movimiento->detalle_compra_id) {
                $costoUnitarioCompra = (float) $movimiento->detalleCompra?->costo_unitario;
                // En compras facturadas, el costo ingresa al 87% neto según Ley 843
                $costoNeto = round($costoUnitarioCompra * 0.87, 2);
                $movimiento->costo_unitario = $costoNeto;
                $movimiento->costo_total = round($cantidadAbs * $costoNeto, 2);
            } elseif ($movimiento->detalle_venta_id) {
                $costoCpp = (float) $movimiento->producto?->costo_promedio;
                if ($costoCpp <= 0) {
                    $costoCpp = (float) $movimiento->detalleVenta?->costo_unitario;
                }
                $movimiento->costo_unitario = $costoCpp;
                $movimiento->costo_total = round($cantidadAbs * $costoCpp, 2);
            } else {
                $costoCpp = (float) $movimiento->producto?->costo_promedio;
                $movimiento->costo_unitario = $costoCpp;
                $movimiento->costo_total = round($cantidadAbs * $costoCpp, 2);
            }
        } elseif ((float) $movimiento->costo_total <= 0) {
            $movimiento->costo_total = round($cantidadAbs * (float) $movimiento->costo_unitario, 2);
        }
    }

    /**
     * Contabiliza ajustes directos de inventario (mermas / sobrantes).
     */
    public function created(MovimientoInventario $movimiento): void
    {
        if (in_array($movimiento->tipo, ['AJUSTE_ENTRADA', 'AJUSTE_SALIDA'], true)) {
            $this->contabilidad->contabilizarAjusteInventario($movimiento);
        }
    }
}
