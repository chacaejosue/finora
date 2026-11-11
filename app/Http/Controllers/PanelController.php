<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Muestra el panel principal del consultor.
     */
    public function index(): View
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        // 1. Cobros pendientes del mes / activos
        $cobrosPendientesMes = (float) Cuota::whereHas('venta', function ($q) {
            $q->where('estado', 'CONFIRMADA');
        })
            ->where('estado', '!=', 'PAGADA')
            ->whereBetween('fecha_vencimiento', [$inicioMes, $finMes])
            ->selectRaw('SUM(monto - monto_pagado) as total')
            ->value('total') ?? 0.0;

        if ($cobrosPendientesMes == 0) {
            $cobrosPendientesMes = (float) Venta::where('estado', 'CONFIRMADA')
                ->where('forma_pago', 'CREDITO')
                ->with(['detalles', 'pagos'])
                ->get()
                ->sum(fn ($v) => $v->saldo_pendiente);
        }

        // 2. Ganancia Neta Estimada del Mes
        $ventasMes = Venta::where('estado', 'CONFIRMADA')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->with('detalles.producto')
            ->get();

        $gananciaEstimadaMes = 0.0;
        foreach ($ventasMes as $v) {
            foreach ($v->detalles as $det) {
                $costoUnit = (float) ($det->producto->costo_promedio > 0 ? $det->producto->costo_promedio : ($det->precio_unitario * 0.70));
                $gananciaEstimadaMes += ($det->precio_unitario - $costoUnit) * $det->cantidad;
            }
            $gananciaEstimadaMes -= (float) $v->descuento;
        }
        $gananciaEstimadaMes = max(0, $gananciaEstimadaMes);

        // 3. Clientes con Cuotas Vencidas (Morosidad)
        $cuotasVencidasCount = Cuota::whereHas('venta', function ($q) {
            $q->where('estado', 'CONFIRMADA');
        })
            ->where('estado', '!=', 'PAGADA')
            ->where('fecha_vencimiento', '<', now()->toDateString())
            ->whereRaw('monto > monto_pagado')
            ->distinct('venta_id')
            ->count();

        // 4. Productos con Stock Crítico (<= 3 unidades)
        $stockCriticoCount = Producto::where('activo', true)
            ->where(function ($q) {
                $q->whereRaw('(SELECT COALESCE(SUM(cantidad), 0) FROM movimientos_inventario WHERE movimientos_inventario.producto_id = productos.id) <= 3');
            })
            ->count();

        // 5. Pedidos pendientes de atención
        $pedidosPendientes = Pedido::whereIn('estado', [
            'PENDIENTE',
            'RESERVADO',
            'PENDIENTE_ABASTECIMIENTO',
            'LISTO_ENTREGA',
        ])->count();

        // 6. Últimas ventas recientes
        $ultimasVentas = Venta::with(['cliente', 'detalles'])
            ->latest('fecha')
            ->limit(5)
            ->get();

        return view('panel', [
            'usuario' => $usuario,
            'pedidosPendientes' => $pedidosPendientes,
            'cobrosPendientesMes' => $cobrosPendientesMes,
            'gananciaEstimadaMes' => $gananciaEstimadaMes,
            'cuotasVencidasCount' => $cuotasVencidasCount,
            'stockCriticoCount' => $stockCriticoCount,
            'ultimasVentas' => $ultimasVentas,
        ]);
    }

    /**
     * Muestra el panel operativo del colaborador.
     */
    public function operativo(): View
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('panel.colaborador', [
            'usuario' => $usuario,
            'pedidosPendientes' => Pedido::whereIn('estado', [
                'PENDIENTE',
                'RESERVADO',
                'PENDIENTE_ABASTECIMIENTO',
                'LISTO_ENTREGA',
            ])->count(),
            'ventasPendientes' => Venta::where('estado', 'BORRADOR')->count(),
            'productosActivos' => Producto::where('activo', true)->count(),
        ]);
    }

    /**
     * Muestra el dashboard privado del cliente autenticado.
     */
    public function cliente(): View
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $cliente = $usuario;
        $pedidos = $cliente->pedidos()
            ->with('detalles.producto')
            ->latest('fecha')
            ->limit(5)
            ->get();
        $ventas = $cliente->ventas()
            ->with(['detalles.producto', 'cuotas', 'pagos'])
            ->latest('fecha')
            ->limit(5)
            ->get();

        return view('cliente.index', compact('usuario', 'cliente', 'pedidos', 'ventas'));
    }
}
