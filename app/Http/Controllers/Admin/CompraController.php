<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompraRequest;
use App\Models\Categoria;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\TipoCambioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompraController extends Controller
{
    public function __construct(private TipoCambioService $tipoCambio) {}

    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'registradoPor', 'detalles']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->input('proveedor_id'));
        }

        $compras = $query->latest('id')->paginate(15)->withQueryString();
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();

        return view('panel.compras.index', compact('compras', 'proveedores'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        $cuentaCaja = \App\Models\CuentaContable::where('codigo', '1.1.1.01')->first();
        $cuentaBancos = \App\Models\CuentaContable::where('codigo', '1.1.1.02')->first();
        $saldoCaja = $cuentaCaja ? (float) $cuentaCaja->saldo_actual : 0.0;
        $saldoBancos = $cuentaBancos ? (float) $cuentaBancos->saldo_actual : 0.0;

        return view('panel.compras.create', compact('proveedores', 'productos', 'saldoCaja', 'saldoBancos'));
    }

    public function store(CompraRequest $request)
    {
        $validated = $request->validated();

        $compra = DB::transaction(function () use ($validated, $request) {
            $compra = Compra::create([
                'proveedor_id' => $validated['proveedor_id'],
                'registrado_por_usuario_id' => $request->user()->id,
                'fecha_solicitud' => now(),
                'forma_pago' => $validated['forma_pago'] ?? 'CONTADO',
                'metodo_pago' => $validated['metodo_pago'] ?? null,
                'estado' => 'BORRADOR',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($validated['detalles'] as $item) {
                $productoId = $item['producto_id'] ?? null;

                $costo = isset($item['costo_unitario']) && $item['costo_unitario'] !== null && $item['costo_unitario'] !== ''
                    ? (float) $item['costo_unitario']
                    : $this->convertirDolaresABolivianos($item['costo_unitario_usd'] ?? 0);

                if (! $productoId && ! empty($item['nuevo_producto_nombre'])) {
                    $categoriaId = Categoria::first()?->id ?? 1;
                    $codigo = ! empty($item['nuevo_producto_codigo'])
                        ? mb_strtoupper(trim($item['nuevo_producto_codigo']), 'UTF-8')
                        : 'TEMP-'.strtoupper(Str::random(6));

                    $nuevoProducto = Producto::create([
                        'proveedor_id' => $validated['proveedor_id'],
                        'categoria_id' => $categoriaId,
                        'codigo' => $codigo,
                        'nombre' => mb_strtoupper(trim($item['nuevo_producto_nombre']), 'UTF-8'),
                        'descripcion' => 'Registrado automáticamente desde Compra #'.$compra->id,
                        'precio_venta_actual' => $costo > 0 ? round($costo * 1.30, 2) : 0,
                        'publicado' => false,
                        'activo' => true,
                    ]);

                    $productoId = $nuevoProducto->id;
                }

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $productoId,
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $costo,
                ]);
            }

            return $compra;
        });

        return redirect()->route('panel.compras.show', $compra)
            ->with('exito', 'Compra guardada en borrador correctamente.');
    }

    private function convertirDolaresABolivianos(float|string $precioEnDolares): float
    {
        return round((float) $precioEnDolares * $this->tipoCambio->bolivianosPorDolar(), 2);
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'registradoPor', 'detalles.producto']);

        return view('panel.compras.show', compact('compra'));
    }

    public function cambiarEstado(Request $request, Compra $compra)
    {
        $nuevoEstado = $request->input('estado');
        $estadosValidos = ['BORRADOR', 'SOLICITADA', 'EN_CAMINO', 'RECIBIDA', 'CANCELADA'];

        if (! in_array($nuevoEstado, $estadosValidos)) {
            return redirect()->back()->withErrors(['error' => 'Estado no válido.']);
        }

        if ($compra->estado === 'RECIBIDA') {
            return redirect()->back()->withErrors(['error' => 'Una compra que ya ha sido recibida no puede modificar su estado.']);
        }

        DB::transaction(function () use ($compra, $nuevoEstado, $request) {
            if ($nuevoEstado === 'RECIBIDA') {
                $compra->update([
                    'estado' => 'RECIBIDA',
                    'fecha_recepcion' => now(),
                ]);

                foreach ($compra->detalles as $detalle) {
                    Producto::whereKey($detalle->producto_id)->lockForUpdate()->firstOrFail();

                    MovimientoInventario::create([
                        'producto_id' => $detalle->producto_id,
                        'registrado_por_usuario_id' => $request->user()->id,
                        'detalle_compra_id' => $detalle->id,
                        'tipo' => 'ENTRADA_COMPRA',
                        'cantidad' => $detalle->cantidad,
                        'fecha' => now(),
                        'observacion' => "Ingreso por recepción de compra #{$compra->id}",
                    ]);
                }
            } else {
                $compra->update(['estado' => $nuevoEstado]);
            }
        });

        return redirect()->route('panel.compras.show', $compra)
            ->with('exito', "Estado de la compra actualizado a {$nuevoEstado}.");
    }
}
