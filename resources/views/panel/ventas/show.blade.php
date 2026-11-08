<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Detalle de Venta #{{ $venta->id }} | Panel del Consultor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="openPanelDrawerBtn" type="button" class="p-2 rounded-xl text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors cursor-pointer" title="Navegación de módulos">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.index') }}">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Ficha de Venta</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.ventas.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Ventas</span>
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if (session('exito'))
            <div role="alert" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center justify-between text-emerald-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span>{{ session('exito') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->has('error'))
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 flex items-center justify-between text-red-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span>{{ $errors->first('error') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold text-finora-navy">
                        Venta #{{ $venta->id }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border bg-purple-50 border-purple-200 text-purple-800 dark:bg-purple-950/70 dark:border-purple-700 dark:text-purple-200">
                        {{ $venta->forma_pago }}
                    </span>
                    @php
                        $badgeStyle = match($venta->estado) {
                            'CONFIRMADA' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                            'ANULADA' => 'bg-red-50 border-red-200 text-red-700',
                            default => 'bg-slate-100 border-slate-200 text-slate-600',
                        };
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyle }} dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100">
                        {{ $venta->estado }}
                    </span>
                </div>
                <p class="text-xs text-finora-subtle mt-1">
                    Cliente: <strong class="text-finora-navy">{{ $venta->cliente->persona->nombre }} {{ $venta->cliente->persona->apellido }}</strong> &bull; Registrada el {{ $venta->fecha ? $venta->fecha->format('d/m/Y H:i') : '-' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($venta->estado === 'BORRADOR')
                    <form action="{{ route('panel.ventas.confirmar', $venta) }}" method="POST" onsubmit="return confirm('¿Confirmas la venta? Esto registrará la salida definitiva de inventario.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Confirmar Venta y Descontar Inventario
                        </button>
                    </form>
                @endif

                @if ($venta->estado === 'CONFIRMADA' && auth()->user()?->rol === 'CONSULTOR')
                    <form action="{{ route('panel.ventas.anular', $venta) }}" method="POST" onsubmit="return confirm('¿Confirmas la anulación de esta venta? Se revertirá la salida de inventario.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-700 text-xs font-bold rounded-xl transition-colors">
                            Anular Venta
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="font-heading font-bold text-sm text-finora-navy">Detalle de Productos</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs divide-y divide-slate-100">
                            <thead class="bg-[#F8FAFC] text-finora-subtle font-bold uppercase">
                                <tr>
                                    <th class="py-2.5 px-3">Código</th>
                                    <th class="py-2.5 px-3">Producto</th>
                                    <th class="py-2.5 px-3 text-center">Cantidad</th>
                                    <th class="py-2.5 px-3 text-right">Precio Unit.</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($venta->detalles as $det)
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono text-slate-600 font-bold">{{ $det->producto->codigo }}</td>
                                        <td class="py-2.5 px-3 font-bold text-finora-navy">{{ $det->producto->nombre }}</td>
                                        <td class="py-2.5 px-3 text-center font-bold">{{ $det->cantidad }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-600">@money($det->precio_unitario)</td>
                                        <td class="py-2.5 px-3 text-right font-bold text-finora-navy">@money($det->subtotal)</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-slate-200 font-bold text-xs text-finora-navy">
                                <tr>
                                    <td colspan="4" class="py-2 px-3 text-right text-finora-subtle">Subtotal General:</td>
                                    <td class="py-2 px-3 text-right">@money($venta->subtotal)</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="py-2 px-3 text-right text-finora-subtle">Descuento:</td>
                                    <td class="py-2 px-3 text-right text-red-600">-@money($venta->descuento)</td>
                                </tr>
                                <tr class="text-sm font-extrabold border-t border-slate-200">
                                    <td colspan="4" class="py-3 px-3 text-right">Total Venta:</td>
                                    <td class="py-3 px-3 text-right text-finora-blue">@money($venta->total)</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                    <h3 class="font-heading font-bold text-sm text-finora-navy border-b border-slate-100 pb-2">Resumen de Pagos</h3>
                    <div class="flex justify-between text-xs">
                        <span class="text-finora-subtle">Total Venta:</span>
                        <span class="font-bold text-finora-navy">@money($venta->total)</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-finora-subtle">Monto Pagado:</span>
                        <span class="font-bold text-emerald-700">@money($venta->total_pagado)</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-slate-100 pt-2">
                        <span class="font-bold text-finora-navy">Saldo Pendiente:</span>
                        <span class="font-extrabold text-red-600">@money($venta->saldo_pendiente)</span>
                    </div>

                    @if ($venta->estado === 'CONFIRMADA' && $venta->saldo_pendiente > 0)
                        <a href="{{ route('panel.pagos.create', ['venta_id' => $venta->id]) }}" class="w-full py-2.5 bg-finora-navy text-white text-xs font-semibold rounded-xl hover:bg-finora-dark transition-colors inline-flex items-center justify-center gap-1 shadow-sm mt-2">
                            <span class="material-symbols-outlined text-sm">payments</span>
                            Registrar Pago
                        </a>
                    @endif
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-3">
                    <h3 class="font-heading font-bold text-sm text-finora-navy border-b border-slate-100 pb-2">Historial de Pagos</h3>
                    @forelse ($venta->pagos as $p)
                        <div class="text-xs p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-finora-navy block">@money($p->monto)</span>
                                <span class="text-[10px] text-finora-subtle">{{ $p->metodo }} &bull; {{ $p->fecha ? $p->fecha->format('d/m/Y') : '' }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->estado === 'REGISTRADO' ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700' }}">
                                {{ $p->estado }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-finora-subtle py-2 text-center">No hay pagos registrados para esta venta.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    @include('partials.panel-footer')
</body>
</html>
