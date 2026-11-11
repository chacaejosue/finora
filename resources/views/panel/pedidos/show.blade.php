<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Detalle de Pedido #{{ $pedido->id }} | Panel del Consultor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.index') }}">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Ficha de Pedido</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.pedidos.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-finora-blue hover:text-finora-deep-blue">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Volver a pedidos
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

        @if ($errors->any())
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 flex items-center justify-between text-red-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold text-finora-navy">
                        Pedido #{{ $pedido->id }}
                    </h1>
                    @php
                        $badgeStyle = match($pedido->estado) {
                            'COMPLETADO' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                            'LISTO_ENTREGA' => 'bg-cyan-50 border-cyan-200 text-cyan-800',
                            'RESERVADO' => 'bg-blue-50 border-blue-200 text-blue-800',
                            'PENDIENTE_ABASTECIMIENTO' => 'bg-amber-50 border-amber-200 text-amber-800',
                            'CANCELADO' => 'bg-red-50 border-red-200 text-red-700',
                            default => 'bg-slate-100 border-slate-200 text-slate-600',
                        };
                    @endphp
                     <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyle }} dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                        {{ $pedido->estado }}
                    </span>
                </div>
                <p class="text-xs text-finora-subtle mt-1">
                     Cliente: <strong class="text-finora-navy">{{ $pedido->cliente->persona->nombre }} {{ $pedido->cliente->persona->apellido }}</strong> &bull; Registrado el {{ $pedido->fecha ? $pedido->fecha->format('d/m/Y H:i') : '-' }} &bull; Teléfono: <strong class="text-finora-navy">{{ $pedido->cliente->persona->telefono ?? 'No registrado' }}</strong>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($pedido->estado === 'PENDIENTE')
                    <form action="{{ route('panel.pedidos.reservar', $pedido) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1.5 shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                            <span>Verificar y reservar stock</span>
                        </button>
                    </form>
                @endif

                @if ($pedido->estado !== 'CANCELADO' && ! $pedido->venta)
                    <a href="{{ route('panel.ventas.create', ['pedido_id' => $pedido->id]) }}" class="px-4 py-2 bg-[#00c1de] hover:bg-[#00a3bd] text-white text-xs font-bold rounded-xl transition-all inline-flex items-center gap-1.5 shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
                        <span>Facturar / Generar Venta desde Pedido</span>
                    </a>
                @endif

                @if ($pedido->venta)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">receipt_long</span>
                        <span>Venta #{{ $pedido->venta->id }} ({{ $pedido->venta->estado }})</span>
                        <a href="{{ route('panel.ventas.show', $pedido->venta) }}" class="underline font-bold hover:text-emerald-950 ml-1">Ver venta</a>
                    </div>
                @endif

                @if ($pedido->estado === 'LISTO_ENTREGA')
                    <form action="{{ route('panel.pedidos.entregar', $pedido) }}" method="POST" class="flex max-w-full flex-wrap items-end gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/60 p-2.5 dark:border-emerald-800 dark:bg-emerald-950/50">
                        @csrf
                        @method('PATCH')
                        <label class="text-xs font-bold text-emerald-900 dark:text-emerald-200">Recibido por
                            <input type="text" name="recibido_por" required value="{{ $pedido->cliente->persona->nombre }} {{ $pedido->cliente->persona->apellido }}" placeholder="Nombre de quien recibe" class="mt-1 w-44 rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-finora-blue dark:border-emerald-700 dark:bg-slate-900 dark:text-white">
                        </label>
                        <label class="text-xs font-bold text-emerald-900 dark:text-emerald-200">Observación <span class="font-normal text-emerald-700 dark:text-emerald-300">(opcional)</span>
                            <input type="text" name="observaciones_entrega" placeholder="Nota de entrega" class="mt-1 w-44 rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-finora-blue dark:border-emerald-700 dark:bg-slate-900 dark:text-white">
                        </label>
                        <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl inline-flex items-center gap-1 shadow-xs cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            <span>Registrar entrega</span>
                        </button>
                    </form>
                @elseif ($pedido->estado === 'COMPLETADO')
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">task_alt</span>
                        <span>Entregado a: <strong>{{ $pedido->recibido_por ?: 'Cliente' }}</strong> {{ $pedido->fecha_entrega ? 'el '.$pedido->fecha_entrega->format('d/m/Y H:i') : '' }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-finora-navy">Items del Pedido</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-[#F8FAFC] text-finora-subtle font-bold uppercase">
                             <tr class="dark:border-slate-700">
                            <th class="py-2.5 px-3">Código</th>
                            <th class="py-2.5 px-3">Producto</th>
                            <th class="py-2.5 px-3 text-center">Cantidad Pedida</th>
                             <th class="py-2.5 px-3 text-center">Cantidad Reservada</th>
                             <th class="py-2.5 px-3 text-center">Stock disponible</th>
                            <th class="py-2.5 px-3 text-right">Precio Acordado</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pedido->detalles as $det)
                            <tr>
                                <td class="py-2.5 px-3 font-mono text-slate-600 font-bold">{{ $det->producto->codigo }}</td>
                                <td class="py-2.5 px-3 font-bold text-finora-navy">{{ $det->producto->nombre }}</td>
                                <td class="py-2.5 px-3 text-center font-bold">{{ $det->cantidad }}</td>
                                  <td class="py-2.5 px-3 text-center font-bold text-cyan-700 bg-cyan-50/50 dark:bg-cyan-950/70 dark:text-cyan-200 rounded">{{ $det->cantidad_reservada }}</td>
                                  <td class="py-2.5 px-3 text-center font-bold {{ $det->producto->stock_disponible > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300' }}">{{ $det->producto->stock_disponible }}</td>
                                <td class="py-2.5 px-3 text-right text-slate-600">@money($det->precio_acordado)</td>
                                <td class="py-2.5 px-3 text-right font-bold text-finora-navy">@money($det->subtotal)</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-slate-200 font-bold text-sm text-finora-navy">
                        <tr>
                             <td colspan="6" class="py-3 px-3 text-right">Total Pedido:</td>
                            <td class="py-3 px-3 text-right text-finora-blue">@money($pedido->total)</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($pedido->observaciones)
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-xs font-bold text-finora-subtle block">Observaciones:</span>
                    <p class="text-xs text-slate-700 mt-0.5">{{ $pedido->observaciones }}</p>
                </div>
            @endif

                 @if ($pedido->fecha_entrega)
                <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div><span class="text-finora-subtle block">Fecha de entrega:</span><strong>{{ $pedido->fecha_entrega->format('d/m/Y H:i') }}</strong></div>
                    <div><span class="text-finora-subtle block">Recibido por:</span><strong>{{ $pedido->recibido_por }}</strong></div>
                    <div><span class="text-finora-subtle block">Registró:</span><strong>{{ $pedido->entregadoPor?->persona?->nombre ?? '—' }}</strong></div>
                 </div>
             @endif
        </div>
    </main>
</body>
</html>
