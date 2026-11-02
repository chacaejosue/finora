<?php

namespace App\Observers;

use App\Models\Pago;
use App\Services\ContabilidadService;

class PagoObserver
{
    public function __construct(private ContabilidadService $contabilidad) {}

    /**
     * Maneja el evento created de Pago.
     */
    public function created(Pago $pago): void
    {
        if ($pago->estado === 'REGISTRADO') {
            $this->contabilidad->contabilizarPago($pago);
        }
    }

    /**
     * Maneja el evento updated de Pago.
     */
    public function updated(Pago $pago): void
    {
        if ($pago->isDirty('estado') && $pago->estado === 'ANULADO') {
            foreach ($pago->asientos as $asiento) {
                if ($asiento->estado !== 'ANULADO') {
                    $this->contabilidad->revertirAsiento($asiento, "Pago #{$pago->id} anulado", auth()->id());
                }
            }
        }
    }
}
