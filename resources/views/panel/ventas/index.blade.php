<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Gestión de Ventas | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Gestión de Ventas</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
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

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy">Registro de Ventas</h1>
                <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-1">
                    Registra ventas directas o ventas vinculadas a pedidos; al confirmarlas se descuenta el inventario.
                </p>
            </div>
            <div>
                <a href="{{ route('panel.ventas.create') }}" class="finora-gradient-btn px-4 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">point_of_sale</span>
                    Nueva Venta
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('panel.ventas.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 mb-6 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label for="cliente_id" class="block text-xs font-bold text-finora-navy mb-1">Cliente</label>
                <select id="cliente_id" name="cliente_id" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    <option value="">Todos los clientes</option>
                    @foreach ($clientes as $cli)
                        <option value="{{ $cli->id }}" {{ request('cliente_id') == $cli->id ? 'selected' : '' }}>{{ $cli->persona->nombre }} {{ $cli->persona->apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="forma_pago" class="block text-xs font-bold text-finora-navy mb-1">Forma de Pago</label>
                <select id="forma_pago" name="forma_pago" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    <option value="">Todas las formas</option>
                    <option value="CONTADO" {{ request('forma_pago') === 'CONTADO' ? 'selected' : '' }}>CONTADO</option>
                    <option value="CREDITO" {{ request('forma_pago') === 'CREDITO' ? 'selected' : '' }}>CREDITO</option>
                </select>
            </div>
            <div>
                <label for="estado" class="block text-xs font-bold text-finora-navy mb-1">Estado</label>
                <select id="estado" name="estado" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    <option value="">Todos los estados</option>
                    <option value="BORRADOR" {{ request('estado') === 'BORRADOR' ? 'selected' : '' }}>BORRADOR</option>
                    <option value="CONFIRMADA" {{ request('estado') === 'CONFIRMADA' ? 'selected' : '' }}>CONFIRMADA</option>
                    <option value="ANULADA" {{ request('estado') === 'ANULADA' ? 'selected' : '' }}>ANULADA</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-finora-navy text-white text-xs font-semibold rounded-xl hover:bg-finora-dark transition-colors inline-flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">filter_list</span>
                    Filtrar
                </button>
            </div>
        </form>

        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-slate-200 text-finora-subtle uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4"># Venta</th>
                            <th class="py-3.5 px-4">Cliente</th>
                            <th class="py-3.5 px-4">Forma de Pago</th>
                            <th class="py-3.5 px-4">Fecha</th>
                            <th class="py-3.5 px-4">Origen</th>
                            <th class="py-3.5 px-4 text-center">Estado</th>
                            <th class="py-3.5 px-4 text-right">Total</th>
                            <th class="py-3.5 px-4 text-right">Saldo Pendiente</th>
                            <th class="py-3.5 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-finora-navy">
                        @forelse ($ventas as $vnt)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-bold text-sm text-finora-navy">
                                    <a href="{{ route('panel.ventas.show', $vnt) }}" class="hover:text-finora-blue">
                                        #{{ $vnt->id }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 font-bold">
                                    {{ $vnt->cliente->persona->nombre }} {{ $vnt->cliente->persona->apellido }}
                                </td>
                                <td class="py-3 px-4 font-bold">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] {{ $vnt->forma_pago === 'CREDITO' ? 'bg-purple-50 text-purple-800 border border-purple-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                        {{ $vnt->forma_pago }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $vnt->fecha ? $vnt->fecha->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if ($vnt->pedido)
                                        <a href="{{ route('panel.pedidos.show', $vnt->pedido) }}" class="inline-flex rounded-full border border-purple-200 bg-purple-50 px-2.5 py-1 text-[10px] font-bold text-purple-800 hover:bg-purple-100 dark:border-purple-700 dark:bg-purple-950/70 dark:text-purple-200">Pedido #{{ $vnt->pedido->id }}</a>
                                    @else
                                        <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-800 dark:border-blue-700 dark:bg-blue-950/70 dark:text-blue-200">Directa</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $badgeStyle = match($vnt->estado) {
                                            'CONFIRMADA' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                                            'ANULADA' => 'bg-red-50 border-red-200 text-red-700',
                                            default => 'bg-slate-100 border-slate-200 text-slate-600',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeStyle }}">
                                        {{ $vnt->estado }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-finora-navy">
                                    @money($vnt->total)
                                </td>
                                <td class="py-3 px-4 text-right font-bold {{ $vnt->saldo_pendiente > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                    @money($vnt->saldo_pendiente)
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        @if ($vnt->estado === 'BORRADOR')
                                            <a href="{{ route('panel.ventas.edit', $vnt) }}" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors inline-flex items-center" title="Editar borrador">
                                                <span class="material-symbols-outlined text-base">edit</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('panel.ventas.show', $vnt) }}" class="p-1.5 text-slate-600 hover:text-finora-blue hover:bg-blue-50 rounded-lg transition-colors inline-flex items-center" title="Ver detalle">
                                            <span class="material-symbols-outlined text-base">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-finora-subtle">
                                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">point_of_sale</span>
                                    <span>No se encontraron ventas registradas.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($ventas->hasPages())
                <div class="p-4 border-t border-slate-200/80 bg-[#F8FAFC]">
                    {{ $ventas->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('partials.panel-footer')
</body>
</html>
