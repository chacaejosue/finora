<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Plan de Cuentas y Modelos | Módulo Contable</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Plan de Cuentas y Plantillas</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('contabilidad.asientos.crear') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-sm transition-all">
                    <span class="material-symbols-outlined text-sm">add_circle</span>
                    <span>Nuevo Asiento</span>
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('panel.contabilidad.header-nav', [
            'titulo' => 'Plan de Cuentas y Plantillas de Automatización',
            'subtitulo' => 'Estructura contable formal de 82 cuentas clasificadas y reglas porcentuales dinámicas bajo Ley 843.',
            'accionBoton' => '<button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer"><span class="material-symbols-outlined text-sm">print</span>Imprimir</button>'
        ])

        <!-- Pestañas -->
        <div class="flex items-center gap-2 mb-6 border-b border-slate-200 pb-2">
            <button type="button" id="btnTabPlan" onclick="switchTab('plan')" class="px-4 py-2 rounded-xl text-xs font-extrabold bg-finora-blue text-white shadow-sm transition-all cursor-pointer">
                1. Plan de Cuentas Oficial (82 Cuentas)
            </button>
            <button type="button" id="btnTabModelos" onclick="switchTab('modelos')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-all cursor-pointer">
                2. Plantillas de Contabilización (Modelos Automáticos)
            </button>
        </div>

        <!-- 1. PLAN DE CUENTAS (TABLA JERÁRQUICA) -->
        <div id="seccionPlan" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden printable-card">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-finora-navy">Nomenclatura y Catálogo de Cuentas</h3>
                        <p class="text-[11px] text-slate-500">Solo las cuentas de último nivel marcadas como "Imputable" reciben movimientos directos.</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            {{ $todasCuentas->where('permite_movimiento', true)->count() }} Imputables
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                            {{ $todasCuentas->where('permite_movimiento', false)->count() }} Grupos / Rubros
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-2.5 px-4 w-32">Código</th>
                                <th class="py-2.5 px-4">Nombre de la Cuenta</th>
                                <th class="py-2.5 px-4 w-28">Nivel</th>
                                <th class="py-2.5 px-4 w-32">Tipo</th>
                                <th class="py-2.5 px-4 w-32">Naturaleza</th>
                                <th class="py-2.5 px-4 w-28 text-center">Imputable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-sans">
                            @foreach($todasCuentas as $cta)
                                @php
                                    $indent = match($cta->nivel) {
                                        1 => 'pl-2 font-extrabold text-slate-900 bg-slate-50/70',
                                        2 => 'pl-6 font-bold text-slate-800',
                                        3 => 'pl-10 font-semibold text-slate-700',
                                        4 => 'pl-14 text-slate-700',
                                        default => 'pl-16 text-slate-600',
                                    };
                                @endphp
                                <tr class="hover:bg-blue-50/40 transition-colors {{ $cta->nivel === 1 ? 'border-t-2 border-slate-200' : '' }}">
                                    <td class="py-2 px-4 font-mono font-bold text-slate-600">
                                        {{ $cta->codigo }}
                                    </td>
                                    <td class="py-2 px-4 {{ $indent }}">
                                        {{ $cta->nombre }}
                                    </td>
                                    <td class="py-2 px-4 text-slate-500 font-medium">
                                        Nivel {{ $cta->nivel }}
                                    </td>
                                    <td class="py-2 px-4">
                                        @php
                                            $tipoBadge = match($cta->tipo) {
                                                'ACTIVO' => 'bg-blue-100 text-blue-800',
                                                'PASIVO' => 'bg-rose-100 text-rose-800',
                                                'PATRIMONIO' => 'bg-emerald-100 text-emerald-800',
                                                'INGRESO' => 'bg-teal-100 text-teal-800',
                                                'COSTO' => 'bg-purple-100 text-purple-800',
                                                'GASTO' => 'bg-amber-100 text-amber-800',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $tipoBadge }}">
                                            {{ $cta->tipo }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4">
                                        <span class="text-[11px] font-semibold {{ $cta->naturaleza === 'DEUDORA' ? 'text-blue-700' : 'text-emerald-700' }}">
                                            {{ $cta->naturaleza }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        @if($cta->permite_movimiento)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                                <span class="material-symbols-outlined text-xs">check</span> Sí
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-300 font-medium">Grupo</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. MODELOS CONTABLES PARAMETRIZADOS -->
        <div id="seccionModelos" class="space-y-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($modelosContables as $modelo)
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
                        <div>
                            <!-- Cabecera de la plantilla -->
                            <div class="p-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-extrabold bg-blue-100 text-finora-blue">
                                            {{ $modelo->codigo }}
                                        </span>
                                        <span class="text-[10px] font-bold uppercase text-slate-400">
                                            {{ $modelo->tipoComprobante->nombre ?? 'Comprobante' }}
                                        </span>
                                    </div>
                                    <h3 class="font-heading font-extrabold text-sm text-finora-navy mt-1">
                                        {{ $modelo->nombre }}
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-finora-blue">rule</span>
                            </div>

                            <div class="p-4 text-xs text-slate-600 border-b border-slate-100 bg-white">
                                {{ $modelo->descripcion }}
                            </div>

                            <!-- Reglas de partidas porcentuales -->
                            <div class="p-4">
                                <table class="w-full text-xs text-left">
                                    <thead>
                                        <tr class="text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                            <th class="pb-2">Lado</th>
                                            <th class="pb-2">Cuenta Contable</th>
                                            <th class="pb-2 text-right">Porcentaje</th>
                                            <th class="pb-2 text-right">Base</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-sans">
                                        @foreach($modelo->detalles as $det)
                                            <tr>
                                                <td class="py-2">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $det->lado === 'DEBE' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                                        {{ $det->lado }}
                                                    </span>
                                                </td>
                                                <td class="py-2 text-slate-700">
                                                    <span class="font-mono text-slate-500 font-semibold">{{ $det->cuentaContable->codigo }}</span>
                                                    <div class="text-[11px] font-medium truncate max-w-xs">{{ $det->cuentaContable->nombre }}</div>
                                                </td>
                                                <td class="py-2 text-right font-mono font-bold text-finora-navy">
                                                    {{ number_format($det->porcentaje, 0) }}%
                                                </td>
                                                <td class="py-2 text-right text-[10px] font-mono text-slate-400">
                                                    {{ $det->tipo_monto }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 text-[10px] font-semibold text-slate-500 flex items-center justify-between">
                            <span>Normativa: Ley N.° 843 Bolivia</span>
                            <span class="text-emerald-700 font-bold">Activo en tiempo real</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </main>

    @include('partials.panel-footer')

    <script>
        function switchTab(tab) {
            const secPlan = document.getElementById('seccionPlan');
            const secModelos = document.getElementById('seccionModelos');
            const btnPlan = document.getElementById('btnTabPlan');
            const btnModelos = document.getElementById('btnTabModelos');

            if (tab === 'plan') {
                secPlan.classList.remove('hidden');
                secModelos.classList.add('hidden');
                btnPlan.className = 'px-4 py-2 rounded-xl text-xs font-extrabold bg-finora-blue text-white shadow-sm transition-all cursor-pointer';
                btnModelos.className = 'px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-all cursor-pointer';
            } else {
                secPlan.classList.add('hidden');
                secModelos.classList.remove('hidden');
                btnModelos.className = 'px-4 py-2 rounded-xl text-xs font-extrabold bg-finora-blue text-white shadow-sm transition-all cursor-pointer';
                btnPlan.className = 'px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-all cursor-pointer';
            }
        }
    </script>
</body>
</html>
