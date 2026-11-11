@php
    $esConsultor = auth()->check() && auth()->user()->rol === 'CONSULTOR';
    $esInicioPanel = request()->routeIs('panel.index', 'panel.colaborador');
    $esModVentas = request()->routeIs('panel.ventas*', 'panel.pedidos*', 'panel.pagos*');
    $esModCatalogo = request()->routeIs('panel.productos*', 'panel.inventario*', 'panel.compras*', 'panel.categorias*', 'panel.proveedores*');
    $esModContable = request()->routeIs('contabilidad.*', 'panel.reportes*');
    $esModClientes = request()->routeIs('panel.clientes*', 'panel.usuarios*');
@endphp

<!-- Barra Unificada de 4 Módulos y Sub-pestañas (Diseño Minimalista Landing) -->
<div id="panelNavbarContainer" class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-18 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Nivel 1: Los 4 Módulos Principales -->
        <nav class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto px-1.5 py-2.5 no-scrollbar text-xs font-semibold" aria-label="Módulos principales del panel">
            <a href="{{ route('panel.index') }}"
               class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full whitespace-nowrap shrink-0 transition-all {{ $esInicioPanel ? 'bg-[#0e1b2e] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-[#0e1b2e] hover:bg-slate-100/80' }}">
                <span class="material-symbols-outlined text-[16px]">space_dashboard</span>
                <span>Inicio</span>
            </a>

            <span class="h-4 w-px bg-slate-200 shrink-0 mx-0.5"></span>

            <!-- 1. Ventas y Pedidos -->
            <a href="{{ route('panel.ventas.index') }}"
               class="btn-bounce inline-flex items-center gap-1.5 px-4 py-2 rounded-full whitespace-nowrap shrink-0 transition-all {{ $esModVentas ? 'bg-[#00c1de] text-white font-bold shadow-xs shadow-cyan-500/20' : 'text-slate-600 hover:text-[#0e1b2e] hover:bg-cyan-50/60' }}">
                <span class="material-symbols-outlined text-[16px]">point_of_sale</span>
                <span>Ventas y Pedidos</span>
            </a>

            <!-- 2. Catálogo e Inventario -->
            <a href="{{ route('panel.productos.index') }}"
               class="btn-bounce inline-flex items-center gap-1.5 px-4 py-2 rounded-full whitespace-nowrap shrink-0 transition-all {{ $esModCatalogo ? 'bg-[#00c1de] text-white font-bold shadow-xs shadow-cyan-500/20' : 'text-slate-600 hover:text-[#0e1b2e] hover:bg-cyan-50/60' }}">
                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                <span>Catálogo e Inventario</span>
            </a>

            <!-- 3. Contabilidad y Reportes (Solo Consultor) -->
            @if($esConsultor)
                <a href="{{ route('contabilidad.index') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-4 py-2 rounded-full whitespace-nowrap shrink-0 transition-all {{ $esModContable ? 'bg-[#00c1de] text-white font-bold shadow-xs shadow-cyan-500/20' : 'text-slate-600 hover:text-[#0e1b2e] hover:bg-cyan-50/60' }}">
                    <span class="material-symbols-outlined text-[16px]">monitoring</span>
                    <span>Contabilidad y Reportes</span>
                </a>
            @endif

            <!-- 4. Clientes y Usuarios -->
            <a href="{{ route('panel.clientes.index') }}"
               class="btn-bounce inline-flex items-center gap-1.5 px-4 py-2 rounded-full whitespace-nowrap shrink-0 transition-all {{ $esModClientes ? 'bg-[#00c1de] text-white font-bold shadow-xs shadow-cyan-500/20' : 'text-slate-600 hover:text-[#0e1b2e] hover:bg-cyan-50/60' }}">
                <span class="material-symbols-outlined text-[16px]">group</span>
                <span>{{ $esConsultor ? 'Clientes y Usuarios' : 'Clientes' }}</span>
            </a>
        </nav>

        <!-- Nivel 2: Sub-pestañas Contextuales del Módulo Activo -->
        @if($esModVentas)
            <div class="flex items-center justify-between gap-2 overflow-x-auto px-1.5 py-2 border-t border-slate-100/90 no-scrollbar text-xs animate-subnav-enter">
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('panel.ventas.index') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.ventas*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">receipt_long</span>
                        <span>Ventas</span>
                    </a>
                    <a href="{{ route('panel.pedidos.index') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.pedidos*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">shopping_bag</span>
                        <span>Pedidos del Catálogo</span>
                    </a>
                    <a href="{{ route('panel.pagos.index') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.pagos*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">payments</span>
                        <span>Créditos y Pagos</span>
                    </a>
                </div>
                <a href="{{ route('panel.ventas.create') }}"
                   class="btn-bounce hidden sm:inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full bg-[#0e1b2e] text-white text-[11px] font-bold hover:bg-slate-800 transition-all shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-[14px]">add</span>
                    <span>Registrar Venta</span>
                </a>
            </div>
        @elseif($esModCatalogo)
            <div class="flex items-center justify-between gap-2 overflow-x-auto px-1.5 py-2 border-t border-slate-100/90 no-scrollbar text-xs animate-subnav-enter">
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('panel.productos.index') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.productos*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">package_2</span>
                        <span>Productos</span>
                    </a>
                    <a href="{{ route('panel.inventario.index') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.inventario.index', 'panel.inventario.ajustar') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">warehouse</span>
                        <span>Existencias</span>
                    </a>
                    <a href="{{ route('panel.inventario.movimientos') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.inventario.movimientos') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">swap_vert</span>
                        <span>Kárdex Físico</span>
                    </a>
                    <a href="{{ route('panel.compras.index') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.compras*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">local_shipping</span>
                        <span>Compras</span>
                    </a>
                    @if($esConsultor)
                        <a href="{{ route('panel.categorias.index') }}"
                           class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.categorias*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[15px]">category</span>
                            <span>Categorías</span>
                        </a>
                        <a href="{{ route('panel.proveedores.index') }}"
                           class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.proveedores*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined text-[15px]">storefront</span>
                            <span>Proveedores</span>
                        </a>
                    @endif
                </div>
            </div>
        @elseif($esModContable && $esConsultor)
            <div class="flex items-center gap-1.5 overflow-x-auto px-1.5 py-2 border-t border-slate-100/90 no-scrollbar text-xs animate-subnav-enter">
                <a href="{{ route('contabilidad.index') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.index') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">monitoring</span>
                    <span>Resumen Contable</span>
                </a>
                <a href="{{ route('panel.reportes.index') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.reportes*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">analytics</span>
                    <span>Reportes</span>
                </a>
                <a href="{{ route('contabilidad.balance-apertura') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.balance-apertura*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">flag</span>
                    <span>Balance de Apertura</span>
                </a>
                <a href="{{ route('contabilidad.libro-diario') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.libro-diario*', 'contabilidad.asientos*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">menu_book</span>
                    <span>Libro Diario</span>
                </a>
                <a href="{{ route('contabilidad.libro-mayor') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.libro-mayor*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">import_contacts</span>
                    <span>Libro Mayor</span>
                </a>
                <a href="{{ route('contabilidad.sumas-saldos') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.sumas-saldos*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">balance</span>
                    <span>Sumas y Saldos</span>
                </a>
                <a href="{{ route('contabilidad.estados-financieros') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.estados-financieros*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">query_stats</span>
                    <span>Estados Financieros</span>
                </a>
                <a href="{{ route('contabilidad.kardex-valorado') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.kardex-valorado*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">receipt_long</span>
                    <span>Kardex Valorado</span>
                </a>
                <a href="{{ route('contabilidad.plan-cuentas') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('contabilidad.plan-cuentas*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">account_tree</span>
                    <span>Plan de Cuentas</span>
                </a>
            </div>
        @elseif($esModClientes)
            <div class="flex items-center gap-1.5 overflow-x-auto px-1.5 py-2 border-t border-slate-100/90 no-scrollbar text-xs animate-subnav-enter">
                <a href="{{ route('panel.clientes.index') }}"
                   class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.clientes*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[15px]">group</span>
                    <span>Cartera de Clientes</span>
                </a>
                @if($esConsultor)
                    <a href="{{ route('panel.usuarios.index') }}"
                       class="btn-bounce inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap shrink-0 transition-all {{ request()->routeIs('panel.usuarios*') ? 'bg-cyan-50 text-[#0092aa] font-bold border border-[#00c1de]/50 shadow-xs' : 'text-slate-500 hover:text-[#0e1b2e] hover:bg-slate-50' }}">
                        <span class="material-symbols-outlined text-[15px]">manage_accounts</span>
                        <span>Usuarios y Colaboradores</span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Drawer lateral de Finora Panel (Agrupado en los 4 Módulos) -->
