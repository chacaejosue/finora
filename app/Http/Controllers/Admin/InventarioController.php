<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'proveedor'])
            ->withSum('movimientosInventario as stock_fisico_calc', 'cantidad')
            ->selectSub(
                \App\Models\DetallePedido::selectRaw('COALESCE(SUM(cantidad_reservada), 0)')
                    ->whereColumn('producto_id', 'productos.id')
                    ->whereHas('pedido', function ($q) {
                        $q->whereIn('estado', ['PENDIENTE', 'RESERVADO', 'PENDIENTE_ABASTECIMIENTO', 'LISTO_ENTREGA']);
                    }),
                'cantidad_reservada_calc'
            )
            ->where('activo', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        $productos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('panel.inventario.index', compact('productos'));
    }

    public function movimientos(Request $request)
    {
        $query = MovimientoInventario::with(['producto', 'registradoPor', 'detalleCompra.compra', 'detalleVenta.venta']);

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->input('producto_id'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        $movimientos = $query->latest('fecha')->paginate(20)->withQueryString();
        $productos = Producto::orderBy('nombre')->get();

        return view('panel.inventario.movimientos', compact('movimientos', 'productos'));
    }

    public function ajuste(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'tipo' => ['required', 'in:AJUSTE_ENTRADA,AJUSTE_SALIDA'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'observacion' => ['required', 'string', 'max:255'],
        ]);

        $cantidadSigned = $validated['tipo'] === 'AJUSTE_ENTRADA' ? $validated['cantidad'] : -$validated['cantidad'];

        DB::transaction(function () use ($validated, $cantidadSigned, $request): void {
            $producto = Producto::whereKey($validated['producto_id'])->lockForUpdate()->firstOrFail();

            if ($cantidadSigned < 0 && $producto->stock_fisico < abs($cantidadSigned)) {
                throw ValidationException::withMessages([
                    'cantidad' => 'El ajuste no puede dejar el inventario en negativo.',
                ]);
            }

            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'registrado_por_usuario_id' => $request->user()->id,
                'tipo' => $validated['tipo'],
                'cantidad' => $cantidadSigned,
                'fecha' => now(),
                'observacion' => $validated['observacion'],
            ]);
        });

        return redirect()->back()->with('exito', 'Ajuste de inventario registrado correctamente.');
    }
}
