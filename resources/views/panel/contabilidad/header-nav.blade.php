<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
    <div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-finora-blue">
                <span class="material-symbols-outlined text-xs">account_balance</span>
                ERP Finora
            </span>
            <span class="text-xs font-semibold text-slate-500">Normativa Contable y Tributaria de Bolivia (Ley 843)</span>
        </div>
        <h1 class="text-2xl font-heading font-extrabold text-finora-navy mt-1 tracking-tight">
            {{ $titulo ?? 'Módulo Contable' }}
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
            {{ $subtitulo ?? 'Gestión del ciclo contable completo, libro diario, mayores y estados financieros.' }}
        </p>
    </div>

    @if(!empty($accionBoton))
        <div>
            {!! $accionBoton !!}
        </div>
    @endif
</div>

<!-- Barra de Pestañas de Navegación del Ciclo Contable -->
<nav class="flex items-center gap-1.5 overflow-x-auto pb-3 mb-6 no-scrollbar text-xs font-bold border-b border-slate-100" aria-label="Secciones contables">
    <a href="{{ route('contabilidad.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.index') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">monitoring</span>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('contabilidad.balance-apertura') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.balance-apertura*') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">flag</span>
        <span>Balance de Apertura</span>
    </a>

    <a href="{{ route('contabilidad.libro-diario') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.libro-diario*') || request()->routeIs('contabilidad.asientos*') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">menu_book</span>
        <span>Libro Diario</span>
    </a>

    <a href="{{ route('contabilidad.libro-mayor') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.libro-mayor*') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">import_contacts</span>
        <span>Libro Mayor</span>
    </a>

    <a href="{{ route('contabilidad.sumas-saldos') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.sumas-saldos*') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">balance</span>
        <span>Sumas y Saldos</span>
    </a>

    <a href="{{ route('contabilidad.estados-financieros') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.estados-financieros*') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">query_stats</span>
        <span>Estados Financieros</span>
    </a>

    <a href="{{ route('contabilidad.kardex-valorado') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.kardex-valorado*') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">receipt_long</span>
        <span>Kardex Valorado (CPP)</span>
    </a>

    <a href="{{ route('contabilidad.plan-cuentas') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl transition-all whitespace-nowrap {{ request()->routeIs('contabilidad.plan-cuentas*') ? 'bg-finora-blue text-white shadow-sm' : 'text-slate-600 hover:text-finora-navy hover:bg-slate-100' }}">
        <span class="material-symbols-outlined text-sm">account_tree</span>
        <span>Plan de Cuentas</span>
    </a>
</nav>
