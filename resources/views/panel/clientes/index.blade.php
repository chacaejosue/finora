<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Gestión de Clientes | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Gestión de Clientes</span>
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
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy">Directorio de Clientes</h1>
                <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-1">
                    Administra tus clientes, sus datos de contacto y revisa sus estados de cuenta.
                </p>
            </div>
            <div>
                <a href="{{ route('panel.clientes.create') }}" class="finora-gradient-btn px-4 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    Nuevo Cliente
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('panel.clientes.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div class="sm:col-span-2">
                <label for="search" class="block text-xs font-bold text-finora-navy mb-1">Buscar por nombre, email o teléfono</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ej. Ana María o 70001122" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-finora-navy text-white text-xs font-semibold rounded-xl hover:bg-finora-dark transition-colors inline-flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">search</span>
                    Buscar
                </button>
                @if (request()->hasAny(['search', 'activo']))
                    <a href="{{ route('panel.clientes.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors inline-flex items-center justify-center" title="Limpiar filtro">
                        <span class="material-symbols-outlined text-sm">restart_alt</span>
                    </a>
                @endif
            </div>
        </form>

        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-slate-200 text-finora-subtle uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4">Cliente / Persona</th>
                            <th class="py-3.5 px-4">Teléfono</th>
                            <th class="py-3.5 px-4">Correo Electrónico</th>
                            <th class="py-3.5 px-4">Dirección</th>
                            <th class="py-3.5 px-4 text-center">Estado</th>
                            <th class="py-3.5 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-finora-navy">
                        @forelse ($clientes as $cli)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-bold text-sm text-finora-navy">
                                    <div class="flex flex-col gap-1 items-start">
                                        <a href="{{ route('panel.clientes.show', $cli) }}" class="hover:text-finora-blue">
                                            {{ $cli->persona->nombre }} {{ $cli->persona->apellido }}
                                        </a>
                                        @if ($cli->estado_crediticio === 'MOROSO')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-100 text-red-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Moroso ({{ $cli->cuotas_vencidas_count }} vencidas · Bs {{ number_format($cli->saldo_deudor, 2) }})
                                            </span>
                                        @elseif ($cli->estado_crediticio === 'CON_DEUDA')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Con saldo (Bs {{ number_format($cli->saldo_deudor, 2) }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Al día
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $cli->persona->telefono ?? 'Sin teléfono' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $cli->persona->email ?? 'Sin correo' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $cli->persona->direccion ?? 'Sin dirección' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('panel.clientes.toggle', $cli) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="cursor-pointer px-2.5 py-1 rounded-full text-[10px] font-bold border transition-colors {{ $cli->activo ? 'bg-emerald-50 border-emerald-200 text-emerald-800 hover:bg-emerald-100' : 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100' }}">
                                            {{ $cli->activo ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('panel.clientes.show', $cli) }}" class="p-1.5 text-slate-600 hover:text-finora-blue hover:bg-blue-50 rounded-lg transition-colors" title="Ver detalle">
                                            <span class="material-symbols-outlined text-base">visibility</span>
                                        </a>
                                        <a href="{{ route('panel.clientes.edit', $cli) }}" class="p-1.5 text-slate-600 hover:text-finora-blue hover:bg-blue-50 rounded-lg transition-colors" title="Editar cliente">
                                            <span class="material-symbols-outlined text-base">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-finora-subtle">
                                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">group_off</span>
                                    <span>No se encontraron clientes registrados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($clientes->hasPages())
                <div class="p-4 border-t border-slate-200/80 bg-[#F8FAFC]">
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('partials.panel-footer')
</body>
</html>
