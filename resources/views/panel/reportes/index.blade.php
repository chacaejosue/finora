<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finora | Reportes</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-finora-surface text-finora-navy antialiased dark:bg-slate-950 dark:text-slate-100">
    <header class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-finora-blue">Panel comercial</p>
                <h1 class="text-2xl font-extrabold">Reportes</h1>
            </div>
            <div class="flex items-center gap-3">
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('panel.reportes.index') }}" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:flex-row sm:items-end">
            <label class="text-xs font-bold text-slate-600 dark:text-slate-300">Desde
                <input type="date" name="desde" value="{{ $desde }}" class="mt-1 block rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </label>
            <label class="text-xs font-bold text-slate-600 dark:text-slate-300">Hasta
                <input type="date" name="hasta" value="{{ $hasta }}" class="mt-1 block rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </label>
            <button class="rounded-xl bg-finora-navy px-4 py-2.5 text-xs font-bold text-white hover:bg-finora-dark">Actualizar</button>
            <a href="{{ route('panel.reportes.exportar', ['desde' => $desde, 'hasta' => $hasta]) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Exportar CSV</a>
        </form>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article class="rounded-2xl border border-blue-200 bg-white p-5 shadow-sm dark:border-blue-900/50 dark:bg-slate-900">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-300">point_of_sale</span>
                <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400">Ventas confirmadas (Ingresos)</p>
                <p class="mt-1 text-2xl font-extrabold text-finora-navy dark:text-white">@money($totalVentas)</p>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Costo mercadería: @money($costoVentas)</p>
            </article>

            <article class="rounded-2xl border border-emerald-200 bg-emerald-50/40 p-5 shadow-sm dark:border-emerald-900/50 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-300">trending_up</span>
                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">+{{ $margenRentabilidad }}% margen</span>
                </div>
                <p class="mt-4 text-xs font-bold text-emerald-700 dark:text-emerald-400">Ganancia real estimada (Utilidad)</p>
                <p class="mt-1 text-2xl font-extrabold text-emerald-700 dark:text-emerald-300">@money($gananciaReal)</p>
                <p class="mt-1 text-[11px] text-emerald-600 dark:text-emerald-400">Ingresos menos costo de compras</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-300">payments</span>
                <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400">Total cobrado</p>
                <p class="mt-1 text-2xl font-extrabold text-finora-navy dark:text-white">@money($totalCobrado)</p>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Recaudación en caja y cuentas</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-amber-600 dark:text-amber-300">account_balance</span>
                <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400">Clientes con saldo pendiente</p>
                <p class="mt-1 text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ $clientesConSaldo }}</p>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Cuentas por cobrar activas</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-300">shopping_cart</span>
                <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400">Pedidos pendientes</p>
                <p class="mt-1 text-2xl font-extrabold text-finora-navy dark:text-white">{{ $pedidosPendientes }}</p>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Por entregar o abastecer</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-300">receipt_long</span>
                <p class="mt-4 text-xs font-bold text-slate-500 dark:text-slate-400">Transacciones comerciales</p>
                <p class="mt-1 text-2xl font-extrabold text-finora-navy dark:text-white">{{ $ventas->count() }}</p>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">{{ $ventasContado }} al contado · {{ $ventasCredito }} a crédito</p>
            </article>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3"><h2 class="font-extrabold">Productos más vendidos</h2><span class="text-xs text-slate-500 dark:text-slate-400">{{ $ventasContado }} contado · {{ $ventasCredito }} crédito</span></div>
                <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($productosMasVendidos as $producto)
                        <div class="flex items-center justify-between gap-3 py-3 text-sm">
                            <span class="truncate">{{ $producto->nombre }}</span>
                            <div class="shrink-0 text-right">
                                <span class="font-bold block">{{ $producto->unidades }} u. · @money($producto->importe)</span>
                                @if (($producto->ganancia ?? 0) > 0)
                                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">Ganancia: +@money($producto->ganancia)</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-slate-500 dark:text-slate-400">No hay ventas confirmadas en este periodo.</p>
                    @endforelse
                </div>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <h2 class="font-extrabold">Stock bajo</h2>
                <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($stockBajo as $producto)
                        <div class="flex items-center justify-between gap-3 py-3 text-sm"><span class="truncate">{{ $producto->nombre }}</span><span class="shrink-0 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">{{ $producto->movimientos_inventario_sum_cantidad ?? 0 }} u.</span></div>
                    @empty
                        <p class="py-4 text-sm text-slate-500 dark:text-slate-400">No hay productos con stock bajo.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="border-b border-slate-100 p-5 dark:border-slate-800"><h2 class="font-extrabold">Ventas del periodo</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-300"><tr><th class="px-5 py-3">Venta</th><th class="px-5 py-3">Fecha</th><th class="px-5 py-3">Cliente</th><th class="px-5 py-3">Pago</th><th class="px-5 py-3 text-right">Total</th><th class="px-5 py-3 text-right">Saldo</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($ventas as $venta)
                            <tr><td class="px-5 py-3 font-bold">#{{ $venta->id }}</td><td class="px-5 py-3">{{ $venta->fecha?->format('d/m/Y') }}</td><td class="px-5 py-3">{{ $venta->cliente?->persona?->nombre ?? '—' }}</td><td class="px-5 py-3">{{ $venta->forma_pago }}</td><td class="px-5 py-3 text-right font-bold">@money($venta->total)</td><td class="px-5 py-3 text-right">@money($venta->saldo_pendiente)</td></tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No hay ventas para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
