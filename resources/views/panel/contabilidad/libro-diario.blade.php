<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Libro Diario General | Módulo Contable</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Libro Diario General</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('contabilidad.asientos.crear') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-sm transition-all">
                    <span class="material-symbols-outlined text-sm">add_circle</span>
                    <span>Nuevo Asiento Manual</span>
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
            'titulo' => 'Libro Diario General',
            'subtitulo' => 'Registro cronológico obligatorio de hechos contables, comprobantes de ingreso, egreso, traspaso y apertura.',
            'accionBoton' => '<a href="' . route('contabilidad.asientos.crear') . '" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-sm transition-all"><span class="material-symbols-outlined text-sm">add_circle</span>Nuevo Asiento</a>'
        ])

        <!-- Barra de Filtros -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 mb-6 shadow-xs">
            <form method="GET" action="{{ route('contabilidad.libro-diario') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Buscar por Glosa o N°</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej. CI-2026 o Venta #4..." class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Tipo de Comprobante</label>
                    <select name="tipo_id" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposComprobante as $tc)
                            <option value="{{ $tc->id }}" {{ request('tipo_id') == $tc->id ? 'selected' : '' }}>
                                {{ $tc->codigo }} - {{ $tc->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Fecha Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-1.5 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'tipo_id', 'fecha_desde', 'fecha_hasta', 'estado']))
                        <a href="{{ route('contabilidad.libro-diario') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition-colors" title="Limpiar filtros">
                            <span class="material-symbols-outlined text-sm">refresh</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Lista de Asientos Cronológicos -->
        <div class="space-y-6">
            @forelse($asientos as $asiento)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden transition-all hover:border-slate-300">
                    <!-- Cabecera del Asiento -->
                    <div class="bg-slate-50/75 p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @php
                                $codigo = $asiento->tipoComprobante->codigo ?? 'GEN';
                                $badgeColor = match($codigo) {
                                    'ING' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'EGR' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'TRS' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'APE' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    default => 'bg-slate-100 text-slate-800 border-slate-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-extrabold border {{ $badgeColor }}">
                                {{ $asiento->numero }}
                            </span>
                            <div>
                                <span class="text-xs font-bold text-finora-navy">{{ $asiento->tipoComprobante->nombre ?? 'Comprobante' }}</span>
                                <span class="text-slate-400 mx-1.5">•</span>
                                <span class="text-xs text-slate-500 font-medium">
                                    {{ \Carbon\Carbon::parse($asiento->fecha)->translatedFormat('d \d\e F \d\e Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Badge de origen polimórfico -->
                            @if($asiento->contabilizable_type)
                                @php
                                    $nombreModelo = class_basename($asiento->contabilizable_type);
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-finora-blue border border-blue-100" title="Generado automáticamente por el módulo comercial">
                                    <span class="material-symbols-outlined text-xs">auto_awesome</span>
                                    Origen: {{ $nombreModelo }} #{{ $asiento->contabilizable_id }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    <span class="material-symbols-outlined text-xs">person</span>
                                    Asiento Manual
                                </span>
                            @endif

                            @if($asiento->estado === 'APROBADO')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                    <span class="material-symbols-outlined text-xs">check_circle</span> Aprobado
                                </span>

                                @if(!$asiento->contabilizable_type)
                                    <!-- Botón de anular solo para manuales o asientos activos -->
                                    <form action="{{ route('contabilidad.asientos.anular', $asiento) }}" method="POST" onsubmit="return confirm('¿Confirmas anular este asiento? Se creará un asiento de reversión.')" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded transition-colors" title="Anular comprobante">
                                            <span class="material-symbols-outlined text-sm">cancel</span>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700">
                                    <span class="material-symbols-outlined text-xs">block</span> Anulado
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Glosa General -->
                    <div class="px-5 py-3 bg-white text-xs text-slate-700 border-b border-slate-100">
                        <span class="font-bold text-slate-900">Glosa:</span> {{ $asiento->glosa }}
                    </div>

                    <!-- Tabla de Partidas Dobles (Debe y Haber) -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead>
                                <tr class="bg-slate-50/50 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                    <th class="py-2 px-5 w-28">Código</th>
                                    <th class="py-2 px-4">Cuenta Contable</th>
                                    <th class="py-2 px-4 text-slate-400">Glosa Específica</th>
                                    <th class="py-2 px-5 text-right w-36">Debe (Bs)</th>
                                    <th class="py-2 px-5 text-right w-36">Haber (Bs)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-sans">
                                @foreach($asiento->detalles as $det)
                                    <tr class="hover:bg-slate-50/30">
                                        <td class="py-2 px-5 font-mono text-slate-500 font-semibold">
                                            {{ $det->cuentaContable->codigo }}
                                        </td>
                                        <td class="py-2 px-4 font-bold text-finora-navy">
                                            {{ $det->cuentaContable->nombre }}
                                        </td>
                                        <td class="py-2 px-4 text-slate-500 text-[11px] truncate max-w-xs">
                                            {{ $det->glosa_detalle ?? '-' }}
                                        </td>
                                        <td class="py-2 px-5 text-right font-mono {{ $det->debe > 0 ? 'font-bold text-slate-900' : 'text-slate-300' }}">
                                            {{ $det->debe > 0 ? number_format($det->debe, 2) : '-' }}
                                        </td>
                                        <td class="py-2 px-5 text-right font-mono {{ $det->haber > 0 ? 'font-bold text-slate-900' : 'text-slate-300' }}">
                                            {{ $det->haber > 0 ? number_format($det->haber, 2) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50/70 border-t border-slate-200 font-mono font-bold text-xs">
                                    <td colspan="3" class="py-2.5 px-5 text-right text-slate-600 font-sans uppercase tracking-wider text-[11px]">
                                        Totales de Comprobante:
                                    </td>
                                    <td class="py-2.5 px-5 text-right text-finora-blue">
                                        Bs {{ number_format($asiento->total_debe, 2) }}
                                    </td>
                                    <td class="py-2.5 px-5 text-right text-finora-blue">
                                        Bs {{ number_format($asiento->total_haber, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">menu_book</span>
                    <p class="text-sm font-semibold text-slate-600">No se encontraron comprobantes con los filtros seleccionados.</p>
                    <p class="text-xs text-slate-400 mt-1">Registra una compra o venta en Finora para ver los asientos generados automáticamente.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $asientos->links() }}
        </div>

    </main>

    @include('partials.panel-footer')
</body>
</html>
