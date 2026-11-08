<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntregarPedidoRequest;
use App\Http\Requests\PedidoRequest;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'registradoPor', 'detalles']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->input('cliente_id'));
        }

        $pedidos = $query->latest('id')->paginate(15)->withQueryString();
        $clientes = Usuario::clientes()->where('activo', true)->get();

        return view('panel.pedidos.index', compact('pedidos', 'clientes'));
    }

    public function create()
    {
        $clientes = Usuario::clientes()->where('activo', true)->get();
        $productos = Producto::where('activo', true)->get();

        return view('panel.pedidos.create', compact('clientes', 'productos'));
    }

    public function store(PedidoRequest $request)
    {
        $validated = $request->validated();

        $pedido = DB::transaction(function () use ($validated, $request) {
            $clienteId = $validated['cliente_id'] ?? null;

            if (! $clienteId && ! empty($validated['nuevo_cliente_nombre'])) {
                $nuevoCliente = Usuario::create([
                    'nombre' => mb_strtoupper(trim($validated['nuevo_cliente_nombre']), 'UTF-8'),
                    'apellido' => ! empty($validated['nuevo_cliente_apellido']) ? mb_strtoupper(trim($validated['nuevo_cliente_apellido']), 'UTF-8') : null,
                    'telefono' => $validated['nuevo_cliente_telefono'] ?? null,
                    'rol' => 'CLIENTE',
                    'password' => null,
                    'activo' => true,
                    'observaciones' => 'Registrado automáticamente desde pedido',
                ]);

                $clienteId = $nuevoCliente->id;
            }

            $pedido = Pedido::create([
                'cliente_id' => $clienteId,
                'registrado_por_usuario_id' => $request->user()->id,
                'fecha' => now(),
                'estado' => 'PENDIENTE',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($validated['detalles'] as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'cantidad_reservada' => 0,
                    'precio_acordado' => $item['precio_acordado'],
                    'estado' => 'PENDIENTE',
                ]);
            }

            return $pedido;
        });

        // Intentar reserva automática al crear
        $this->ejecutarReserva($pedido);

        return redirect()->route('panel.pedidos.show', $pedido)
            ->with('exito', 'Pedido registrado exitosamente.');
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'registradoPor', 'entregadoPor', 'detalles.producto', 'venta']);

        return view('panel.pedidos.show', compact('pedido'));
    }

    public function reservarStock(Pedido $pedido)
    {
        if (in_array($pedido->estado, ['COMPLETADO', 'CANCELADO'])) {
            return redirect()->back()->withErrors(['error' => 'No se puede modificar la reserva de un pedido completado o cancelado.']);
        }

        $resultado = $this->ejecutarReserva($pedido);

        return redirect()->route('panel.pedidos.show', $pedido)
            ->with('exito', $resultado['mensaje']);
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $nuevoEstado = $request->input('estado');
        $estadosValidos = ['PENDIENTE', 'RESERVADO', 'PENDIENTE_ABASTECIMIENTO', 'LISTO_ENTREGA', 'COMPLETADO', 'CANCELADO'];

        if (! in_array($nuevoEstado, $estadosValidos)) {
            return redirect()->back()->withErrors(['error' => 'Estado no válido.']);
        }

        DB::transaction(function () use ($pedido, $nuevoEstado) {
            if ($nuevoEstado === 'CANCELADO') {
                // Liberar reservas
                foreach ($pedido->detalles as $det) {
                    $det->update(['cantidad_reservada' => 0, 'estado' => 'CANCELADO']);
                }
            }
            $pedido->update(['estado' => $nuevoEstado]);
        });

        return redirect()->route('panel.pedidos.show', $pedido)
            ->with('exito', "Estado del pedido actualizado a {$nuevoEstado}.");
    }

    public function entregar(EntregarPedidoRequest $request, Pedido $pedido)
    {
        if ($pedido->estado !== 'LISTO_ENTREGA') {
            return redirect()->back()->withErrors(['error' => 'El pedido debe estar listo para entrega.']);
        }

        $pedido->update([
            'estado' => 'COMPLETADO',
            'fecha_entrega' => now(),
            'entregado_por_usuario_id' => $request->user()->id,
            'recibido_por' => $request->validated('recibido_por'),
            'observaciones_entrega' => $request->validated('observaciones_entrega'),
        ]);

        return redirect()->route('panel.pedidos.show', $pedido)
            ->with('exito', 'Entrega registrada correctamente.');
    }

    private function ejecutarReserva(Pedido $pedido): array
    {
        return DB::transaction(function () use ($pedido) {
            $todoReservado = true;
            $algoReservado = false;

            foreach ($pedido->detalles as $det) {
                $producto = Producto::whereKey($det->producto_id)->lockForUpdate()->firstOrFail();
                // Stock disponible considerando reservas de OTROS pedidos
                $stockFisico = (int) DB::table('movimientos_inventario')
                    ->where('producto_id', $producto->id)
                    ->sum('cantidad');

                $reservasOtrosPedidos = (int) DB::table('detalle_pedidos')
                    ->join('pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
                    ->where('detalle_pedidos.producto_id', $producto->id)
                    ->where('detalle_pedidos.pedido_id', '!=', $pedido->id)
                    ->whereIn('pedidos.estado', ['PENDIENTE', 'RESERVADO', 'PENDIENTE_ABASTECIMIENTO', 'LISTO_ENTREGA'])
                    ->sum('detalle_pedidos.cantidad_reservada');

                $disponibleParaEstePedido = max(0, $stockFisico - $reservasOtrosPedidos);

                $cantidadRequerida = $det->cantidad;
                $reservaPosible = min($cantidadRequerida, $disponibleParaEstePedido);

                $det->update([
                    'cantidad_reservada' => $reservaPosible,
                    'estado' => $reservaPosible >= $cantidadRequerida ? 'RESERVADO' : 'PENDIENTE',
                ]);

                if ($reservaPosible < $cantidadRequerida) {
                    $todoReservado = false;
                }
                if ($reservaPosible > 0) {
                    $algoReservado = true;
                }
            }

            if ($todoReservado) {
                $nuevoEstado = 'LISTO_ENTREGA';
                $mensaje = 'Todo el inventario requerido ha sido reservado exitosamente. Pedido listo para entrega.';
            } elseif ($algoReservado) {
                $nuevoEstado = 'PENDIENTE_ABASTECIMIENTO';
                $mensaje = 'Se reservó stock parcial. El pedido requiere reabastecimiento para completarse.';
            } else {
                $nuevoEstado = 'PENDIENTE_ABASTECIMIENTO';
                $mensaje = 'No hay stock disponible actualmente para reservar. El pedido requiere reabastecimiento.';
            }

            $pedido->update(['estado' => $nuevoEstado]);

            return ['mensaje' => $mensaje, 'estado' => $nuevoEstado];
        });
    }
}
