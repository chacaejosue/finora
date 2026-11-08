<?php

namespace App\Http\Controllers;

use App\Http\Requests\PedidoPublicoRequest;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoPublicoController extends Controller
{
    public function create(): View
    {
        $productos = Producto::with(['categoria', 'proveedor'])
            ->where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', fn ($query) => $query->where('activo', true))
            ->orderBy('nombre')
            ->get();

        return view('pedidos.publico.create', compact('productos'));
    }

    public function store(PedidoPublicoRequest $request): View|RedirectResponse
    {
        $validated = $request->validated();
        $items = collect($validated['carrito'])->keyBy('producto_id');
        $productos = Producto::with('categoria')
            ->whereIn('id', $items->keys())
            ->where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', fn ($query) => $query->where('activo', true))
            ->get()
            ->keyBy('id');

        if ($productos->count() !== $items->count()) {
            return back()->withInput()->withErrors([
                'carrito' => 'Uno o más productos ya no están disponibles en el catálogo.',
            ]);
        }

        $pedido = DB::transaction(function () use ($validated, $productos, $items, $request): Pedido {
            $cliente = $this->resolverCliente($validated, $request);

            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'registrado_por_usuario_id' => null,
                'fecha' => now(),
                'estado' => 'PENDIENTE',
                'observaciones' => trim(sprintf(
                    'Solicitud desde catálogo web. Preferencia de pago: %s.%s',
                    $validated['preferencia_pago'],
                    filled($validated['observaciones'] ?? null) ? ' '.$validated['observaciones'] : '',
                )),
            ]);

            foreach ($items as $productoId => $item) {
                $producto = $productos->get((int) $productoId);

                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'cantidad_reservada' => 0,
                    'precio_acordado' => $producto->precio_venta_actual,
                    'estado' => 'PENDIENTE',
                ]);
            }

            return $pedido;
        });

        $pedido->load(['cliente', 'detalles.producto']);

        return view('pedidos.publico.confirmacion', [
            'pedido' => $pedido,
            'whatsappUrl' => $this->crearEnlaceWhatsApp($pedido, $validated['preferencia_pago']),
        ]);
    }

    private function resolverCliente(array $validated, PedidoPublicoRequest $request): Usuario
    {
        $usuarioAutenticado = $request->user();

        if ($usuarioAutenticado && $usuarioAutenticado->rol === 'CLIENTE') {
            return $usuarioAutenticado;
        }

        $cliente = null;

        if (filled($validated['email'] ?? null)) {
            $cliente = Usuario::clientes()
                ->where('email', $validated['email'])
                ->first();
        }

        if (! $cliente && filled($validated['telefono'] ?? null)) {
            $cliente = Usuario::clientes()
                ->where('telefono', $validated['telefono'])
                ->first();
        }

        if (! $cliente) {
            $email = $validated['email'] ?? null;

            if ($email && Usuario::where('email', $email)->exists()) {
                $email = null;
            }

            $cliente = Usuario::create([
                'nombre' => mb_strtoupper(trim($validated['nombre']), 'UTF-8'),
                'apellido' => filled($validated['apellido'] ?? null) ? mb_strtoupper(trim($validated['apellido']), 'UTF-8') : null,
                'telefono' => $validated['telefono'],
                'email' => $email,
                'direccion' => $validated['direccion'] ?? null,
                'rol' => 'CLIENTE',
                'password' => null,
                'activo' => true,
                'observaciones' => 'Registrado desde catálogo web.',
            ]);
        } else {
            $email = $validated['email'] ?? null;
            if ($email && Usuario::where('email', $email)->where('id', '!=', $cliente->id)->exists()) {
                $email = null;
            }

            $cliente->update(array_filter([
                'telefono' => $validated['telefono'],
                'email' => $email,
                'direccion' => $validated['direccion'] ?? null,
            ], static fn ($value): bool => filled($value)));
        }

        return $cliente;
    }

    private function crearEnlaceWhatsApp(Pedido $pedido, string $preferenciaPago): ?string
    {
        $telefono = config('services.whatsapp.phone');

        if (! filled($telefono)) {
            return null;
        }

        $detalle = $pedido->detalles->map(fn (DetallePedido $detalle): string => sprintf(
            '• %sx %s — Bs %s',
            $detalle->cantidad,
            $detalle->producto->nombre,
            number_format((float) $detalle->precio_acordado * $detalle->cantidad, 2, ',', '.'),
        ))->implode("\n");

        $mensaje = sprintf(
            "¡Hola! Deseo coordinar mi pedido #%d del Catálogo Natura:\n\n%s\n\n• Total estimado: Bs %s\n• Preferencia de pago: %s\n\n¿Me podrías confirmar la disponibilidad y entrega, por favor?",
            $pedido->id,
            $detalle,
            number_format($pedido->total, 2, ',', '.'),
            $preferenciaPago,
        );

        return 'https://wa.me/'.preg_replace('/\D+/', '', $telefono).'?text='.urlencode($mensaje);
    }
}
