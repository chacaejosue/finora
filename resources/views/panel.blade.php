<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Panel Principal</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>

    <!-- Tipografías de Google -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

    <!-- Iconos Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>

    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-[#00c1de] selection:text-white flex flex-col min-h-screen">

    <!-- Barra de navegación superior minimalista (Estilo Landing) -->
    <header class="bg-white/85 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-2">
            <!-- Marca Finora -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <a class="inline-flex items-center gap-2.5 sm:gap-3 group min-w-0" href="{{ route('panel.index') }}">
                    <div class="relative w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center shrink-0">
                        <img
                            src="{{ asset('images/branding/finora-isotipo.png') }}"
                            alt="Finora"
                            class="h-8 sm:h-9 w-auto object-contain group-hover:scale-105 transition-transform duration-300"
                        />
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-heading text-lg sm:text-xl font-extrabold tracking-tight text-[#0e1b2e] truncate">Finora</span>
                        <span class="text-[10px] font-semibold text-slate-400 -mt-1 tracking-wide hidden sm:block">
                            {{ $usuario->rol === 'CONSULTOR' ? 'Panel del Consultor' : 'Panel del Colaborador' }}
                        </span>
                    </div>
                </a>
            </div>

            <!-- Acciones de usuario -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <a href="{{ route('landing') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold text-slate-600 hover:text-[#00c1de] bg-slate-50 hover:bg-cyan-50/60 border border-slate-200/70 transition-colors" title="Abrir tienda en otra pestaña">
                    <span class="material-symbols-outlined text-[16px] text-[#00c1de]">storefront</span>
                    <span class="hidden sm:inline">Ver Tienda</span>
                    <span class="material-symbols-outlined text-[12px] text-slate-400">open_in_new</span>
                </a>

                <!-- Perfil desplegable al tocar el avatar -->
                <div class="relative" id="userProfileDropdownContainer">
                    <button id="userProfileBtn" type="button" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-cyan-50 hover:bg-cyan-100 border border-cyan-200/80 flex items-center justify-center text-[#00a3bd] font-heading font-extrabold text-xs transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#00c1de]" title="Perfil de usuario" aria-expanded="false" aria-haspopup="true">
                        {{ mb_strtoupper(mb_substr($usuario->persona->nombre, 0, 1) . mb_substr($usuario->persona->apellido, 0, 1)) }}
                    </button>

                    <!-- Menú emergente de usuario -->
                    <div id="userProfileDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-slate-100 py-2 z-50 transform origin-top-right transition-all">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-xs font-bold text-slate-900 leading-tight">
                                {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
                            </p>
                            @if ($usuario->email)
                                <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $usuario->email }}</p>
                            @endif
                            <div class="mt-2">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-cyan-50 text-[#00a3bd] border border-cyan-100">
                                    {{ $usuario->rol }}
                                </span>
                            </div>
                        </div>

                        <div class="px-2 pt-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left">
                                    <span class="material-symbols-outlined text-[16px]">logout</span>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <!-- Contenido Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">

        <!-- Hero Minimalista (Mismo diseño que Landing) -->
        <section class="hero-gradient rounded-3xl p-6 sm:p-10 border border-white shadow-soft relative overflow-hidden">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-xs text-[#00a3bd] text-xs font-bold px-3.5 py-1.5 rounded-full shadow-2xs border border-cyan-100">
                        <span class="w-2 h-2 rounded-full bg-[#00c1de] animate-pulse"></span>
                        {{ $usuario->rol === 'CONSULTOR' ? 'Gestión Integral en 4 Módulos' : 'Operación Comercial Habilitada' }}
                    </span>

                    <h1 class="font-heading text-2xl sm:text-4xl font-extrabold tracking-tight text-[#0e1b2e] leading-tight">
                        ¡{{ $usuario->persona->saludo }}, {{ $usuario->persona->nombre }}!
                    </h1>

                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        @if ($usuario->rol === 'CONSULTOR')
                            Todo tu negocio está organizado en <strong class="text-[#0e1b2e] font-semibold">4 módulos unificados</strong>: registra ventas al contado o a crédito, controla tu stock y compras, consulta tu contabilidad automática y gestiona tus clientes desde un solo lugar.
                        @else
                            Gestiona ventas, pedidos del catálogo, inventario y cobros de clientes de forma rápida y minimalista.
                        @endif
                    </p>
                </div>

                <!-- Acciones Rápidas en Píldoras -->
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('panel.ventas.create') }}"
                       class="btn-bounce inline-flex items-center gap-2 bg-[#00c1de] hover:bg-[#00a3bd] text-white font-bold px-6 py-3.5 rounded-full shadow-md hover:shadow-lg transition-all text-xs sm:text-sm">
                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                        <span>Nueva Venta</span>
                    </a>
                    <a href="{{ route('panel.productos.create') }}"
                       class="btn-bounce inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-[#0e1b2e] font-bold px-5 py-3.5 rounded-full border border-slate-200/80 shadow-2xs transition-all text-xs sm:text-sm">
                        <span class="material-symbols-outlined text-[18px] text-[#00c1de]">add_box</span>
                        <span>Nuevo Producto</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Tarjetas de Métricas Clave (KPIs de Negocio para Consultora) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Cobros Pendientes del Mes -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-soft card-hover-lift group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cuentas por Cobrar</span>
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-black text-finora-navy font-heading">
                        @money($cobrosPendientesMes ?? 0)
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-amber-500">schedule</span>
                        <span>Cobros pendientes del mes</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('panel.pagos.index') }}" class="font-bold text-finora-blue hover:underline">
                        Ver cartera de pagos &rarr;
                    </a>
                </div>
            </div>

            <!-- Ganancia Estimada del Mes -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-soft card-hover-lift group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ganancia del Mes</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                        <span class="material-symbols-outlined text-[20px]">trending_up</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-black text-emerald-600 font-heading">
                        @money($gananciaEstimadaMes ?? 0)
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-emerald-500">insights</span>
                        <span>Margen neto ventas confirmadas</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('contabilidad.estados-financieros') }}" class="font-bold text-finora-blue hover:underline">
                        Ver Estado de Resultados &rarr;
                    </a>
                </div>
            </div>

            <!-- Clientes Morosos / Cuotas Vencidas -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-soft card-hover-lift group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alerta de Morosidad</span>
                    <div class="w-10 h-10 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                        <span class="material-symbols-outlined text-[20px]">error_outline</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-black {{ ($cuotasVencidasCount ?? 0) > 0 ? 'text-red-600' : 'text-finora-navy' }} font-heading">
                        {{ $cuotasVencidasCount ?? 0 }} {{ ($cuotasVencidasCount ?? 0) === 1 ? 'venta' : 'ventas' }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-red-500">timer_off</span>
                        <span>Con cuotas vencidas impagas</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('panel.clientes.index') }}" class="font-bold text-finora-blue hover:underline">
                        Cobranza a morosos &rarr;
                    </a>
                </div>
            </div>

            <!-- Stock Crítico -->
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-soft card-hover-lift group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Stock Crítico</span>
                    <div class="w-10 h-10 rounded-2xl bg-cyan-50 text-finora-blue flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                        <span class="material-symbols-outlined text-[20px]">inventory</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-2xl font-black {{ ($stockCriticoCount ?? 0) > 0 ? 'text-amber-600' : 'text-finora-navy' }} font-heading">
                        {{ $stockCriticoCount ?? 0 }} {{ ($stockCriticoCount ?? 0) === 1 ? 'producto' : 'productos' }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-cyan-600">warning</span>
                        <span>Existencias &le; 3 unidades</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="{{ route('panel.inventario.index') }}" class="font-bold text-finora-blue hover:underline">
                        Reponer para Ciclo Natura &rarr;
                    </a>
                </div>
            </div>
        </section>

        <!-- Últimas Ventas y Operaciones Recientes -->
        @if (isset($ultimasVentas) && $ultimasVentas->isNotEmpty())
            <section class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-soft space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-finora-navy">Últimas Ventas Registradas</h3>
                        <p class="text-xs text-slate-500">Acceso rápido a las transacciones más recientes.</p>
                    </div>
                    <a href="{{ route('panel.ventas.index') }}" class="text-xs font-bold text-finora-blue hover:underline">
                        Ver todas las ventas &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase">
                            <tr>
                                <th class="py-2.5 px-3"># Venta</th>
                                <th class="py-2.5 px-3">Cliente</th>
                                <th class="py-2.5 px-3">Fecha</th>
                                <th class="py-2.5 px-3">Modalidad</th>
                                <th class="py-2.5 px-3 text-right">Total</th>
                                <th class="py-2.5 px-3 text-center">Estado</th>
                                <th class="py-2.5 px-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($ultimasVentas as $uv)
                                <tr>
                                    <td class="py-3 px-3 font-bold text-finora-navy">#{{ $uv->id }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-700">
                                        {{ $uv->cliente->persona->nombre }} {{ $uv->cliente->persona->apellido }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-500">{{ $uv->fecha ? $uv->fecha->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $uv->forma_pago === 'CREDITO' ? 'bg-purple-50 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $uv->forma_pago }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right font-extrabold text-finora-navy">@money($uv->total)</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $uv->estado === 'CONFIRMADA' ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $uv->estado }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <a href="{{ route('panel.ventas.show', $uv) }}" class="inline-flex items-center gap-1 text-xs font-bold text-finora-blue hover:underline">
                                            Ver ficha &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

    </main>

@include('partials.panel-footer')

</body>
</html>
