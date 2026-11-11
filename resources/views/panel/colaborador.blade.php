<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Panel del Colaborador</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-[#00c1de] selection:text-white flex flex-col min-h-screen">

    <header class="bg-white/85 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <a class="inline-flex items-center gap-2.5 sm:gap-3 group min-w-0" href="{{ route('panel.colaborador') }}">
                    <div class="relative w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-8 sm:h-9 w-auto object-contain group-hover:scale-105 transition-transform duration-300"/>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-heading text-lg sm:text-xl font-extrabold tracking-tight text-[#0e1b2e] truncate">Finora</span>
                        <span class="text-[10px] font-semibold text-slate-400 -mt-1 tracking-wide hidden sm:block">Panel del Colaborador</span>
                    </div>
                </a>
            </div>

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

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">
        <!-- Hero Minimalista -->
        <section class="hero-gradient rounded-3xl p-6 sm:p-10 border border-white shadow-soft relative overflow-hidden">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-xs text-[#00a3bd] text-xs font-bold px-3.5 py-1.5 rounded-full shadow-2xs border border-cyan-100">
                        <span class="w-2 h-2 rounded-full bg-[#00c1de] animate-pulse"></span>
                        Panel del Colaborador &bull; Operaciones disponibles
                    </span>
                    <h1 class="font-heading text-2xl sm:text-4xl font-extrabold tracking-tight text-[#0e1b2e]">
                        ¡{{ $usuario->persona->saludo }}, {{ $usuario->persona->nombre }}!
                    </h1>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Gestiona las ventas, pedidos recibidos, inventario y cobros de clientes desde tus módulos agrupados.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('panel.ventas.create') }}"
                       class="btn-bounce inline-flex items-center gap-2 bg-[#00c1de] hover:bg-[#00a3bd] text-white font-bold px-6 py-3.5 rounded-full shadow-md hover:shadow-lg transition-all text-xs sm:text-sm">
                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                        <span>Nueva Venta</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Módulos Agrupados para Colaborador -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 1. Ventas y Pedidos -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-soft card-hover-lift flex flex-col justify-between gap-5 group">
                <div class="space-y-3">
                    <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-[#00c1de] flex items-center justify-center group-hover:bg-[#00c1de] group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-2xl">point_of_sale</span>
                    </div>
                    <h2 class="text-lg font-extrabold text-[#0e1b2e]">Ventas y Pedidos</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Registra ventas al contado o a crédito, atiende reservas del catálogo y registra abonos.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <a href="{{ route('panel.ventas.index') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 text-[#00a3bd] hover:bg-[#00c1de] hover:text-white transition-colors">Ventas</a>
                        <a href="{{ route('panel.pedidos.index') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] transition-colors">Pedidos</a>
                        <a href="{{ route('panel.pagos.index') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] transition-colors">Pagos</a>
                    </div>
                </div>
                <a href="{{ route('panel.ventas.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-[#0e1b2e] group-hover:text-[#00c1de] pt-3 border-t border-slate-100">
                    <span>Abrir módulo</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- 2. Catálogo e Inventario -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-soft card-hover-lift flex flex-col justify-between gap-5 group">
                <div class="space-y-3">
                    <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-[#00c1de] flex items-center justify-center group-hover:bg-[#00c1de] group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-2xl">inventory_2</span>
                    </div>
                    <h2 class="text-lg font-extrabold text-[#0e1b2e]">Catálogo e Inventario</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Consulta y administra productos, existencias en almacén, movimientos y compras.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <a href="{{ route('panel.productos.index') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 text-[#00a3bd] hover:bg-[#00c1de] hover:text-white transition-colors">Productos</a>
                        <a href="{{ route('panel.inventario.index') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] transition-colors">Inventario</a>
                        <a href="{{ route('panel.compras.index') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] transition-colors">Compras</a>
                    </div>
                </div>
                <a href="{{ route('panel.productos.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-[#0e1b2e] group-hover:text-[#00c1de] pt-3 border-t border-slate-100">
                    <span>Abrir módulo</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- 3. Cartera de Clientes -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-soft card-hover-lift flex flex-col justify-between gap-5 group">
                <div class="space-y-3">
                    <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-[#00c1de] flex items-center justify-center group-hover:bg-[#00c1de] group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-2xl">group</span>
                    </div>
                    <h2 class="text-lg font-extrabold text-[#0e1b2e]">Cartera de Clientes</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Consulta datos de contacto, historial de pedidos y registra nuevos clientes.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <a href="{{ route('panel.clientes.index') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 text-[#00a3bd] hover:bg-[#00c1de] hover:text-white transition-colors">Directorio</a>
                        <a href="{{ route('panel.clientes.create') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-50 text-slate-600 hover:bg-cyan-50 hover:text-[#00a3bd] transition-colors">+ Nuevo cliente</a>
                    </div>
                </div>
                <a href="{{ route('panel.clientes.index') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-[#0e1b2e] group-hover:text-[#00c1de] pt-3 border-t border-slate-100">
                    <span>Abrir módulo</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </section>
    </main>

    @include('partials.panel-footer')
</body>
</html>
