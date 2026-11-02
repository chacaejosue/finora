<?php

namespace App\Observers;

use App\Models\Venta;
use App\Services\ContabilidadService;

class VentaObserver
{
    public function __construct(private ContabilidadService $contabilidad) {}

    /**
     * Maneja el evento updated de Venta.
     */
    public function updated(Venta $venta): void
    {
        if ($venta->isDirty('estado')) {
            if ($venta->estado === 'CONFIRMADA') {
                $this->contabilidad->contabilizarVenta($venta);
            } elseif ($venta->estado === 'ANULADA') {
                foreach ($venta->asientos as $asiento) {
                    if ($asiento->estado !== 'ANULADO') {
                        $this->contabilidad->revertirAsiento($asiento, "Venta #{$venta->id} anulada", auth()->id());
                    }
                }
            }
        }
    }
}
