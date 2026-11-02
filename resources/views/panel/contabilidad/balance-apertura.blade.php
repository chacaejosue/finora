<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Balance de Apertura | Módulo Contable</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Balance de Apertura</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="document.getElementById('modalApertura').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-sm transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-sm">edit_note</span>
                    <span>{{ $asientoApertura ? 'Modificar / Nueva Apertura' : 'Registrar Balance de Apertura' }}</span>
                </button>
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
            'titulo' => 'Balance de Apertura e Inicio de Actividades',
            'subtitulo' => 'Estado de Situación Patrimonial inicial bajo Norma Contable N.° 1 y legislación comercial boliviana.',
            'accionBoton' => '<button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer"><span class="material-symbols-outlined text-sm">print</span>Imprimir Reporte</button>'
        ])

        @if($asientoApertura)
            <!-- REPORTE FORMAL DE BALANCE DE APERTURA -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 sm:p-12 mb-8 max-w-4xl mx-auto printable-card">
                <!-- Encabezado Oficial -->
                <div class="border-b border-slate-200 pb-6 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">FINORA S.R.L.</div>
                        <div class="text-xs text-slate-500">NIT: 10283749021 • Santa Cruz de la Sierra, Bolivia</div>
                        <h2 class="text-2xl font-heading font-extrabold text-finora-navy mt-2">
                            BALANCE DE APERTURA
                        </h2>
                        <div class="text-xs font-semibold text-slate-500">
                            Al {{ \Carbon\Carbon::parse($asientoApertura->fecha)->translatedFormat('d \d\e F \d\e Y') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold bg-purple-100 text-purple-800">
                            Comprobante N° {{ $asientoApertura->numero }}
                        </span>
                        <div class="text-[11px] font-semibold text-slate-400 mt-1">Expresado en Bolivianos (Bs)</div>
                    </div>
                </div>

                @php
                    $detalles = $asientoApertura->detalles;
                    $activos = $detalles->filter(fn($d) => $d->cuentaContable->tipo === 'ACTIVO');
                    $pasivos = $detalles->filter(fn($d) => $d->cuentaContable->tipo === 'PASIVO');
                    $patrimonio = $detalles->filter(fn($d) => $d->cuentaContable->tipo === 'PATRIMONIO');

                    $totalActivos = $activos->sum('debe');
                    $totalPasivos = $pasivos->sum('haber');
                    $totalPatrimonio = $patrimonio->sum('haber');
                    $totalPasivoPatrimonio = $totalPasivos + $totalPatrimonio;
                @endphp

                <!-- Cuerpo del Balance: Dos Columnas o Secciones -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Columna Izquierda: ACTIVO -->
                    <div>
                        <div class="border-b-2 border-finora-blue pb-1 mb-4 flex justify-between items-center">
                            <span class="font-heading font-extrabold text-sm text-finora-blue uppercase tracking-wide">1. ACTIVO</span>
                            <span class="text-xs font-bold text-slate-400">Monto (Bs)</span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Activo Corriente</div>
                            @forelse($activos->filter(fn($a) => str_starts_with($a->cuentaContable->codigo, '1.1')) as $act)
                                <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-slate-100">
                                    <span class="text-slate-700">{{ $act->cuentaContable->codigo }} {{ $act->cuentaContable->nombre }}</span>
                                    <span class="font-mono font-semibold text-finora-navy">{{ number_format($act->debe, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 italic">Sin cuentas corrientes iniciales</div>
                            @endforelse
                        </div>

                        <div class="space-y-2 mb-6">
                            <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">Activo No Corriente</div>
                            @forelse($activos->filter(fn($a) => str_starts_with($a->cuentaContable->codigo, '1.2')) as $act)
                                <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-slate-100">
                                    <span class="text-slate-700">{{ $act->cuentaContable->codigo }} {{ $act->cuentaContable->nombre }}</span>
                                    <span class="font-mono font-semibold text-finora-navy">{{ number_format($act->debe, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 italic">Sin bienes de uso iniciales</div>
                            @endforelse
                        </div>

                        <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center font-extrabold text-xs text-finora-blue">
                            <span>TOTAL ACTIVO</span>
                            <span class="font-mono text-sm">Bs {{ number_format($totalActivos, 2) }}</span>
                        </div>
                    </div>

                    <!-- Columna Derecha: PASIVO Y PATRIMONIO -->
                    <div>
                        <!-- Pasivo -->
                        <div class="border-b-2 border-rose-500 pb-1 mb-4 flex justify-between items-center">
                            <span class="font-heading font-extrabold text-sm text-rose-600 uppercase tracking-wide">2. PASIVO</span>
                            <span class="text-xs font-bold text-slate-400">Monto (Bs)</span>
                        </div>

                        <div class="space-y-2 mb-6">
                            @forelse($pasivos as $pas)
                                <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-slate-100">
                                    <span class="text-slate-700">{{ $pas->cuentaContable->codigo }} {{ $pas->cuentaContable->nombre }}</span>
                                    <span class="font-mono font-semibold text-finora-navy">{{ number_format($pas->haber, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 italic">Sin obligaciones con terceros (Deuda cero)</div>
                            @endforelse

                            <div class="flex justify-between items-center font-bold text-xs text-slate-700 pt-2 border-t border-slate-200">
                                <span>TOTAL PASIVO</span>
                                <span class="font-mono">Bs {{ number_format($totalPasivos, 2) }}</span>
                            </div>
                        </div>

                        <!-- Patrimonio -->
                        <div class="border-b-2 border-emerald-500 pb-1 mb-4 flex justify-between items-center">
                            <span class="font-heading font-extrabold text-sm text-emerald-600 uppercase tracking-wide">3. PATRIMONIO</span>
                            <span class="text-xs font-bold text-slate-400">Monto (Bs)</span>
                        </div>

                        <div class="space-y-2 mb-6">
                            @forelse($patrimonio as $pat)
                                <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-slate-100">
                                    <span class="text-slate-700 font-medium">{{ $pat->cuentaContable->codigo }} {{ $pat->cuentaContable->nombre }}</span>
                                    <span class="font-mono font-semibold text-finora-navy">{{ number_format($pat->haber, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 italic">Sin capital social registrado</div>
                            @endforelse

                            <div class="flex justify-between items-center font-bold text-xs text-slate-700 pt-2 border-t border-slate-200">
                                <span>TOTAL PATRIMONIO</span>
                                <span class="font-mono">Bs {{ number_format($totalPatrimonio, 2) }}</span>
                            </div>
                        </div>

                        <div class="bg-emerald-50/70 p-3 rounded-xl flex justify-between items-center font-extrabold text-xs text-emerald-800">
                            <span>TOTAL PASIVO Y PATRIMONIO</span>
                            <span class="font-mono text-sm">Bs {{ number_format($totalPasivoPatrimonio, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Glosa Oficial -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 mb-10 text-xs text-slate-600">
                    <span class="font-bold text-slate-700">Glosa General:</span> {{ $asientoApertura->glosa }}
                </div>

                <!-- Firmas Legales -->
                <div class="pt-12 border-t border-slate-200 grid grid-cols-2 gap-8 text-center">
                    <div>
                        <div class="border-b border-slate-400 w-48 mx-auto mb-2"></div>
                        <div class="text-xs font-bold text-finora-navy">Lic. Carlos Morales</div>
                        <div class="text-[10px] text-slate-400">Contador General (Mat. CAUB 4821)</div>
                    </div>
                    <div>
                        <div class="border-b border-slate-400 w-48 mx-auto mb-2"></div>
                        <div class="text-xs font-bold text-finora-navy">Ing. Roberto Suárez</div>
                        <div class="text-[10px] text-slate-400">Representante Legal • FINORA</div>
                    </div>
                </div>
            </div>
        @else
            <!-- Estado Inicial Sin Apertura -->
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-2xl mx-auto shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">flag</span>
                </div>
                <h2 class="text-xl font-heading font-extrabold text-finora-navy">No se ha registrado un Balance de Apertura</h2>
                <p class="text-xs text-slate-500 mt-2 max-w-md mx-auto">
                    El Balance de Apertura es el punto de partida legal del ciclo contable comercial en Bolivia. Permite declarar los aportes iniciales de caja, inventario, muebles o pasivos con su contrapartida en Capital Social.
                </p>
                <div class="mt-6">
                    <button type="button" onclick="document.getElementById('modalApertura').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-md transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-sm">edit_calendar</span>
                        <span>Registrar Balance de Apertura Inicial</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- MODAL FORMULARIO DE REGISTRO / MODIFICACIÓN DE BALANCE DE APERTURA -->
        <div id="modalApertura" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-lg">flag</span>
                        </div>
                        <div>
                            <h3 class="font-heading font-extrabold text-base text-finora-navy">Formulario de Balance de Apertura</h3>
                            <p class="text-[11px] text-slate-500">El Capital Social se computa automáticamente como: Total Activos - Total Pasivos</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('modalApertura').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('contabilidad.balance-apertura.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Fecha de Inicio de Actividades</label>
                            <input type="date" name="fecha" value="{{ now()->startOfYear()->toDateString() }}" required class="w-full text-xs font-medium px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-finora-blue/20 focus:border-finora-blue"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Glosa del Comprobante</label>
                            <input type="text" name="glosa" value="Balance de Apertura e Inicio de Actividades Comerciales" required class="w-full text-xs font-medium px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-finora-blue/20 focus:border-finora-blue"/>
                        </div>
                    </div>

                    <!-- Saldos de Activos -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-finora-blue">1. Cuentas de Activo (Aportes Iniciales)</span>
                            <span class="text-[11px] text-slate-400">Ingresa monto en Bs</span>
                        </div>
                        <div class="space-y-2 border border-slate-200 rounded-2xl p-4 max-h-56 overflow-y-auto">
                            @foreach($cuentasActivo as $cta)
                                <div class="flex items-center justify-between gap-3 text-xs">
                                    <label class="text-slate-700 flex-1 truncate">
                                        <span class="font-mono font-bold text-slate-500">{{ $cta->codigo }}</span> {{ $cta->nombre }}
                                    </label>
                                    <input type="hidden" name="partidas[{{ $loop->index }}][cuenta_contable_id]" value="{{ $cta->id }}"/>
                                    <div class="flex items-center gap-1 w-36">
                                        <span class="text-[11px] text-slate-400">Bs</span>
                                        <input type="number" step="0.01" min="0" name="partidas[{{ $loop->index }}][monto]" value="0.00" oninput="calcularCapitalSocial()" class="input-activo w-full text-xs text-right font-mono px-2 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Saldos de Pasivos -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-rose-600">2. Cuentas de Pasivo (Obligaciones Iniciales)</span>
                            <span class="text-[11px] text-slate-400">Opcional (Deudas)</span>
                        </div>
                        <div class="space-y-2 border border-slate-200 rounded-2xl p-4 max-h-48 overflow-y-auto">
                            @php $offset = count($cuentasActivo); @endphp
                            @foreach($cuentasPasivo as $cta)
                                <div class="flex items-center justify-between gap-3 text-xs">
                                    <label class="text-slate-700 flex-1 truncate">
                                        <span class="font-mono font-bold text-slate-500">{{ $cta->codigo }}</span> {{ $cta->nombre }}
                                    </label>
                                    <input type="hidden" name="partidas[{{ $offset + $loop->index }}][cuenta_contable_id]" value="{{ $cta->id }}"/>
                                    <div class="flex items-center gap-1 w-36">
                                        <span class="text-[11px] text-slate-400">Bs</span>
                                        <input type="number" step="0.01" min="0" name="partidas[{{ $offset + $loop->index }}][monto]" value="0.00" oninput="calcularCapitalSocial()" class="input-pasivo w-full text-xs text-right font-mono px-2 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-rose-500"/>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Resumen Matemático y Capital Social en Vivo -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs space-y-1 w-full sm:w-auto">
                            <div>Total Activos: <span id="lblTotalActivos" class="font-mono font-bold text-finora-blue">Bs 0.00</span></div>
                            <div>Total Pasivos: <span id="lblTotalPasivos" class="font-mono font-bold text-rose-600">Bs 0.00</span></div>
                        </div>
                        <div class="text-right w-full sm:w-auto bg-white px-4 py-2 rounded-xl border border-slate-200">
                            <div class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400">Capital Social Computado (3.1.1.01)</div>
                            <div id="lblCapitalSocial" class="text-lg font-heading font-extrabold text-emerald-700">Bs 0.00</div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('modalApertura').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-md transition-all cursor-pointer">
                            Guardar y Asentar Comprobante de Apertura
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    @include('partials.panel-footer')

    <script>
        function calcularCapitalSocial() {
            let totalActivos = 0;
            document.querySelectorAll('.input-activo').forEach(input => {
                totalActivos += parseFloat(input.value) || 0;
            });

            let totalPasivos = 0;
            document.querySelectorAll('.input-pasivo').forEach(input => {
                totalPasivos += parseFloat(input.value) || 0;
            });

            let capitalSocial = Math.max(0, totalActivos - totalPasivos);

            document.getElementById('lblTotalActivos').innerText = 'Bs ' + totalActivos.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('lblTotalPasivos').innerText = 'Bs ' + totalPasivos.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('lblCapitalSocial').innerText = 'Bs ' + capitalSocial.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    </script>
</body>
</html>
