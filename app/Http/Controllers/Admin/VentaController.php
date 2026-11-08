<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaRequest;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'pedido', 'registradoPor']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('forma_pago')) {
            $query->where('forma_pago', $request->input('forma_pago'));
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->input('cliente_id'));
        }

        $ventas = $query->latest('id')->paginate(15)->withQueryString();
        $clientes = Usuario::clientes()->where('activo', true)->get();

        return view('panel.ventas.index', compact('ventas', 'clientes'));
    }

    public function create(Request $request)
    {
        $pedido = null;
        if ($request->filled('pedido_id')) {
            $pedido = Pedido::with(['cliente', 'detalles.producto'])->find($request->input('pedido_id'));
        }

        $clientes = Usuario::clientes()->where('activo', true)->get();
        $productos = Producto::where('activo', true)->get();

        return view('panel.ventas.create', compact('clientes', 'productos', 'pedido'));
    }

    public function store(VentaRequest $request)
    {
        $validated = $request->validated();

        $venta = DB::transaction(function () use ($validated, $request) {
            $venta = Venta::create([
                'cliente_id' => $validated['cliente_id'],
                'pedido_id' => $validated['pedido_id'] ?? null,
                'registrado_por_usuario_id' => $request->user()->id,
                'fecha' => now(),
                'forma_pago' => $validated['forma_pago'],
                'metodo_pago' => $validated['metodo_pago'] ?? null,
                'numero_cuotas' => $validated['numero_cuotas'] ?? null,
                'estado' => 'BORRADOR',
                'descuento' => $validated['descuento'] ?? 0,
            ]);

            foreach ($validated['detalles'] as $item) {
                $prod = Producto::find($item['producto_id']);
                // Se registra el precio de venta y costo de compra más reciente si existe
                $ultimoCosto = DB::table('detalle_compras')
                    ->where('producto_id', $item['producto_id'])
                    ->latest('id')
                    ->value('costo_unitario') ?? 0;

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'costo_unitario' => $ultimoCosto,
                ]);
            }

            return $venta;
        });

        return redirect()->route('panel.ventas.show', $venta)
            ->with('exito', 'Venta registrada en borrador correctamente.');
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'registradoPor', 'detalles.producto', 'pagos', 'cuotas']);

        return view('panel.ventas.show', compact('venta'));
    }

    public function confirmar(Request $request, Venta $venta)
    {
        if ($venta->estado === 'CONFIRMADA') {
            return redirect()->back()->withErrors(['error' => 'Esta venta ya fue confirmada anteriormente.']);
        }

        if (in_array($venta->estado, ['CANCELADA', 'ANULADA'])) {
            return redirect()->back()->withErrors(['error' => 'Una venta cancelada o anulada no puede ser confirmada.']);
        }

        DB::transaction(function () use ($venta, $request): void {
            $venta->load(['detalles', 'pedido']);
            $productoIds = $venta->detalles->pluck('producto_id')->unique()->values();
            $productos = Producto::whereKey($productoIds)->lockForUpdate()->get()->keyBy('id');
            $pedidoId = $venta->pedido_id;

            foreach ($venta->detalles as $detalle) {
                $producto = $productos->get($detalle->producto_id);
                $stockFisico = (int) MovimientoInventario::where('producto_id', $detalle->producto_id)->sum('cantidad');
                $reservasQuery = DB::table('detalle_pedidos')
                    ->join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
                    ->where('detalle_pedidos.producto_id', $detalle->producto_id)
                    ->whereIn('pedidos.estado', ['PENDIENTE', 'RESERVADO', 'PENDIENTE_ABASTECIMIENTO', 'LISTO_ENTREGA']);

                if ($pedidoId) {
                    $reservasQuery->where('detalle_pedidos.pedido_id', '!=', $pedidoId);
                }

                $reservasOtrosPedidos = (int) $reservasQuery->sum('detalle_pedidos.cantidad_reservada');
                $stockDisponible = $stockFisico - $reservasOtrosPedidos;

                if (! $producto || $stockDisponible < $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'stock' => "No hay stock suficiente para confirmar la venta del producto #{$detalle->producto_id}.",
                    ]);
                }
            }

            $venta->update(['estado' => 'CONFIRMADA']);

            // Generar salidas de inventario evitando duplicados
            foreach ($venta->detalles as $det) {
                $yaExisteMovimiento = MovimientoInventario::where('detalle_venta_id', $det->id)->exists();
                if (! $yaExisteMovimiento) {
                    MovimientoInventario::create([
                        'producto_id' => $det->producto_id,
                        'registrado_por_usuario_id' => $request->user()->id,
                        'detalle_venta_id' => $det->id,
                        'tipo' => 'SALIDA_VENTA',
                        'cantidad' => -1 * abs($det->cantidad),
                        'fecha' => now(),
                        'observacion' => "Salida por confirmación de venta #{$venta->id}",
                    ]);
                }
            }

            // Si proviene de un pedido, liberar las reservas y dejarlo listo para entrega.
            if ($venta->pedido) {
                foreach ($venta->pedido->detalles as $detPed) {
                    $detPed->update(['cantidad_reservada' => 0, 'estado' => 'RESERVADO']);
                }
                $venta->pedido->update(['estado' => 'LISTO_ENTREGA']);
            }

            // Si es a crédito, generar directamente las cuotas vinculadas a la venta.
            if ($venta->forma_pago === 'CREDITO') {
                if ($venta->cuotas()->doesntExist()) {
                    $numeroCuotas = max(1, (int) ($venta->numero_cuotas ?: 1));
                    $montoFinanciado = (float) $venta->total;
                    $montoBase = round($montoFinanciado / $numeroCuotas, 2);
                    $totalCuotas = 0;

                    for ($numero = 1; $numero <= $numeroCuotas; $numero++) {
                        $montoCuota = $numero === $numeroCuotas
                            ? round($montoFinanciado - $totalCuotas, 2)
                            : $montoBase;
                        $totalCuotas += $montoCuota;

                        $venta->cuotas()->create([
                            'numero' => $numero,
                            'monto' => $montoCuota,
                            'monto_pagado' => 0,
                            'fecha_vencimiento' => now()->addDays(30 * $numero),
                            'estado' => 'PENDIENTE',
                        ]);
                    }
                }
            } else {
                Pago::create([
                    'venta_id' => $venta->id,
                    'registrado_por_usuario_id' => $request->user()->id,
                    'monto' => $venta->total,
                    'fecha' => now(),
                    'metodo' => $venta->metodo_pago ?: 'EFECTIVO',
                    'estado' => 'REGISTRADO',
                    'observacion' => 'Pago automático por venta de contado confirmada.',
                ]);
            }
        });

        return redirect()->route('panel.ventas.show', $venta)
            ->with('exito', 'Venta confirmada exitosamente. Salidas de inventario registradas.');
    }

    public function anular(Venta $venta)
    {
        if ($venta->estado === 'ANULADA') {
            return redirect()->back()->withErrors(['error' => 'La venta ya se encuentra anulada.']);
        }

        DB::transaction(function () use ($venta) {
            $eraConfirmada = ($venta->estado === 'CONFIRMADA');
            $venta->update(['estado' => 'ANULADA']);

            if ($eraConfirmada) {
                // Registrar reverso en movimientos de inventario
                foreach ($venta->detalles as $det) {
                    MovimientoInventario::create([
                        'producto_id' => $det->producto_id,
                        'registrado_por_usuario_id' => auth()->id(),
                        'detalle_venta_id' => $det->id,
                        'tipo' => 'REVERSO',
                        'cantidad' => abs($det->cantidad),
                        'fecha' => now(),
                        'observacion' => "Reverso por anulación de venta #{$venta->id}",
                    ]);
                }
            }
        });

        return redirect()->route('panel.ventas.show', $venta)
            ->with('exito', 'Venta anulada correctamente.');
    }
}
