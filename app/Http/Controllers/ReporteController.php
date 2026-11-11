<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        [$desde, $hasta] = $this->periodo($request);
        $ventas = $this->ventas($desde, $hasta);
        $productosMasVendidos = $this->productosMasVendidos($desde, $hasta);
        $stockBajo = Producto::with('categoria')
            ->withSum('movimientosInventario', 'cantidad')
            ->get()
            ->filter(fn (Producto $producto): bool => (int) ($producto->movimientos_inventario_sum_cantidad ?? 0) <= 3)
            ->sortBy('movimientos_inventario_sum_cantidad')
            ->take(10);

        $totalVentas = (float) $ventas->sum(fn (Venta $venta): float => $venta->total);
        $costoVentas = (float) $ventas->sum(function (Venta $venta): float {
            return (float) $venta->detalles->sum(function ($d): float {
                return (float) $d->cantidad * (float) ($d->producto?->costo_promedio ?? 0);
            });
        });
        $gananciaReal = max(0, $totalVentas - $costoVentas);
        $margenRentabilidad = $totalVentas > 0 ? round(($gananciaReal / $totalVentas) * 100, 1) : 0;

        return view('panel.reportes.index', [
            'desde' => $desde,
            'hasta' => $hasta,
            'ventas' => $ventas,
            'totalVentas' => $totalVentas,
            'costoVentas' => $costoVentas,
            'gananciaReal' => $gananciaReal,
            'margenRentabilidad' => $margenRentabilidad,
            'totalCobrado' => $ventas->sum(fn (Venta $venta): float => (float) $venta->pagos->where('estado', 'REGISTRADO')->sum('monto')),
            'ventasContado' => $ventas->where('forma_pago', 'CONTADO')->count(),
            'ventasCredito' => $ventas->where('forma_pago', 'CREDITO')->count(),
            'productosMasVendidos' => $productosMasVendidos,
            'stockBajo' => $stockBajo,
            'pedidosPendientes' => Pedido::whereIn('estado', ['PENDIENTE', 'RESERVADO', 'PENDIENTE_ABASTECIMIENTO'])->count(),
            'clientesConSaldo' => Venta::where('forma_pago', 'CREDITO')
                ->whereIn('estado', ['CONFIRMADA', 'BORRADOR'])
                ->with(['pagos', 'detalles'])
                ->get()
                ->filter(fn (Venta $venta): bool => $venta->saldo_pendiente > 0.01)
                ->count(),
        ]);
    }

    public function exportar(Request $request): StreamedResponse
    {
        [$desde, $hasta] = $this->periodo($request);
        $ventas = $this->ventas($desde, $hasta);

        return response()->streamDownload(function () use ($ventas): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Venta', 'Fecha', 'Cliente', 'Forma de pago', 'Estado', 'Total Venta', 'Costo Estimado', 'Ganancia Real', 'Pagado', 'Saldo']);

            foreach ($ventas as $venta) {
                $costoVenta = (float) $venta->detalles->sum(fn ($d): float => (float) $d->cantidad * (float) ($d->producto?->costo_promedio ?? 0));
                $gananciaVenta = max(0, $venta->total - $costoVenta);

                fputcsv($handle, [
                    $venta->id,
                    $venta->fecha?->format('Y-m-d H:i'),
                    trim(($venta->cliente?->nombre ?? '') . ' ' . ($venta->cliente?->apellido ?? '')),
                    $venta->forma_pago,
                    $venta->estado,
                    number_format($venta->total, 2, '.', ''),
                    number_format($costoVenta, 2, '.', ''),
                    number_format($gananciaVenta, 2, '.', ''),
                    number_format((float) $venta->pagos->where('estado', 'REGISTRADO')->sum('monto'), 2, '.', ''),
                    number_format($venta->saldo_pendiente, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, "reporte-ventas-{$desde}-{$hasta}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function periodo(Request $request): array
    {
        $validated = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        return [
            Carbon::parse($validated['desde'] ?? now()->startOfMonth())->toDateString(),
            Carbon::parse($validated['hasta'] ?? now())->toDateString(),
        ];
    }

    private function ventas(string $desde, string $hasta)
    {
        return Venta::with(['cliente', 'detalles.producto', 'pagos'])
            ->where('estado', 'CONFIRMADA')
            ->whereBetween('fecha', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->latest('fecha')
            ->get();
    }

    private function productosMasVendidos(string $desde, string $hasta)
    {
        return DetalleVenta::query()
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as importe'),
                DB::raw('SUM(detalle_ventas.cantidad * (detalle_ventas.precio_unitario - COALESCE(productos.costo_promedio, 0))) as ganancia')
            )
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->where('ventas.estado', 'CONFIRMADA')
            ->whereBetween('ventas.fecha', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('unidades')
            ->limit(10)
            ->get();
    }
}
