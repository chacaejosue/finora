<?php

namespace App\Observers;

use App\Models\Compra;
use App\Services\ContabilidadService;

class CompraObserver
{
    public function __construct(private ContabilidadService $contabilidad) {}

    /**
     * Maneja el evento updated de Compra.
     */
    public function updated(Compra $compra): void
    {
        if ($compra->isDirty('estado')) {
            if ($compra->estado === 'RECIBIDA') {
                $this->contabilidad->procesarRecepcionCompra($compra);
            } elseif (in_array($compra->estado, ['CANCELADA', 'ANULADA'], true)) {
                foreach ($compra->asientos as $asiento) {
                    if ($asiento->estado !== 'ANULADO') {
                        $this->contabilidad->revertirAsiento($asiento, "Compra #{$compra->id} cancelada/anulada", auth()->id());
                    }
                }
            }
        }
    }
}
