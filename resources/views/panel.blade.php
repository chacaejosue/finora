<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Panel Principal</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>

    <!-- Tipografías de Google -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

    <!-- Iconos Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-[#00c1de] selection:text-white flex flex-col min-h-screen">

    <!-- Barra de navegación superior minimalista (Estilo Landing) -->
    <header class="bg-white/85 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-2">
            <!-- Marca Finora -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <button id="openPanelDrawerBtn" type="button" class="p-2 rounded-full text-slate-600 hover:text-[#0e1b2e] hover:bg-slate-100 transition-colors cursor-pointer shrink-0" title="Navegación de módulos" aria-label="Abrir menú de módulos" aria-controls="panelDrawer" aria-expanded="false">
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>
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
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold text-slate-600 hover:text-[#00c1de] bg-slate-50 hover:bg-cyan-50/60 border border-slate-200/70 transition-all" title="Ir al catálogo público">
                    <span class="material-symbols-outlined text-[16px] text-[#00c1de]">storefront</span>
                    <span class="hidden sm:inline">Ver Tienda</span>
                </a>

                <div class="hidden md:flex items-center gap-2.5 pl-2 border-l border-slate-100">
                    <div class="text-right">
                        <p class="text-xs font-bold text-[#0e1b2e] leading-tight">
                            {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
                        </p>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-cyan-50 text-[#00a3bd]">
                            {{ $usuario->rol }}
                        </span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-cyan-50 border border-cyan-100 flex items-center justify-center text-[#00a3bd] font-heading font-bold text-xs">
                        {{ mb_strtoupper(mb_substr($usuario->persona->nombre, 0, 1) . mb_substr($usuario->persona->apellido, 0, 1)) }}
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold text-slate-600 hover:text-rose-600 bg-slate-50 hover:bg-rose-50 border border-slate-200/70 hover:border-rose-200 transition-all cursor-pointer"
                        title="Cerrar sesión"
                    >
                        <span class="material-symbols-outlined text-[16px]">logout</span>
                        <span class="hidden sm:inline">Salir</span>
                    </button>
                </form>
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
                       class="inline-flex items-center gap-2 bg-[#00c1de] hover:bg-[#00a3bd] text-white font-bold px-6 py-3.5 rounded-full shadow-md hover:shadow-lg transition-all text-xs sm:text-sm">
                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                        <span>Nueva Venta</span>
                    </a>
                    <a href="{{ route('panel.productos.create') }}"
                       class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-[#0e1b2e] font-bold px-5 py-3.5 rounded-full border border-slate-200/80 shadow-2xs transition-all text-xs sm:text-sm">
                        <span class="material-symbols-outlined text-[18px] text-[#00c1de]">add_box</span>
                        <span>Nuevo Producto</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Encabezado de los 4 Módulos Agrupados -->
        <section class="space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                <div>
                    <span class="text-[#00c1de] font-bold text-xs tracking-wider uppercase block mb-1">Espacio de Trabajo</span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-[#0e1b2e] tracking-tight">Módulos del Sistema</h2>
                </div>
                <p class="text-xs text-slate-400 font-medium">
                    Selecciona un módulo principal o entra directo a cualquiera de sus pestañas
                </p>
            </div>

            <!-- Grid de los 4 Módulos Unificados -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- MÓDULO 1: VENTAS Y PEDIDOS -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-soft hover:border-[#00c1de]/40 transition-all flex flex-col justify-between gap-6 group">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-[#00c1de] flex items-center justify-center shrink-0 group-hover:bg-[#00c1de] group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-2xl">point_of_sale</span>
                            </div>
                            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-slate-50 text-slate-500 border border-slate-100">
                                Módulo 1 &bull; Comercial
                            </span>
                        </div>

                        <div>
                            <h3 class="text-lg sm:text-xl font-extrabold text-[#0e1b2e] tracking-tight">
                                Ventas y Pedidos
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                                Registra ventas al contado o a crédito, atiende reservas recibidas desde el catálogo web y registra abonos o cobros pendientes.
                            </p>
                        </div>

                        <!-- Sub-módulos integrados como píldoras -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            <a href="{{ route('panel.ventas.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-cyan-50/80 text-[#00a3bd] hover:bg-[#00c1de] hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[15px]">receipt_long</span>
                                <span>Ventas</span>
                            </a>
                            <a href="{{ route('panel.pedidos.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">shopping_bag</span>
                                <span>Pedidos Web</span>
                            </a>
                            <a href="{{ route('panel.pagos.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">payments</span>
                                <span>Créditos y Pagos</span>
                            </a>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('panel.ventas.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#0e1b2e] group-hover:text-[#00c1de] transition-colors">
                            <span>Abrir Ventas y Pedidos</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                        <a href="{{ route('panel.ventas.create') }}" class="text-xs font-bold text-[#00a3bd] hover:underline">
                            + Registrar venta
                        </a>
                    </div>
                </div>

                <!-- MÓDULO 2: CATÁLOGO E INVENTARIO -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-soft hover:border-[#00c1de]/40 transition-all flex flex-col justify-between gap-6 group">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-[#00c1de] flex items-center justify-center shrink-0 group-hover:bg-[#00c1de] group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-2xl">inventory_2</span>
                            </div>
                            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-slate-50 text-slate-500 border border-slate-100">
                                Módulo 2 &bull; Almacén
                            </span>
                        </div>

                        <div>
                            <h3 class="text-lg sm:text-xl font-extrabold text-[#0e1b2e] tracking-tight">
                                Catálogo e Inventario
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                                Administra tus productos, precios y stock inicial, revisa existencias en tiempo real, registra compras a Natura o proveedores y organiza categorías.
                            </p>
                        </div>

                        <!-- Sub-módulos integrados como píldoras -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            <a href="{{ route('panel.productos.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-cyan-50/80 text-[#00a3bd] hover:bg-[#00c1de] hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[15px]">package_2</span>
                                <span>Productos</span>
                            </a>
                            <a href="{{ route('panel.inventario.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">warehouse</span>
                                <span>Inventario</span>
                            </a>
                            <a href="{{ route('panel.compras.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">local_shipping</span>
                                <span>Compras</span>
                            </a>
                            @if ($usuario->rol === 'CONSULTOR')
                                <a href="{{ route('panel.categorias.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">category</span>
                                    <span>Categorías</span>
                                </a>
                                <a href="{{ route('panel.proveedores.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">storefront</span>
                                    <span>Proveedores</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('panel.productos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#0e1b2e] group-hover:text-[#00c1de] transition-colors">
                            <span>Abrir Catálogo e Inventario</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                        <a href="{{ route('panel.productos.create') }}" class="text-xs font-bold text-[#00a3bd] hover:underline">
                            + Agregar producto
                        </a>
                    </div>
                </div>

                <!-- MÓDULO 3: CONTABILIDAD Y REPORTES (Solo Consultor) -->
                @if ($usuario->rol === 'CONSULTOR')
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-soft hover:border-[#00c1de]/40 transition-all flex flex-col justify-between gap-6 group">
                        <div class="space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-[#00c1de] flex items-center justify-center shrink-0 group-hover:bg-[#00c1de] group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined text-2xl">monitoring</span>
                                </div>
                                <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-cyan-50 text-[#00a3bd]">
                                    Módulo 3 &bull; Finanzas y Ley 843
                                </span>
                            </div>

                            <div>
                                <h3 class="text-lg sm:text-xl font-extrabold text-[#0e1b2e] tracking-tight">
                                    Contabilidad y Reportes
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                                    Todo tu control financiero unificado: reportes comerciales, Balance de Apertura, Libro Diario, Mayor, Estados Financieros y Kardex Valorado CPP.
                                </p>
                            </div>

                            <!-- Sub-módulos integrados como píldoras -->
                            <div class="flex flex-wrap gap-2 pt-1">
                                <a href="{{ route('contabilidad.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-cyan-50/80 text-[#00a3bd] hover:bg-[#00c1de] hover:text-white transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">monitoring</span>
                                    <span>Resumen Contable</span>
                                </a>
                                <a href="{{ route('panel.reportes.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">analytics</span>
                                    <span>Reportes</span>
                                </a>
                                <a href="{{ route('contabilidad.libro-diario') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">menu_book</span>
                                    <span>Libro Diario</span>
                                </a>
                                <a href="{{ route('contabilidad.estados-financieros') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">query_stats</span>
                                    <span>Estados Financieros</span>
                                </a>
                                <a href="{{ route('contabilidad.kardex-valorado') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">receipt_long</span>
                                    <span>Kardex CPP</span>
                                </a>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('contabilidad.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#0e1b2e] group-hover:text-[#00c1de] transition-colors">
                                <span>Abrir Contabilidad y Reportes</span>
                                <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                            <a href="{{ route('panel.reportes.index') }}" class="text-xs font-bold text-[#00a3bd] hover:underline">
                                Ver indicadores
                            </a>
                        </div>
                    </div>
                @endif

                <!-- MÓDULO 4: CLIENTES Y USUARIOS -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-soft hover:border-[#00c1de]/40 transition-all flex flex-col justify-between gap-6 group">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-[#00c1de] flex items-center justify-center shrink-0 group-hover:bg-[#00c1de] group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-2xl">group</span>
                            </div>
                            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-slate-50 text-slate-500 border border-slate-100">
                                Módulo {{ $usuario->rol === 'CONSULTOR' ? '4' : '3' }} &bull; Personas
                            </span>
                        </div>

                        <div>
                            <h3 class="text-lg sm:text-xl font-extrabold text-[#0e1b2e] tracking-tight">
                                {{ $usuario->rol === 'CONSULTOR' ? 'Clientes y Usuarios' : 'Cartera de Clientes' }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                                @if ($usuario->rol === 'CONSULTOR')
                                    Gestiona el directorio de clientes, historial de compras y saldos, además de las cuentas de colaboradores que te ayudan en el negocio.
                                @else
                                    Consulta y registra los datos de contacto de los clientes para asociar sus pedidos, compras y pagos.
                                @endif
                            </p>
                        </div>

                        <!-- Sub-módulos integrados como píldoras -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            <a href="{{ route('panel.clientes.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-cyan-50/80 text-[#00a3bd] hover:bg-[#00c1de] hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[15px]">group</span>
                                <span>Cartera de Clientes</span>
                            </a>
                            @if ($usuario->rol === 'CONSULTOR')
                                <a href="{{ route('panel.usuarios.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] border border-slate-200/60 transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">manage_accounts</span>
                                    <span>Usuarios y Colaboradores</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('panel.clientes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#0e1b2e] group-hover:text-[#00c1de] transition-colors">
                            <span>Abrir {{ $usuario->rol === 'CONSULTOR' ? 'Clientes y Usuarios' : 'Clientes' }}</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                        <a href="{{ route('panel.clientes.create') }}" class="text-xs font-bold text-[#00a3bd] hover:underline">
                            + Nuevo cliente
                        </a>
                    </div>
                </div>

            </div>
        </section>

        @if ($usuario->rol === 'CONSULTOR')
        @php
            $tasaInicial = $tasaInicial ?? ['base' => 'USD', 'destino' => 'BOB', 'tasa' => 6.9600, 'fecha' => date('Y-m-d')];
            $monedasDisponibles = $monedasDisponibles ?? [
                'USD' => 'Dólar Estadounidense',
                'BOB' => 'Boliviano',
                'EUR' => 'Euro',
                'BRL' => 'Real Brasileño',
                'ARS' => 'Peso Argentino',
                'PEN' => 'Sol Peruano',
                'CLP' => 'Peso Chileno',
            ];
        @endphp
        <!-- Conversor de Divisas Minimalista (Estilo Banner Landing) -->
        <section class="banner-gradient rounded-3xl p-6 sm:p-8 border border-cyan-100/80 shadow-soft" id="conversor-card">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-1.5 max-w-md">
                    <div class="inline-flex items-center gap-1.5 text-[#00a3bd] text-xs font-bold">
                        <span class="material-symbols-outlined text-base">currency_exchange</span>
                        <span>Herramienta Financiera</span>
                    </div>
                    <h3 class="font-heading font-extrabold text-lg text-[#0e1b2e]">Conversor de Divisas en Tiempo Real</h3>
                    <p class="text-xs text-slate-600">
                        Calcula importes rápidamente entre Bolivianos (BOB), Dólares (USD) y otras divisas.
                    </p>
                    <p class="text-[11px] text-slate-500 pt-1">
                        1 <span class="font-bold" id="label-base">{{ $tasaInicial['base'] }}</span> =
                        <span class="font-bold text-[#0e1b2e]" id="tasa-referencia-texto">{{ number_format($tasaInicial['tasa'], 4) }}</span>
                        <span class="font-bold" id="label-destino">{{ $tasaInicial['destino'] }}</span>
                        &bull; <span id="fecha-referencia-texto">{{ $tasaInicial['fecha'] }}</span>
                    </p>
                </div>

                <div class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white/90 backdrop-blur-xs p-4 rounded-2xl border border-white shadow-2xs">
                    <!-- Monto -->
                    <div class="sm:col-span-3">
                        <label for="monto-origen" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Importe</label>
                        <input
                            type="number"
                            id="monto-origen"
                            value="100"
                            min="0"
                            step="any"
                            class="w-full bg-[#f7fafd] border border-slate-200 rounded-full px-3.5 py-2 text-xs font-bold text-[#0e1b2e] focus:bg-white outline-none"
                        />
                    </div>

                    <!-- Origen -->
                    <div class="sm:col-span-3">
                        <label for="moneda-origen" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">De</label>
                        <select
                            id="moneda-origen"
                            class="w-full bg-[#f7fafd] border border-slate-200 rounded-full px-3 py-2 text-xs font-semibold text-[#0e1b2e] focus:bg-white outline-none cursor-pointer"
                        >
                            @foreach ($monedasDisponibles as $codigo => $nombre)
                                <option value="{{ $codigo }}" {{ $codigo === 'USD' ? 'selected' : '' }}>
                                    {{ $codigo }} - {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Invertir -->
                    <div class="sm:col-span-1 flex justify-center sm:pt-4">
                        <button
                            type="button"
                            id="btn-invertir-moneda"
                            class="w-8 h-8 rounded-full bg-cyan-50 hover:bg-[#00c1de] text-[#00a3bd] hover:text-white flex items-center justify-center transition-colors cursor-pointer"
                            title="Invertir divisas"
                        >
                            <span class="material-symbols-outlined text-base">swap_horiz</span>
                        </button>
                    </div>

                    <!-- Destino -->
                    <div class="sm:col-span-3">
                        <label for="moneda-destino" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">A</label>
                        <select
                            id="moneda-destino"
                            class="w-full bg-[#f7fafd] border border-slate-200 rounded-full px-3 py-2 text-xs font-semibold text-[#0e1b2e] focus:bg-white outline-none cursor-pointer"
                        >
                            @foreach ($monedasDisponibles as $codigo => $nombre)
                                <option value="{{ $codigo }}" {{ $codigo === 'BOB' ? 'selected' : '' }}>
                                    {{ $codigo }} - {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Resultado -->
                    <div class="sm:col-span-2 text-right sm:pl-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Resultado</span>
                        <span class="font-heading text-base font-extrabold text-[#00a3bd] break-all" id="monto-convertido-valor">
                            {{ number_format(100 * $tasaInicial['tasa'], 2) }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-500 block" id="codigo-destino-badge">BOB</span>
                    </div>
                </div>
            </div>
        </section>
        @endif

    </main>

    @if ($usuario->rol === 'CONSULTOR')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputMonto = document.getElementById('monto-origen');
            const selectOrigen = document.getElementById('moneda-origen');
            const selectDestino = document.getElementById('moneda-destino');
            const btnInvertir = document.getElementById('btn-invertir-moneda');

            const valorConvertido = document.getElementById('monto-convertido-valor');
            const badgeDestino = document.getElementById('codigo-destino-badge');
            const labelBase = document.getElementById('label-base');
            const labelDestino = document.getElementById('label-destino');
            const textoTasa = document.getElementById('tasa-referencia-texto');
            const textoFecha = document.getElementById('fecha-referencia-texto');

            let tasaActual = {{ (float) $tasaInicial['tasa'] }};

            function recalcularMontoLocal() {
                const monto = parseFloat(inputMonto.value) || 0;
                const total = monto * tasaActual;
                valorConvertido.textContent = total.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            async function actualizarTasaDesdeServidor() {
                const base = selectOrigen.value;
                const destino = selectDestino.value;
                const monto = parseFloat(inputMonto.value) || 0;

                badgeDestino.textContent = destino;
                labelBase.textContent = base;
                labelDestino.textContent = destino;
                valorConvertido.classList.add('opacity-50');

                try {
                    const url = new URL("{{ route('tipo-cambio') }}", window.location.origin);
                    url.searchParams.set('base', base);
                    url.searchParams.set('destino', destino);
                    url.searchParams.set('monto', monto);

                    const response = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) throw new Error('Error al consultar tasa');
                    const data = await response.json();

                    tasaActual = parseFloat(data.tasa) || 1;
                    textoTasa.textContent = tasaActual.toFixed(4);
                    textoFecha.textContent = data.fecha || '';
                    recalcularMontoLocal();
                } catch (e) {
                    console.error('No se pudo obtener la tasa de cambio:', e);
                } finally {
                    valorConvertido.classList.remove('opacity-50');
                }
            }

            inputMonto.addEventListener('input', recalcularMontoLocal);
            selectOrigen.addEventListener('change', actualizarTasaDesdeServidor);
            selectDestino.addEventListener('change', actualizarTasaDesdeServidor);

            btnInvertir.addEventListener('click', () => {
                const temp = selectOrigen.value;
                selectOrigen.value = selectDestino.value;
                selectDestino.value = temp;
                actualizarTasaDesdeServidor();
            });
        });
    </script>
    @endif

    @include('partials.panel-footer')

</body>
</html>
