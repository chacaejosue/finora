<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Balance de Sumas y Saldos | Módulo Contable</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Sumas y Saldos</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-sm">print</span>
                    <span>Imprimir Balance</span>
                </button>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('panel.contabilidad.header-nav', [
            'titulo' => 'Balance de Comprobación de Sumas y Saldos',
            'subtitulo' => 'Verificación matemática de la Partida Doble en 6 columnas antes de la preparación de los Estados Financieros.',
            'accionBoton' => '<button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer"><span class="material-symbols-outlined text-sm">print</span>Imprimir</button>'
        ])

        <!-- Barra de Filtros y Estado de Cuadre -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="md:col-span-2 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('contabilidad.sumas-saldos') }}" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Fecha Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                    </div>
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors h-9">
                        Filtrar Periodo
                    </button>
                    @if(request()->hasAny(['fecha_desde', 'fecha_hasta']))
                        <a href="{{ route('contabilidad.sumas-saldos') }}" class="px-3 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition-colors h-9 flex items-center">
                            Restablecer
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tarjeta de Certificación de Cuadre -->
            <div class="p-4 rounded-2xl border {{ $cuadranSumas && $cuadranSaldos ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }} flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $cuadranSumas && $cuadranSaldos ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">{{ $cuadranSumas && $cuadranSaldos ? 'verified' : 'error' }}</span>
                </div>
                <div>
                    <div class="text-xs font-extrabold uppercase tracking-wide">
                        {{ $cuadranSumas && $cuadranSaldos ? 'Partida Doble Certificada' : 'Diferencia en Sumas o Saldos' }}
                    </div>
                    <div class="text-[11px] mt-0.5 opacity-80">
                        {{ $cuadranSumas && $cuadranSaldos ? 'Sumas y saldos coinciden exactamente al centavo.' : 'Existe una diferencia contable a verificar.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de 6 Columnas de Sumas y Saldos -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden printable-card">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 text-[10px] font-extrabold uppercase tracking-wider border-b border-slate-200 text-center">
                            <th class="py-2.5 px-3 text-left" rowspan="2">Código</th>
                            <th class="py-2.5 px-4 text-left" rowspan="2">Cuenta Contable</th>
                            <th class="py-2 px-4 border-l border-slate-200 bg-blue-50/60 text-finora-blue" colspan="2">SUMAS DEL MAYOR (Bs)</th>
                            <th class="py-2 px-4 border-l border-slate-200 bg-emerald-50/60 text-emerald-800" colspan="2">SALDOS COMPUTADOS (Bs)</th>
                        </tr>
                        <tr class="bg-slate-50 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                            <th class="py-2 px-4 text-right border-l border-slate-200 w-32">Debe</th>
                            <th class="py-2 px-4 text-right w-32">Haber</th>
                            <th class="py-2 px-4 text-right border-l border-slate-200 w-32">Deudor</th>
                            <th class="py-2 px-4 text-right w-32">Acreedor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($filas as $fila)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-500">
                                    {{ $fila['cuenta']->codigo }}
                                </td>
                                <td class="py-2.5 px-4 font-semibold text-finora-navy">
                                    <a href="{{ route('contabilidad.libro-mayor', ['cuenta_id' => $fila['cuenta']->id]) }}" class="hover:underline text-finora-blue">
                                        {{ $fila['cuenta']->nombre }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">({{ $fila['cuenta']->tipo }})</span>
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono border-l border-slate-100 {{ $fila['debe'] > 0 ? 'text-slate-900 font-medium' : 'text-slate-300' }}">
                                    {{ $fila['debe'] > 0 ? number_format($fila['debe'], 2) : '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono {{ $fila['haber'] > 0 ? 'text-slate-900 font-medium' : 'text-slate-300' }}">
                                    {{ $fila['haber'] > 0 ? number_format($fila['haber'], 2) : '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono border-l border-slate-100 {{ $fila['saldo_deudor'] > 0 ? 'font-bold text-finora-blue' : 'text-slate-300' }}">
                                    {{ $fila['saldo_deudor'] > 0 ? number_format($fila['saldo_deudor'], 2) : '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono {{ $fila['saldo_acreedor'] > 0 ? 'font-bold text-emerald-700' : 'text-slate-300' }}">
                                    {{ $fila['saldo_acreedor'] > 0 ? number_format($fila['saldo_acreedor'], 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    No hay cuentas con movimientos contables en el periodo seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100 border-t-2 border-slate-300 font-mono font-extrabold text-xs">
                            <td colspan="2" class="py-3 px-4 text-right font-sans uppercase tracking-wider text-[11px] text-slate-700">
                                Sumas Iguales y Cuadre:
                            </td>
                            <td class="py-3 px-4 text-right text-finora-navy border-l border-slate-200">
                                Bs {{ number_format($totalSumasDebe, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right text-finora-navy">
                                Bs {{ number_format($totalSumasHaber, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right text-finora-blue border-l border-slate-200">
                                Bs {{ number_format($totalSaldosDeudor, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right text-emerald-800">
                                Bs {{ number_format($totalSaldosAcreedor, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </main>

    @include('partials.panel-footer')
</body>
</html>
