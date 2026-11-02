<!-- Barra de navegación rápida y Drawer lateral de Finora Panel -->
<div id="panelDrawerOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden transition-opacity duration-300 opacity-0" aria-hidden="true"></div>

<aside id="panelDrawer" class="fixed inset-y-0 left-0 w-[min(18rem,calc(100vw-1rem))] bg-white z-50 shadow-2xl flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out border-r border-slate-200" aria-label="Navegación de módulos" aria-hidden="true">
    <!-- Encabezado del Drawer -->
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <a class="inline-flex items-center gap-3 group" href="{{ route('panel.index') }}">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-7 w-auto object-contain group-hover:scale-105 transition-transform"/>
            </div>
            <div class="flex flex-col">
                <span class="font-heading text-lg font-extrabold text-finora-navy">Finora</span>
                <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Panel Comercial</span>
            </div>
        </a>
        <button id="closePanelDrawerBtn" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-finora-navy hover:bg-slate-100 transition-colors cursor-pointer" aria-label="Cerrar navegación">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>

    <!-- Lista de Módulos Operativos -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-1" aria-label="Módulos del sistema">
        <a href="{{ route('panel.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel*index') && !request()->routeIs('panel.*') ? 'bg-finora-blue text-white shadow-sm' : 'text-finora-navy hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg">dashboard</span>
            <span>Inicio del Panel</span>
        </a>

        <div class="pt-3 pb-1.5 px-3.5">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Operaciones</span>
        </div>

        <a href="{{ route('panel.productos.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.productos*') ? 'bg-blue-50 text-finora-blue font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-blue-600">package_2</span>
            <span>Productos</span>
        </a>

        <a href="{{ route('panel.clientes.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.clientes*') ? 'bg-emerald-50 text-emerald-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-emerald-600">group</span>
            <span>Clientes</span>
        </a>

        <a href="{{ route('panel.pedidos.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.pedidos*') ? 'bg-purple-50 text-purple-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-purple-600">shopping_cart</span>
            <span>Pedidos</span>
        </a>

        <a href="{{ route('panel.compras.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.compras*') ? 'bg-cyan-50 text-cyan-800 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-cyan-600">shopping_bag</span>
            <span>Compras</span>
        </a>

        <a href="{{ route('panel.inventario.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.inventario*') ? 'bg-amber-50 text-amber-800 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-amber-600">inventory_2</span>
            <span>Inventario</span>
        </a>

        <a href="{{ route('panel.ventas.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.ventas*') ? 'bg-rose-50 text-rose-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-rose-600">point_of_sale</span>
            <span>Ventas</span>
        </a>

        <a href="{{ route('panel.pagos.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.pagos*') ? 'bg-indigo-50 text-indigo-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-indigo-600">account_balance</span>
            <span>Créditos y Pagos</span>
        </a>

        @if(auth()->check() && auth()->user()->rol === 'CONSULTOR')
            <a href="{{ route('panel.reportes.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all dark:text-slate-200 {{ request()->routeIs('panel.reportes*') ? 'bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-200 font-extrabold' : 'text-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-lg text-teal-600 dark:text-teal-300">analytics</span>
                <span>Reportes</span>
            </a>
        @endif

        <div class="pt-3 pb-1.5 px-3.5">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Contabilidad (Bolivia)</span>
        </div>

        <a href="{{ route('contabilidad.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.index') ? 'bg-blue-50 text-finora-blue font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-blue-600">monitoring</span>
            <span>Panel Contable</span>
        </a>

        <a href="{{ route('contabilidad.balance-apertura') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.balance-apertura*') ? 'bg-emerald-50 text-emerald-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-emerald-600">flag</span>
            <span>Balance de Apertura</span>
        </a>

        <a href="{{ route('contabilidad.libro-diario') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.libro-diario*') || request()->routeIs('contabilidad.asientos*') ? 'bg-indigo-50 text-indigo-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-indigo-600">menu_book</span>
            <span>Libro Diario</span>
        </a>

        <a href="{{ route('contabilidad.libro-mayor') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.libro-mayor*') ? 'bg-purple-50 text-purple-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-purple-600">import_contacts</span>
            <span>Libro Mayor</span>
        </a>

        <a href="{{ route('contabilidad.sumas-saldos') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.sumas-saldos*') ? 'bg-amber-50 text-amber-800 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-amber-600">balance</span>
            <span>Sumas y Saldos</span>
        </a>

        <a href="{{ route('contabilidad.estados-financieros') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.estados-financieros*') ? 'bg-rose-50 text-rose-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-rose-600">query_stats</span>
            <span>Estados Financieros</span>
        </a>

        <a href="{{ route('contabilidad.kardex-valorado') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.kardex-valorado*') ? 'bg-cyan-50 text-cyan-800 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-cyan-600">receipt_long</span>
            <span>Kardex Valorado (CPP)</span>
        </a>

        <a href="{{ route('contabilidad.plan-cuentas') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contabilidad.plan-cuentas*') ? 'bg-teal-50 text-teal-700 font-extrabold' : 'text-slate-700 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-teal-600">account_tree</span>
            <span>Plan de Cuentas y Modelos</span>
        </a>

        <div class="pt-3 pb-1.5 px-3.5">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Configuración</span>
        </div>

        <a href="{{ route('panel.proveedores.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.proveedores*') ? 'bg-slate-100 text-finora-navy' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-slate-500">storefront</span>
            <span>Proveedores</span>
        </a>

        <a href="{{ route('panel.categorias.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.categorias*') ? 'bg-slate-100 text-finora-navy' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-slate-500">category</span>
            <span>Categorías</span>
        </a>

        <a href="{{ route('panel.lineas.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.lineas*') ? 'bg-slate-100 text-finora-navy' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-lg text-slate-500">style</span>
            <span>Líneas</span>
        </a>

        @if(auth()->check() && auth()->user()->rol === 'CONSULTOR')
            <a href="{{ route('panel.usuarios.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('panel.usuarios*') ? 'bg-slate-100 text-finora-navy' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-lg text-slate-500">manage_accounts</span>
                <span>Usuarios</span>
            </a>
        @endif
    </nav>

    <!-- Pie del Drawer: Catálogo público y Cerrar sesión -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/60 space-y-2">
        <a href="{{ route('landing') }}" target="_blank" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-finora-blue hover:bg-white border border-transparent hover:border-slate-200 transition-all">
            <span class="inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-base">store</span>
                <span>Ver Catálogo Público</span>
            </span>
            <span class="material-symbols-outlined text-xs">open_in_new</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
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
