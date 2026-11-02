<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Kardex Físico-Valorado (CPP) | Módulo Contable</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Kardex Físico-Valorado</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-sm">print</span>
                    <span>Imprimir Kardex</span>
                </button>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('panel.contabilidad.header-nav', [
            'titulo' => 'Kardex Físico-Valorado por Costo Promedio Ponderado (CPP)',
            'subtitulo' => 'Control de inventario permanente valuado al 87% neto de compras y descarga automática en ventas.',
            'accionBoton' => '<button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer"><span class="material-symbols-outlined text-sm">print</span>Imprimir</button>'
        ])

        <!-- Selector de Producto -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 mb-6 shadow-xs">
            <form method="GET" action="{{ route('contabilidad.kardex-valorado') }}" class="flex flex-col sm:flex-row items-end gap-3">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Seleccionar Producto del Catálogo</label>
                    <select name="producto_id" onchange="this.form.submit()" class="w-full text-xs font-semibold px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue">
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}" {{ $productoSeleccionado && $productoSeleccionado->id == $prod->id ? 'selected' : '' }}>
                                [{{ $prod->codigo }}] {{ $prod->nombre }} (Stock: {{ $prod->stock_fisico }} | CPP: Bs {{ number_format($prod->costo_promedio, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <noscript>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white">Ver Kardex</button>
                </noscript>
            </form>
        </div>

        @if($productoSeleccionado)
            <!-- Resumen del Producto Seleccionado -->
            <div class="bg-gradient-to-r from-slate-900 to-finora-navy rounded-2xl p-6 text-white mb-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                        Método: Costo Promedio Ponderado (CPP)
                    </span>
                    <h2 class="text-xl font-heading font-extrabold text-white mt-2">
                        [{{ $productoSeleccionado->codigo }}] {{ $productoSeleccionado->nombre }}
                    </h2>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Categoría: {{ $productoSeleccionado->categoria?->nombre ?? 'General' }} • Precio Venta Actual: Bs {{ number_format($productoSeleccionado->precio_venta_actual, 2) }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="bg-white/10 px-4 py-2.5 rounded-xl border border-white/10 text-right">
                        <div class="text-[10px] uppercase font-bold text-slate-300">Stock Físico Actual</div>
                        <div class="text-xl font-mono font-extrabold text-cyan-300">
                            {{ $saldoFisicoActual }} <span class="text-xs font-normal">und</span>
                        </div>
                    </div>
                    <div class="bg-white/10 px-4 py-2.5 rounded-xl border border-white/10 text-right">
                        <div class="text-[10px] uppercase font-bold text-slate-300">CPP Vigente</div>
                        <div class="text-xl font-mono font-extrabold text-emerald-300">
                            Bs {{ number_format($productoSeleccionado->costo_promedio, 2) }}
                        </div>
                    </div>
                    <div class="bg-white/10 px-4 py-2.5 rounded-xl border border-white/10 text-right">
                        <div class="text-[10px] uppercase font-bold text-slate-300">Valor en Almacén</div>
                        <div class="text-xl font-mono font-extrabold text-amber-300">
                            Bs {{ number_format($saldoValoradoActual, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla del Kardex Físico-Valorado -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden printable-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase tracking-wider text-center border-b border-slate-200">
                                <th class="py-2.5 px-3 text-left" rowspan="2">Fecha</th>
                                <th class="py-2.5 px-3 text-left" rowspan="2">Movimiento / Detalle</th>
                                <th class="py-2 px-3 border-l border-slate-200 bg-blue-50/60 text-finora-blue" colspan="3">CONTROL FÍSICO (UNIDADES)</th>
                                <th class="py-2.5 px-3 text-right border-l border-slate-200" rowspan="2">Costo Unitario (Bs)</th>
                                <th class="py-2 px-3 border-l border-slate-200 bg-emerald-50/60 text-emerald-800" colspan="3">CONTROL VALORADO (BS)</th>
                                <th class="py-2.5 px-3 text-right border-l border-slate-200" rowspan="2">CPP (Bs)</th>
                            </tr>
                            <tr class="bg-slate-50 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200 text-right">
                                <th class="py-2 px-3 border-l border-slate-200 w-20">Entrada</th>
                                <th class="py-2 px-3 w-20">Salida</th>
                                <th class="py-2 px-3 w-20 font-bold text-finora-navy">Saldo</th>
                                <th class="py-2 px-3 border-l border-slate-200 w-28">Entrada</th>
                                <th class="py-2 px-3 w-28">Salida</th>
                                <th class="py-2 px-3 w-28 font-bold text-emerald-800">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-sans">
                            @forelse($movimientosKardex as $k)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-2.5 px-3 text-slate-600 font-medium whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($k->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-700 max-w-xs truncate" title="{{ $k->observacion }}">
                                        @php
                                            $badgeTipo = match($k->tipo) {
                                                'ENTRADA_COMPRA' => 'bg-cyan-100 text-cyan-800',
                                                'SALIDA_VENTA' => 'bg-rose-100 text-rose-800',
                                                'AJUSTE_ENTRADA' => 'bg-emerald-100 text-emerald-800',
                                                'AJUSTE_SALIDA' => 'bg-amber-100 text-amber-800',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                        @endphp
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $badgeTipo }} mr-1">
                                            {{ str_replace('_', ' ', $k->tipo) }}
                                        </span>
                                        <span class="text-[11px]">{{ $k->observacion }}</span>
                                    </td>
                                    <!-- Físico -->
                                    <td class="py-2.5 px-3 text-right font-mono border-l border-slate-100 {{ $k->cantidad_entrada > 0 ? 'text-slate-900 font-bold' : 'text-slate-300' }}">
                                        {{ $k->cantidad_entrada > 0 ? $k->cantidad_entrada : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono {{ $k->cantidad_salida > 0 ? 'text-slate-900 font-bold' : 'text-slate-300' }}">
                                        {{ $k->cantidad_salida > 0 ? $k->cantidad_salida : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-extrabold text-finora-navy bg-blue-50/20">
                                        {{ $k->saldo_fisico }}
                                    </td>
                                    <!-- Costo Unitario -->
                                    <td class="py-2.5 px-3 text-right font-mono border-l border-slate-100 text-slate-700">
                                        {{ number_format($k->costo_unitario, 2) }}
                                    </td>
                                    <!-- Valorado -->
                                    <td class="py-2.5 px-3 text-right font-mono border-l border-slate-100 {{ $k->costo_total_entrada > 0 ? 'text-slate-900 font-bold' : 'text-slate-300' }}">
                                        {{ $k->costo_total_entrada > 0 ? number_format($k->costo_total_entrada, 2) : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono {{ $k->costo_total_salida > 0 ? 'text-slate-900 font-bold' : 'text-slate-300' }}">
                                        {{ $k->costo_total_salida > 0 ? number_format($k->costo_total_salida, 2) : '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-extrabold text-emerald-800 bg-emerald-50/20">
                                        {{ number_format($k->saldo_valorado, 2) }}
                                    </td>
                                    <!-- CPP Progresivo -->
                                    <td class="py-2.5 px-3 text-right font-mono font-extrabold border-l border-slate-100 text-finora-blue">
                                        Bs {{ number_format($k->cpp, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-12 text-center text-slate-400">
                                        No hay movimientos de inventario registrados para este producto.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </main>

    @include('partials.panel-footer')
</body>
</html>
