<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Gestión de Pagos y Créditos | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Créditos y Pagos</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        @if (session('exito'))
            <div role="alert" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center justify-between text-emerald-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span>{{ session('exito') }}</span>
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy">Finanzas y Cobranzas</h1>
                <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-1">
                    Control de abonos de clientes, saldos pendientes y estado de cuentas corrientes.
                </p>
            </div>
            <div>
                <a href="{{ route('panel.pagos.create') }}" class="finora-gradient-btn px-4 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">payments</span>
                    Registrar Nuevo Pago
                </a>
            </div>
        </div>

        @if ($clientesDeudores->count() > 0)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-heading font-bold text-sm text-finora-navy">Cuentas Pendientes de Cobro por Cliente</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($clientesDeudores as $cli)
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-xs text-finora-navy block">{{ $cli->persona->nombre }} {{ $cli->persona->apellido }}</span>
                                <span class="text-[10px] text-finora-subtle">{{ $cli->persona->telefono ?? 'Sin celular' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-extrabold text-red-600 block">@money($cli->total_deuda)</span>
                                <a href="{{ route('panel.clientes.show', $cli) }}" class="text-[10px] font-bold text-finora-blue hover:underline">Ver ficha</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-100 bg-[#F8FAFC]">
                <h3 class="font-heading font-bold text-sm text-finora-navy">Historial General de Pagos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-slate-200 text-finora-subtle uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4">Fecha</th>
                            <th class="py-3.5 px-4">Venta Referencia</th>
                            <th class="py-3.5 px-4">Cliente</th>
                            <th class="py-3.5 px-4">Método de Pago</th>
                            <th class="py-3.5 px-4 text-right">Monto Registrado</th>
                            <th class="py-3.5 px-4">Observación</th>
                            <th class="py-3.5 px-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-finora-navy">
                        @forelse ($pagos as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-mono text-slate-600 font-bold">
                                    {{ $p->fecha ? $p->fecha->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-bold">
                                    <a href="{{ route('panel.ventas.show', $p->venta_id) }}" class="text-finora-blue hover:underline">
                                        Venta #{{ $p->venta_id }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 font-bold">
                                    {{ $p->venta->cliente->persona->nombre }} {{ $p->venta->cliente->persona->apellido }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-700">
                                    {{ $p->metodo }}
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold text-emerald-700 text-sm">
                                    @money($p->monto)
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $p->observacion ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->estado === 'REGISTRADO' ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700' }}">
                                        {{ $p->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-finora-subtle">
                                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">payments</span>
                                    <span>No se encontraron pagos registrados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pagos->hasPages())
                <div class="p-4 border-t border-slate-200/80 bg-[#F8FAFC]">
                    {{ $pagos->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('partials.panel-footer')
</body>
</html>