<div id="panelDrawerOverlay" class="fixed inset-0 bg-slate-900/35 backdrop-blur-xs z-40 hidden transition-opacity duration-300 opacity-0" aria-hidden="true"></div>

<aside id="panelDrawer" class="fixed inset-y-0 left-0 w-[min(19rem,calc(100vw-1rem))] bg-white z-50 shadow-2xl flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out border-r border-slate-100" aria-label="Navegación de módulos" aria-hidden="true">
    <!-- Encabezado del Drawer -->
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <a class="inline-flex items-center gap-3 group" href="{{ route('panel.index') }}">
            <div class="w-9 h-9 rounded-2xl bg-cyan-50 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-6 w-auto object-contain group-hover:scale-105 transition-transform"/>
            </div>
            <div class="flex flex-col">
                <span class="font-heading text-lg font-extrabold text-[#0e1b2e] tracking-tight">Finora</span>
                <span class="text-[10px] font-semibold text-slate-400 -mt-1 tracking-wide">Panel Minimalista</span>
            </div>
        </a>
        <button id="closePanelDrawerBtn" type="button" class="p-2 rounded-full text-slate-400 hover:text-[#0e1b2e] hover:bg-slate-100 transition-colors cursor-pointer" aria-label="Cerrar navegación">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>

    <!-- Lista de Módulos Agrupados -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-1" aria-label="Módulos del sistema">
        <a href="{{ route('panel.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-full text-xs font-bold transition-all {{ $esInicioPanel ? 'bg-[#0e1b2e] text-white shadow-xs' : 'text-[#0e1b2e] hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg">space_dashboard</span>
            <span>Inicio del Panel</span>
        </a>

        <!-- GRUPO 1: Ventas y Pedidos -->
        <div class="pt-4 pb-1 px-4">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#00a3bd]">1. Ventas y Pedidos</span>
        </div>
        <a href="{{ route('panel.ventas.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.ventas*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-[#00c1de]">point_of_sale</span>
            <span>Ventas</span>
        </a>
        <a href="{{ route('panel.pedidos.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.pedidos*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-[#00c1de]">shopping_bag</span>
            <span>Pedidos</span>
        </a>
        <a href="{{ route('panel.pagos.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.pagos*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-[#00c1de]">payments</span>
            <span>Créditos y Pagos</span>
        </a>

        <!-- GRUPO 2: Catálogo e Inventario -->
        <div class="pt-4 pb-1 px-4">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#00a3bd]">2. Catálogo e Inventario</span>
        </div>
        <a href="{{ route('panel.productos.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.productos*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-[#00c1de]">package_2</span>
            <span>Productos</span>
        </a>
        <a href="{{ route('panel.inventario.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.inventario*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-[#00c1de]">inventory_2</span>
            <span>Inventario y Kárdex</span>
        </a>
        <a href="{{ route('panel.compras.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.compras*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-[#00c1de]">local_shipping</span>
            <span>Compras</span>
        </a>
        @if($esConsultor)
            <a href="{{ route('panel.categorias.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.categorias*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">category</span>
                <span>Categorías</span>
            </a>
            <a href="{{ route('panel.proveedores.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.proveedores*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">storefront</span>
                <span>Proveedores</span>
            </a>
        @endif

        <!-- GRUPO 3: Contabilidad y Reportes -->
        @if($esConsultor)
            <div class="pt-4 pb-1 px-4">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#00a3bd]">3. Contabilidad y Reportes</span>
            </div>
            <a href="{{ route('contabilidad.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('contabilidad.index') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">monitoring</span>
                <span>Resumen Contable</span>
            </a>
            <a href="{{ route('panel.reportes.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.reportes*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">analytics</span>
                <span>Reportes Comerciales</span>
            </a>
            <a href="{{ route('contabilidad.libro-diario') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('contabilidad.libro-diario*', 'contabilidad.asientos*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">menu_book</span>
                <span>Libro Diario</span>
            </a>
            <a href="{{ route('contabilidad.estados-financieros') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('contabilidad.estados-financieros*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">query_stats</span>
                <span>Estados Financieros</span>
            </a>
            <a href="{{ route('contabilidad.kardex-valorado') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('contabilidad.kardex-valorado*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">receipt_long</span>
                <span>Kardex Valorado (CPP)</span>
            </a>
        @endif

        <!-- GRUPO 4: Clientes y Usuarios -->
        <div class="pt-4 pb-1 px-4">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#00a3bd]">{{ $esConsultor ? '4. Clientes y Usuarios' : '3. Clientes' }}</span>
        </div>
        <a href="{{ route('panel.clientes.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.clientes*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-[#00c1de]">group</span>
            <span>Clientes</span>
        </a>
        @if($esConsultor)
            <a href="{{ route('panel.usuarios.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request()->routeIs('panel.usuarios*') ? 'bg-cyan-50 text-[#00a3bd] font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-[#00c1de]">manage_accounts</span>
                <span>Usuarios y Colaboradores</span>
            </a>
        @endif
    </nav>

    <!-- Pie del Drawer: Catálogo público y Cerrar sesión -->
    <div class="p-4 border-t border-slate-100 bg-[#f7fafd] space-y-2">
        <a href="{{ route('landing') }}" target="_blank" class="w-full flex items-center justify-between px-4 py-2.5 rounded-full text-xs font-bold text-[#0e1b2e] hover:text-[#00c1de] bg-white border border-slate-200/80 shadow-2xs transition-all">
            <span class="inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-base text-[#00c1de]">storefront</span>
                <span>Ver Tienda Pública</span>
            </span>
            <span class="material-symbols-outlined text-xs">open_in_new</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-base">logout</span>
                <span>Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const openBtn = document.getElementById('openPanelDrawerBtn');
        const closeBtn = document.getElementById('closePanelDrawerBtn');
        const drawer = document.getElementById('panelDrawer');
        const overlay = document.getElementById('panelDrawerOverlay');

        function openDrawer() {
            if (!drawer || !overlay) return;
            overlay.classList.remove('hidden');
            drawer.setAttribute('aria-hidden', 'false');
            openBtn?.setAttribute('aria-expanded', 'true');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                drawer.classList.remove('-translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            if (!drawer || !overlay) return;
            overlay.classList.add('opacity-0');
            drawer.classList.add('-translate-x-full');
            setTimeout(() => {
                overlay.classList.add('hidden');
                drawer.setAttribute('aria-hidden', 'true');
                openBtn?.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }, 300);
        }

        if (openBtn) openBtn.addEventListener('click', openDrawer);
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (overlay) overlay.addEventListener('click', closeDrawer);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDrawer();
        });
    });
</script>
