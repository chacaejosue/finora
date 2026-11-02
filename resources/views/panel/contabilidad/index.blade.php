<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Módulo Contable | Panel General</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Módulo Contable (Bolivia)</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('contabilidad.asientos.crear') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-sm transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>Nuevo Asiento</span>
                </a>
                <a href="{{ route('panel.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">dashboard</span>
                    <span class="hidden sm:inline">Panel Comercial</span>
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session('exito'))
            <div role="alert" class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center justify-between text-emerald-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span>{{ session('exito') }}</span>
                </div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 text-sm font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('panel.contabilidad.header-nav', [
            'titulo' => 'Panel de Control Contable',
            'subtitulo' => 'Monitoreo en tiempo real del ciclo contable, liquidez, impuestos e indicadores patrimoniales.',
            'accionBoton' => '<a href="' . route('contabilidad.asientos.crear') . '" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-sm transition-all"><span class="material-symbols-outlined text-sm">add_circle</span>Nuevo Asiento Manual</a>'
        ])

        <!-- Cuadrícula de KPIs Principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Activos -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Activo</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                    </div>
                </div>
                <div class="text-2xl font-heading font-extrabold text-finora-navy">
                    Bs {{ number_format($totalActivo, 2) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>Disponible, Exigible y Almacén</span>
                </div>
            </div>

            <!-- Pasivos -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pasivo</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">credit_card</span>
                    </div>
                </div>
                <div class="text-2xl font-heading font-extrabold text-finora-navy">
                    Bs {{ number_format($totalPasivo, 2) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Proveedores e Impuestos por Pagar</span>
                </div>
            </div>

            <!-- Patrimonio Neto -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Patrimonio Neto</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">savings</span>
                    </div>
                </div>
                <div class="text-2xl font-heading font-extrabold text-finora-navy">
                    Bs {{ number_format($patrimonioNeto, 2) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Capital + Resultado de Gestión</span>
                </div>
            </div>

            <!-- Resultado del Ejercicio -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Resultado Neto</span>
                    <div class="w-9 h-9 rounded-xl {{ $resultadoGestion >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">{{ $resultadoGestion >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    </div>
                </div>
                <div class="text-2xl font-heading font-extrabold {{ $resultadoGestion >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    Bs {{ number_format($resultadoGestion, 2) }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $resultadoGestion >= 0 ? 'Utilidad operativa generada' : 'Pérdida en el ejercicio' }}
                </div>
            </div>
        </div>

        <!-- Fila de Indicadores Tributarios Ley 843 (Bolivia) -->
        <div class="bg-gradient-to-r from-slate-900 to-finora-navy rounded-2xl p-6 text-white mb-8 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-800 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-cyan-400">receipt_long</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-heading font-extrabold">Monitor Fiscal y Tributario (Bolivia)</h2>
                        <p class="text-xs text-slate-300">Posición de Débito/Crédito Fiscal IVA (13%) e Impuesto a las Transacciones (3%)</p>
                    </div>
                </div>
                <div class="text-xs text-slate-400">
                    Periodo fiscal evaluado: <span class="font-bold text-white">{{ $mes }}/{{ $anio }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-xs text-cyan-300 font-semibold mb-1 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">add_circle</span>
                        Crédito Fiscal IVA (13% en compras)
                    </div>
                    <div class="text-xl font-extrabold font-heading text-white">
                        Bs {{ number_format($saldoCreditoFiscal, 2) }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Activo Exigible acumulado a favor</p>
                </div>

                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-xs text-rose-300 font-semibold mb-1 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">remove_circle</span>
                        Débito Fiscal IVA (13% en ventas)
                    </div>
                    <div class="text-xl font-extrabold font-heading text-white">
                        Bs {{ number_format($saldoDebitoFiscalMes, 2) }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Pasivo fiscal generado en el mes</p>
                </div>

                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-xs text-amber-300 font-semibold mb-1 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">calculate</span>
                        IT por Pagar (3% s/ total facturado)
                    </div>
                    <div class="text-xl font-extrabold font-heading text-white">
                        Bs {{ number_format($saldoItMes, 2) }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Impuesto a las Transacciones devengado</p>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos del Ciclo Contable -->
        <h2 class="text-base font-heading font-extrabold text-finora-navy mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-finora-blue text-lg">explore</span>
            Etapas del Ciclo Contable ERP
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('contabilidad.balance-apertura') }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:border-finora-blue hover:shadow-md transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-lg">flag</span>
                    </div>
                    <h3 class="font-bold text-xs text-finora-navy">1. Balance de Apertura</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Comprobante inicial (APE) con inventario y aporte de capital social.</p>
                </div>
                <span class="text-[11px] font-bold text-finora-blue mt-3 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">Ver reporte →</span>
            </a>

            <a href="{{ route('contabilidad.libro-diario') }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:border-finora-blue hover:shadow-md transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-lg">menu_book</span>
                    </div>
                    <h3 class="font-bold text-xs text-finora-navy">2. Libro Diario</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $totalAsientos }} asientos ({{ $asientosAutomaticos }} automáticos por ventas/compras).</p>
                </div>
                <span class="text-[11px] font-bold text-finora-blue mt-3 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">Explorar diario →</span>
            </a>

            <a href="{{ route('contabilidad.libro-mayor') }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:border-finora-blue hover:shadow-md transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-lg">import_contacts</span>
                    </div>
                    <h3 class="font-bold text-xs text-finora-navy">3. Libro Mayor</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Saldos progresivos deudores/acreedores por cuenta contable.</p>
                </div>
                <span class="text-[11px] font-bold text-finora-blue mt-3 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">Consultar mayores →</span>
            </a>

            <a href="{{ route('contabilidad.estados-financieros') }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:border-finora-blue hover:shadow-md transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-lg">query_stats</span>
                    </div>
                    <h3 class="font-bold text-xs text-finora-navy">4. Estados Financieros</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Estado de Resultados (IUE 25%) y Balance General cuadrado.</p>
                </div>
                <span class="text-[11px] font-bold text-finora-blue mt-3 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">Generar balance →</span>
            </a>
        </div>

        <!-- Tabla de Últimos Asientos del Libro Diario -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-finora-blue">history</span>
                    <h2 class="font-heading font-extrabold text-sm text-finora-navy">Últimos Movimientos del Libro Diario</h2>
                </div>
                <a href="{{ route('contabilidad.libro-diario') }}" class="text-xs font-bold text-finora-blue hover:underline">
                    Ver todos los asientos ({{ $totalAsientos }}) →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Comprobante</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Fecha</th>
                            <th class="py-3 px-4">Glosa</th>
                            <th class="py-3 px-4">Origen</th>
                            <th class="py-3 px-4 text-right">Total (Bs)</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ultimosAsientos as $asiento)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-finora-navy">
                                    {{ $asiento->numero }}
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $codigo = $asiento->tipoComprobante->codigo ?? 'GEN';
                                        $badgeColor = match($codigo) {
                                            'ING' => 'bg-emerald-100 text-emerald-800',
                                            'EGR' => 'bg-rose-100 text-rose-800',
                                            'TRS' => 'bg-blue-100 text-blue-800',
                                            'APE' => 'bg-purple-100 text-purple-800',
                                            default => 'bg-slate-100 text-slate-800'
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $badgeColor }}">
                                        {{ $asiento->tipoComprobante->nombre ?? $codigo }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($asiento->fecha)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 max-w-xs truncate" title="{{ $asiento->glosa }}">
                                    {{ $asiento->glosa }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($asiento->contabilizable_type)
                                        @php
                                            $clase = class_basename($asiento->contabilizable_type);
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                            <span class="material-symbols-outlined text-[11px]">bolt</span>
                                            {{ $clase }} #{{ $asiento->contabilizable_id }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-400">Manual</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">
                                    Bs {{ number_format($asiento->total_debe, 2) }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($asiento->estado === 'APROBADO')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                            <span class="material-symbols-outlined text-xs">verified</span> Cuadrado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600">
                                            <span class="material-symbols-outlined text-xs">cancel</span> Anulado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    No hay asientos registrados aún en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    @include('partials.panel-footer')
</body>
</html>
