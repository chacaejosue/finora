<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Libro Mayor General | Módulo Contable</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Libro Mayor General</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('contabilidad.libro-diario') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">menu_book</span>
                    <span>Libro Diario</span>
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('panel.contabilidad.header-nav', [
            'titulo' => 'Libro Mayor General (Cuentas en T)',
            'subtitulo' => 'Desglose cronológico de pases del Libro Diario con saldo progresivo según naturaleza deudora o acreedora.',
            'accionBoton' => '<button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer"><span class="material-symbols-outlined text-sm">print</span>Imprimir Mayor</button>'
        ])

        <!-- Selector de Cuenta y Filtros -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 mb-6 shadow-xs">
            <form method="GET" action="{{ route('contabilidad.libro-mayor') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Seleccionar Cuenta Contable Imputable</label>
                    <select name="cuenta_id" onchange="this.form.submit()" class="w-full text-xs font-semibold px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue">
                        @foreach($cuentas as $cta)
                            <option value="{{ $cta->id }}" {{ $cuentaSeleccionada && $cuentaSeleccionada->id == $cta->id ? 'selected' : '' }}>
                                {{ $cta->codigo }} - {{ $cta->nombre }} ({{ $cta->tipo }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Fecha Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                </div>

                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors h-9">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        @if($cuentaSeleccionada)
            <!-- Ficha Técnica de la Cuenta Seleccionada -->
            <div class="bg-gradient-to-r from-slate-900 to-finora-navy rounded-2xl p-6 text-white mb-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-white/20 text-cyan-300">
                            {{ $cuentaSeleccionada->tipo }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-white/10 text-slate-300">
                            Naturaleza: {{ $cuentaSeleccionada->naturaleza }}
                        </span>
                        <span class="text-xs text-slate-400">Nivel {{ $cuentaSeleccionada->nivel }}</span>
                    </div>
                    <h2 class="text-xl font-heading font-extrabold text-white">
                        <span class="font-mono text-cyan-400">{{ $cuentaSeleccionada->codigo }}</span> • {{ $cuentaSeleccionada->nombre }}
                    </h2>
                </div>

                <div class="text-right bg-white/10 px-5 py-3 rounded-xl border border-white/10">
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-300">Saldo Final Actual</div>
                    <div class="text-2xl font-mono font-extrabold {{ $saldoFinal >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        Bs {{ number_format($saldoFinal, 2) }}
                    </div>
                    <div class="text-[10px] text-slate-400">
                        {{ $cuentaSeleccionada->naturaleza === 'DEUDORA' ? 'Saldo Deudor (Debe - Haber)' : 'Saldo Acreedor (Haber - Debe)' }}
                    </div>
                </div>
            </div>

            <!-- Tabla de Movimientos del Libro Mayor -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                <th class="py-3 px-4">Fecha</th>
                                <th class="py-3 px-4">Comprobante</th>
                                <th class="py-3 px-4">Tipo</th>
                                <th class="py-3 px-4">Glosa del Asiento</th>
                                <th class="py-3 px-4 text-right">Debe (Bs)</th>
                                <th class="py-3 px-4 text-right">Haber (Bs)</th>
                                <th class="py-3 px-4 text-right">Saldo Acumulado (Bs)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($movimientos as $mov)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 px-4 text-slate-600 font-medium">
                                        {{ \Carbon\Carbon::parse($mov->asiento->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-finora-navy">
                                        <a href="{{ route('contabilidad.libro-diario', ['search' => $mov->asiento->numero]) }}" class="hover:underline text-finora-blue">
                                            {{ $mov->asiento->numero }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700">
                                            {{ $mov->asiento->tipoComprobante->codigo ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 max-w-sm truncate" title="{{ $mov->glosa_detalle ?: $mov->asiento->glosa }}">
                                        {{ $mov->glosa_detalle ?: $mov->asiento->glosa }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono {{ $mov->debe > 0 ? 'font-bold text-slate-900' : 'text-slate-300' }}">
                                        {{ $mov->debe > 0 ? number_format($mov->debe, 2) : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono {{ $mov->haber > 0 ? 'font-bold text-slate-900' : 'text-slate-300' }}">
                                        {{ $mov->haber > 0 ? number_format($mov->haber, 2) : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-finora-blue">
                                        {{ number_format($mov->saldo_progresivo, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">receipt_long</span>
                                        <p class="text-xs">No hay movimientos registrados para esta cuenta en el periodo indicado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($movimientos->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50 border-t-2 border-slate-200 font-mono font-extrabold text-xs">
                                    <td colspan="4" class="py-3 px-4 text-right font-sans uppercase tracking-wider text-[11px] text-slate-700">
                                        Totales Acumulados:
                                    </td>
                                    <td class="py-3 px-4 text-right text-finora-navy">
                                        Bs {{ number_format($totalDebe, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right text-finora-navy">
                                        Bs {{ number_format($totalHaber, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right text-emerald-700 bg-emerald-50/50">
                                        Bs {{ number_format($saldoFinal, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @endif

    </main>

    @include('partials.panel-footer')
</body>
</html>
