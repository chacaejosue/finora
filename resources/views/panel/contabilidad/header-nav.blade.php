<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-100 rounded-3xl p-6 shadow-soft">
    <div>
        <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-cyan-50 text-[#00a3bd]">
                <span class="material-symbols-outlined text-xs">account_balance</span>
                Contabilidad y Reportes
            </span>
            <span class="text-xs font-medium text-slate-400">Ciclo Contable Integrado (Bolivia &bull; Ley 843)</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-[#0e1b2e] tracking-tight">
            {{ $titulo ?? 'Módulo Contable' }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $subtitulo ?? 'Gestión del ciclo contable completo, libro diario, mayores y estados financieros.' }}
        </p>
    </div>

    @if(!empty($accionBoton))
        <div class="shrink-0">
            {!! $accionBoton !!}
        </div>
    @endif
</div>
