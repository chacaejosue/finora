<?php

namespace App\Http\Controllers;

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

        return view('panel', [
            'usuario' => $usuario,
            'pedidosPendientes' => Pedido::whereIn('estado', [
                'PENDIENTE',
                'RESERVADO',
                'PENDIENTE_ABASTECIMIENTO',
                'LISTO_ENTREGA',
            ])->count(),
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
