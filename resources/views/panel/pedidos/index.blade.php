<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Gestión de Pedidos | Panel del Consultor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Gestión de Pedidos</span>
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
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy">Pedidos de Clientes</h1>
                <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-1">
                    Gestiona solicitudes de clientes, reservación de inventario y preparación de entregas. Después puedes convertir un pedido en venta.
                </p>
            </div>
            <div>
                <a href="{{ route('panel.pedidos.create') }}" class="finora-gradient-btn px-4 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">shopping_bag</span>
                    Nuevo Pedido
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('panel.pedidos.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
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
                <label for="estado" class="block text-xs font-bold text-finora-navy mb-1">Estado</label>
                <select id="estado" name="estado" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    <option value="">Todos los estados</option>
                    <option value="PENDIENTE" {{ request('estado') === 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
                    <option value="RESERVADO" {{ request('estado') === 'RESERVADO' ? 'selected' : '' }}>RESERVADO</option>
                    <option value="PENDIENTE_ABASTECIMIENTO" {{ request('estado') === 'PENDIENTE_ABASTECIMIENTO' ? 'selected' : '' }}>PENDIENTE_ABASTECIMIENTO</option>
                    <option value="LISTO_ENTREGA" {{ request('estado') === 'LISTO_ENTREGA' ? 'selected' : '' }}>LISTO_ENTREGA</option>
                    <option value="COMPLETADO" {{ request('estado') === 'COMPLETADO' ? 'selected' : '' }}>COMPLETADO</option>
                    <option value="CANCELADO" {{ request('estado') === 'CANCELADO' ? 'selected' : '' }}>CANCELADO</option>
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
                            <th class="py-3.5 px-4"># Pedido</th>
                            <th class="py-3.5 px-4">Cliente</th>
                            <th class="py-3.5 px-4">Fecha</th>
                            <th class="py-3.5 px-4 text-center">Estado</th>
                            <th class="py-3.5 px-4 text-center">Reserva Stock</th>
                            <th class="py-3.5 px-4 text-right">Total</th>
                            <th class="py-3.5 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pedidosTableBody" class="divide-y divide-slate-100 text-finora-navy">
                        @forelse ($pedidos as $ped)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-bold text-sm text-finora-navy">
                                    <a href="{{ route('panel.pedidos.show', $ped) }}" class="hover:text-finora-blue">
                                        #{{ $ped->id }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 font-bold">
                                    {{ $ped->cliente->persona->nombre }} {{ $ped->cliente->persona->apellido }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $ped->fecha ? $ped->fecha->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $badgeStyle = match($ped->estado) {
                                            'COMPLETADO' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                                            'LISTO_ENTREGA' => 'bg-cyan-50 border-cyan-200 text-cyan-800',
                                            'RESERVADO' => 'bg-blue-50 border-blue-200 text-blue-800',
                                            'PENDIENTE_ABASTECIMIENTO' => 'bg-amber-50 border-amber-200 text-amber-800',
                                            'CANCELADO' => 'bg-red-50 border-red-200 text-red-700',
                                            default => 'bg-slate-100 border-slate-200 text-slate-600',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeStyle }} dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100">
                                        {{ $ped->estado }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $totalItems = $ped->detalles->sum('cantidad');
                                        $totalReservado = $ped->detalles->sum('cantidad_reservada');
                                    @endphp
                                    @if ($ped->estado === 'COMPLETADO')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                            <span class="material-symbols-outlined text-xs">check_circle</span> Entregado
                                        </span>
                                    @elseif ($ped->estado === 'CANCELADO')
                                        <span class="text-[11px] text-slate-400">Liberado</span>
                                    @elseif ($totalReservado >= $totalItems && $totalItems > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 border border-cyan-200 text-cyan-800 dark:bg-cyan-950/70 dark:border-cyan-700 dark:text-cyan-200">
                                            <span class="material-symbols-outlined text-xs">done_all</span> 100% ({{ $totalReservado }}/{{ $totalItems }})
                                        </span>
                                    @elseif ($totalReservado > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 border border-amber-200 text-amber-800 dark:bg-amber-950/70 dark:border-amber-700 dark:text-amber-200">
                                            <span class="material-symbols-outlined text-xs">hourglass_top</span> Parcial ({{ $totalReservado }}/{{ $totalItems }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 border border-red-200 text-red-700 dark:bg-red-950/70 dark:border-red-700 dark:text-red-200">
                                            <span class="material-symbols-outlined text-xs">block</span> Sin stock (0/{{ $totalItems }})
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-finora-navy">
                                    @money($ped->total)
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('panel.pedidos.show', $ped) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-[#00c1de] hover:text-white rounded-xl transition-all shadow-xs" title="Ver y gestionar pedido">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        <span>Ver pedido</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-finora-subtle">
                                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">remove_shopping_cart</span>
                                    <span>No se encontraron pedidos registrados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pedidos->hasPages())
                <div class="p-4 border-t border-slate-200/80 bg-[#F8FAFC]">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('partials.panel-footer')
</body>
</html>
