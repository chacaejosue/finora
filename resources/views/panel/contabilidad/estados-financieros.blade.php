<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Estados Financieros | Módulo Contable</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Estados Financieros</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-sm">print</span>
                    <span>Imprimir Estados</span>
                </button>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @include('panel.contabilidad.header-nav', [
            'titulo' => 'Estados Financieros Básicos (Norma Contable N.° 1)',
            'subtitulo' => 'Estado de Resultados de gestión con provisión IUE (25%) y Balance General con integración patrimonial dinámica.',
            'accionBoton' => '<button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all cursor-pointer"><span class="material-symbols-outlined text-sm">print</span>Imprimir</button>'
        ])

        <!-- Selector de Periodo de Evaluación -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 mb-8 shadow-xs">
            <form method="GET" action="{{ route('contabilidad.estados-financieros') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Periodo Desde</label>
                    <input type="date" name="fecha_desde" value="{{ $fechaDesde }}" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Periodo Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}" class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition-colors h-9">
                    Actualizar Estados
                </button>
            </form>
        </div>

        <!-- Pestañas de Selección entre Estado de Resultados y Balance General -->
        <div class="flex items-center gap-2 mb-6 border-b border-slate-200 pb-2">
            <button type="button" id="tabBtnResultados" onclick="mostrarPestana('resultados')" class="px-4 py-2 rounded-xl text-xs font-extrabold bg-finora-blue text-white shadow-sm transition-all cursor-pointer">
                1. Estado de Resultados
            </button>
            <button type="button" id="tabBtnBalance" onclick="mostrarPestana('balance')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-all cursor-pointer">
                2. Balance General
            </button>
        </div>

        <!-- 1. ESTADO DE RESULTADOS -->
        <div id="seccionResultados" class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 sm:p-12 max-w-4xl mx-auto printable-card">
                <!-- Encabezado Oficial -->
                <div class="border-b border-slate-200 pb-6 mb-8 text-center">
                    <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">FINORA S.R.L.</div>
                    <div class="text-xs text-slate-500">Santa Cruz de la Sierra, Bolivia</div>
                    <h2 class="text-2xl font-heading font-extrabold text-finora-navy mt-1">
                        ESTADO DE RESULTADOS
                    </h2>
                    <div class="text-xs font-semibold text-slate-500 mt-0.5">
                        Por el periodo comprendido entre el {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} y el {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                    </div>
                    <div class="text-[11px] text-slate-400 italic mt-1">(Expresado en Bolivianos)</div>
                </div>

                <!-- Partidas del Estado de Resultados -->
                <div class="space-y-4 text-xs font-sans">
                    <!-- Ingresos Operativos -->
                    <div class="border-b border-slate-100 pb-2">
                        <div class="flex justify-between items-center font-bold text-slate-800 mb-1">
                            <span>(+) INGRESOS OPERATIVOS</span>
                            <span class="font-mono text-slate-900">Bs {{ number_format($totalIngresosOperativos, 2) }}</span>
                        </div>
                        <div class="pl-4 space-y-1 text-slate-600">
                            <div class="flex justify-between items-center">
                                <span>Ventas de Mercaderías (87% neto factura)</span>
                                <span class="font-mono">{{ number_format($ventasNetas, 2) }}</span>
                            </div>
                            @if($otrosIngresos > 0)
                                <div class="flex justify-between items-center">
                                    <span>Otros Ingresos / Ajustes de Almacén</span>
                                    <span class="font-mono">{{ number_format($otrosIngresos, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Costo de Ventas -->
                    <div class="border-b border-slate-100 pb-2">
                        <div class="flex justify-between items-center font-bold text-slate-800 mb-1">
                            <span>(-) COSTO DE VENTAS (CPP)</span>
                            <span class="font-mono text-rose-600">(Bs {{ number_format($costoVentas, 2) }})</span>
                        </div>
                        <div class="pl-4 text-slate-600 flex justify-between items-center">
                            <span>Costo de Mercaderías Vendidas valuado al Costo Promedio Ponderado</span>
                            <span class="font-mono">{{ number_format($costoVentas, 2) }}</span>
                        </div>
                    </div>

                    <!-- Utilidad Bruta -->
                    <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center font-bold text-finora-blue">
                        <span class="uppercase tracking-wider">(=) UTILIDAD BRUTA EN VENTAS</span>
                        <span class="font-mono text-sm">Bs {{ number_format($utilidadBruta, 2) }}</span>
                    </div>

                    <!-- Gastos Operativos -->
                    <div class="border-b border-slate-100 pb-2 pt-2">
                        <div class="flex justify-between items-center font-bold text-slate-800 mb-2">
                            <span>(-) GASTOS DE ADMINISTRACIÓN Y OPERACIÓN</span>
                            <span class="font-mono text-rose-600">(Bs {{ number_format($totalGastosOperativos, 2) }})</span>
                        </div>
                        <div class="pl-4 space-y-1.5 text-slate-600">
                            <div class="flex justify-between items-center">
                                <span>Impuesto a las Transacciones (3% IT s/ ventas facturadas)</span>
                                <span class="font-mono">{{ number_format($itGasto, 2) }}</span>
                            </div>
                            @if($sueldosGasto > 0)
                                <div class="flex justify-between items-center">
                                    <span>Sueldos y Salarios</span>
                                    <span class="font-mono">{{ number_format($sueldosGasto, 2) }}</span>
                                </div>
                            @endif
                            @if($serviciosGasto > 0)
                                <div class="flex justify-between items-center">
                                    <span>Servicios Básicos (Agua, Energía Eléctrica, Teléfono)</span>
                                    <span class="font-mono">{{ number_format($serviciosGasto, 2) }}</span>
                                </div>
                            @endif
                            @if($alquileresGasto > 0)
                                <div class="flex justify-between items-center">
                                    <span>Alquileres Pagados</span>
                                    <span class="font-mono">{{ number_format($alquileresGasto, 2) }}</span>
                                </div>
                            @endif
                            @if($mermasGasto > 0)
                                <div class="flex justify-between items-center">
                                    <span>Pérdida por Mermas y Ajustes de Inventario</span>
                                    <span class="font-mono">{{ number_format($mermasGasto, 2) }}</span>
                                </div>
                            @endif
                            @if($otrosGastos > 0)
                                <div class="flex justify-between items-center">
                                    <span>Otros Gastos Operativos</span>
                                    <span class="font-mono">{{ number_format($otrosGastos, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Utilidad Antes de Impuestos -->
                    <div class="p-3 rounded-xl border border-slate-200 flex justify-between items-center font-bold text-slate-800">
                        <span>(=) UTILIDAD NETA ANTES DE IMPUESTOS</span>
                        <span class="font-mono text-sm {{ $utilidadNetaAntesImpuestos >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                            Bs {{ number_format($utilidadNetaAntesImpuestos, 2) }}
                        </span>
                    </div>

                    <!-- Provisión IUE (25%) según Ley 843 -->
                    @if($utilidadNetaAntesImpuestos > 0)
                        <div class="flex justify-between items-center text-slate-600 pl-4 py-1">
                            <span>(-) Provisión Impuesto sobre las Utilidades de las Empresas (IUE 25% Ley 843)</span>
                            <span class="font-mono text-rose-600">(Bs {{ number_format($iueCalculado, 2) }})</span>
                        </div>

                        <!-- Utilidad Líquida -->
                        <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200 flex justify-between items-center font-extrabold text-emerald-900">
                            <span class="uppercase tracking-wider">(=) UTILIDAD LÍQUIDA DE LA GESTIÓN</span>
                            <span class="font-mono text-base">Bs {{ number_format($utilidadLiquidaGestion, 2) }}</span>
                        </div>
                    @else
                        <div class="bg-rose-50 p-4 rounded-2xl border border-rose-200 flex justify-between items-center font-extrabold text-rose-900">
                            <span class="uppercase tracking-wider">(=) PÉRDIDA NETA DEL EJERCICIO</span>
                            <span class="font-mono text-base">Bs {{ number_format($utilidadNetaAntesImpuestos, 2) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Firmas -->
                <div class="pt-12 border-t border-slate-200 grid grid-cols-2 gap-8 text-center mt-12">
                    <div>
                        <div class="border-b border-slate-400 w-44 mx-auto mb-2"></div>
                        <div class="text-xs font-bold text-finora-navy">Contador General</div>
                        <div class="text-[10px] text-slate-400">Mat. Profesional Bolivia</div>
                    </div>
                    <div>
                        <div class="border-b border-slate-400 w-44 mx-auto mb-2"></div>
                        <div class="text-xs font-bold text-finora-navy">Gerente General</div>
                        <div class="text-[10px] text-slate-400">FINORA S.R.L.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. BALANCE GENERAL -->
        <div id="seccionBalance" class="space-y-6 hidden">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 sm:p-12 max-w-4xl mx-auto printable-card">
                <!-- Encabezado Oficial -->
                <div class="border-b border-slate-200 pb-6 mb-8 text-center">
                    <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">FINORA S.R.L.</div>
                    <div class="text-xs text-slate-500">Santa Cruz de la Sierra, Bolivia</div>
                    <h2 class="text-2xl font-heading font-extrabold text-finora-navy mt-1">
                        BALANCE GENERAL
                    </h2>
                    <div class="text-xs font-semibold text-slate-500 mt-0.5">
                        Al {{ \Carbon\Carbon::parse($fechaHasta)->translatedFormat('d \d\e F \d\e Y') }}
                    </div>
                    <div class="text-[11px] text-slate-400 italic mt-1">(Expresado en Bolivianos)</div>
                </div>

                <!-- Certificación de Ecuación Patrimonial -->
                <div class="mb-6 p-3 rounded-2xl border {{ $ecuacionCuadrada ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }} flex items-center justify-between text-xs font-bold">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">{{ $ecuacionCuadrada ? 'verified' : 'error' }}</span>
                        <span>Ecuación Fundamental: ACTIVO = PASIVO + PATRIMONIO</span>
                    </div>
                    <div class="font-mono">
                        {{ $ecuacionCuadrada ? 'CUADRADO EXACTO' : 'DIFERENCIA DETECTADA' }}
                    </div>
                </div>

                <!-- Estructura de 2 Columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 text-xs">
                    <!-- ACTIVO -->
                    <div>
                        <div class="border-b-2 border-finora-blue pb-1 mb-4 flex justify-between items-center">
                            <span class="font-heading font-extrabold text-sm text-finora-blue uppercase tracking-wide">1. ACTIVO</span>
                            <span class="text-xs font-bold text-slate-400">Bs</span>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Activo Corriente</div>
                            <div class="pl-3 space-y-1.5 text-slate-600">
                                <div class="flex justify-between items-center">
                                    <span>Disponible (Caja / Bancos M/N)</span>
                                    <span class="font-mono text-slate-900">{{ number_format($disponible, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Exigible (CxC Clientes + Crédito Fiscal IVA)</span>
                                    <span class="font-mono text-slate-900">{{ number_format($exigible, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Realizable (Inventario al CPP)</span>
                                    <span class="font-mono text-slate-900">{{ number_format($realizable, 2) }}</span>
                                </div>
                                @if($anticipados > 0)
                                    <div class="flex justify-between items-center">
                                        <span>Pagos Anticipados</span>
                                        <span class="font-mono text-slate-900">{{ number_format($anticipados, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-between items-center font-bold text-slate-800 pt-1 border-t border-slate-100">
                                <span>Total Activo Corriente</span>
                                <span class="font-mono">Bs {{ number_format($totalActivoCorriente, 2) }}</span>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Activo No Corriente</div>
                            <div class="pl-3 space-y-1.5 text-slate-600">
                                <div class="flex justify-between items-center">
                                    <span>Bienes de Uso (Activos Fijos)</span>
                                    <span class="font-mono text-slate-900">{{ number_format($bienesUso, 2) }}</span>
                                </div>
                                @if($deprecAcum > 0)
                                    <div class="flex justify-between items-center">
                                        <span>(-) Depreciación Acumulada</span>
                                        <span class="font-mono text-rose-600">({{ number_format($deprecAcum, 2) }})</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-between items-center font-bold text-slate-800 pt-1 border-t border-slate-100">
                                <span>Total Activo No Corriente</span>
                                <span class="font-mono">Bs {{ number_format($totalActivoNoCorriente, 2) }}</span>
                            </div>
                        </div>

                        <div class="bg-blue-50/70 p-3.5 rounded-xl flex justify-between items-center font-extrabold text-finora-blue text-xs">
                            <span class="uppercase tracking-wider">TOTAL ACTIVO</span>
                            <span class="font-mono text-sm">Bs {{ number_format($totalActivos, 2) }}</span>
                        </div>
                    </div>

                    <!-- PASIVO Y PATRIMONIO -->
                    <div>
                        <!-- Pasivo -->
                        <div class="border-b-2 border-rose-500 pb-1 mb-4 flex justify-between items-center">
                            <span class="font-heading font-extrabold text-sm text-rose-600 uppercase tracking-wide">2. PASIVO</span>
                            <span class="text-xs font-bold text-slate-400">Bs</span>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Pasivo Corriente</div>
                            <div class="pl-3 space-y-1.5 text-slate-600">
                                <div class="flex justify-between items-center">
                                    <span>Cuentas por Pagar Comerciales (Proveedores)</span>
                                    <span class="font-mono text-slate-900">{{ number_format($cxpProveedores, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Obligaciones Fiscales (Débito Fiscal IVA + IT)</span>
                                    <span class="font-mono text-slate-900">{{ number_format($fiscales, 2) }}</span>
                                </div>
                                @if($laborales > 0)
                                    <div class="flex justify-between items-center">
                                        <span>Obligaciones Laborales</span>
                                        <span class="font-mono text-slate-900">{{ number_format($laborales, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-between items-center font-bold text-slate-800 pt-1 border-t border-slate-100">
                                <span>Total Pasivo Corriente</span>
                                <span class="font-mono">Bs {{ number_format($totalPasivoCorriente, 2) }}</span>
                            </div>
                        </div>

                        @if($totalPasivoNoCorriente > 0)
                            <div class="space-y-3 mb-6">
                                <div class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Pasivo No Corriente</div>
                                <div class="pl-3 text-slate-600 flex justify-between items-center">
                                    <span>Préstamos Bancarios LP</span>
                                    <span class="font-mono">{{ number_format($prestamosLp, 2) }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center font-bold text-slate-800 pb-4 mb-4 border-b border-slate-200">
                            <span>TOTAL PASIVO</span>
                            <span class="font-mono">Bs {{ number_format($totalPasivos, 2) }}</span>
                        </div>

                        <!-- Patrimonio -->
                        <div class="border-b-2 border-emerald-500 pb-1 mb-4 flex justify-between items-center">
                            <span class="font-heading font-extrabold text-sm text-emerald-600 uppercase tracking-wide">3. PATRIMONIO</span>
                            <span class="text-xs font-bold text-slate-400">Bs</span>
                        </div>

                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Capital Social</span>
                                <span class="font-mono text-slate-900">{{ number_format($capitalSocial, 2) }}</span>
                            </div>
                            @if($reservas > 0)
                                <div class="flex justify-between items-center text-slate-600">
                                    <span>Reserva Legal</span>
                                    <span class="font-mono text-slate-900">{{ number_format($reservas, 2) }}</span>
                                </div>
                            @endif
                            @if($resultadosAcum != 0)
                                <div class="flex justify-between items-center text-slate-600">
                                    <span>Resultados Acumulados</span>
                                    <span class="font-mono text-slate-900">{{ number_format($resultadosAcum, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-emerald-800 font-bold bg-emerald-50/60 p-2 rounded-lg">
                                <span>Resultado del Ejercicio Actual</span>
                                <span class="font-mono">Bs {{ number_format($resultadoDelEjercicio, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center font-bold text-slate-800 pt-1 border-t border-slate-100">
                                <span>Total Patrimonio</span>
                                <span class="font-mono">Bs {{ number_format($totalPatrimonio, 2) }}</span>
                            </div>
                        </div>

                        <div class="bg-emerald-50/70 p-3.5 rounded-xl flex justify-between items-center font-extrabold text-emerald-900 text-xs">
                            <span class="uppercase tracking-wider">TOTAL PASIVO Y PATRIMONIO</span>
                            <span class="font-mono text-sm">Bs {{ number_format($totalPasivoYPatrimonio, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Firmas -->
                <div class="pt-12 border-t border-slate-200 grid grid-cols-2 gap-8 text-center mt-12">
                    <div>
                        <div class="border-b border-slate-400 w-44 mx-auto mb-2"></div>
                        <div class="text-xs font-bold text-finora-navy">Lic. Contador General</div>
                        <div class="text-[10px] text-slate-400">Auditor Financiero</div>
                    </div>
                    <div>
                        <div class="border-b border-slate-400 w-44 mx-auto mb-2"></div>
                        <div class="text-xs font-bold text-finora-navy">Gerente General</div>
                        <div class="text-[10px] text-slate-400">FINORA S.R.L.</div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    @include('partials.panel-footer')

    <script>
        function mostrarPestana(tipo) {
            const secResultados = document.getElementById('seccionResultados');
            const secBalance = document.getElementById('seccionBalance');
            const btnResultados = document.getElementById('tabBtnResultados');
            const btnBalance = document.getElementById('tabBtnBalance');

            if (tipo === 'resultados') {
                secResultados.classList.remove('hidden');
                secBalance.classList.add('hidden');
                btnResultados.className = 'px-4 py-2 rounded-xl text-xs font-extrabold bg-finora-blue text-white shadow-sm transition-all cursor-pointer';
                btnBalance.className = 'px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-all cursor-pointer';
            } else {
                secResultados.classList.add('hidden');
                secBalance.classList.remove('hidden');
                btnBalance.className = 'px-4 py-2 rounded-xl text-xs font-extrabold bg-finora-blue text-white shadow-sm transition-all cursor-pointer';
                btnResultados.className = 'px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-all cursor-pointer';
            }
        }
    </script>
</body>
</html>
