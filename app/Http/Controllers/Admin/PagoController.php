<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagoRequest;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::with(['venta.cliente', 'registradoPor']);

        if ($request->filled('venta_id')) {
            $query->where('venta_id', $request->input('venta_id'));
        }

        $pagos = $query->latest('fecha')->paginate(15)->withQueryString();

        // Resumen de cuentas por cobrar por cliente
        $clientesDeudores = Usuario::clientes()->with(['ventas' => function ($q) {
            $q->where('estado', 'CONFIRMADA')->with(['detalles', 'pagos']);
        }])->get()->map(function ($cli) {
            $deuda = $cli->ventas->sum(fn ($v) => $v->saldo_pendiente);
            $cli->total_deuda = $deuda;

            return $cli;
        })->filter(fn ($cli) => $cli->total_deuda > 0);

        return view('panel.pagos.index', compact('pagos', 'clientesDeudores'));
    }

    public function create(Request $request)
    {
        $ventaSeleccionada = null;
        if ($request->filled('venta_id')) {
            $ventaSeleccionada = Venta::with(['cliente', 'detalles.producto', 'pagos'])->find($request->input('venta_id'));
        }

        $ventasConSaldo = Venta::with('cliente')
            ->where('estado', 'CONFIRMADA')
            ->get()
            ->filter(fn ($v) => $v->saldo_pendiente > 0);

        return view('panel.pagos.create', compact('ventasConSaldo', 'ventaSeleccionada'));
    }

    public function store(PagoRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request): void {
            $pago = Pago::create([
                'venta_id' => $validated['venta_id'],
                'registrado_por_usuario_id' => $request->user()->id,
                'monto' => $validated['monto'],
                'fecha' => now(),
                'metodo' => $validated['metodo'],
                'estado' => 'REGISTRADO',
                'observacion' => $validated['observacion'] ?? null,
            ]);

            $venta = Venta::find($validated['venta_id']);
            $montoRestante = (float) $pago->monto;

            if ($venta) {
                $cuotas = Cuota::where('venta_id', $venta->id)
                    ->orderBy('numero')
                    ->lockForUpdate()
                    ->get();

                foreach ($cuotas as $cuota) {
                    if ($montoRestante <= 0) {
                        break;
                    }

                    $aplicado = (float) $cuota->monto_pagado;
                    $saldoCuota = max(0, (float) $cuota->monto - $aplicado);
                    $montoAplicado = min($montoRestante, $saldoCuota);

                    if ($montoAplicado <= 0) {
                        continue;
                    }

                    $nuevoPagado = round($aplicado + $montoAplicado, 2);
                    $montoRestante -= $montoAplicado;

                    $cuota->update([
                        'monto_pagado' => $nuevoPagado,
                        'estado' => $nuevoPagado >= (float) $cuota->monto ? 'PAGADA' : 'PARCIAL',
                    ]);
                }
            }
        });

        return redirect()->route('panel.ventas.show', $validated['venta_id'])
            ->with('exito', 'Pago registrado exitosamente.');
    }
}
