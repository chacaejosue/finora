<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Gestión de Usuarios | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Gestión de Usuarios</span>
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

        @if ($errors->has('error'))
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 flex items-center justify-between text-red-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span>{{ $errors->first('error') }}</span>
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy">Gestión de Usuarios</h1>
                <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-1">
                    Administra los usuarios del sistema, sus credenciales y asignación de roles.
                </p>
            </div>
            <div>
                <a href="{{ route('panel.usuarios.create') }}" class="finora-gradient-btn px-4 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">manage_accounts</span>
                    Nuevo Usuario
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('panel.usuarios.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label for="search" class="block text-xs font-bold text-finora-navy mb-1">Buscar por nombre o email</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ej. Carlos o admin@finora.com" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
            </div>
            <div>
                <label for="rol" class="block text-xs font-bold text-finora-navy mb-1">Rol</label>
                <select id="rol" name="rol" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    <option value="">Todos los roles</option>
                    <option value="CONSULTOR" {{ request('rol') === 'CONSULTOR' ? 'selected' : '' }}>CONSULTOR</option>
                    <option value="COLABORADOR" {{ request('rol') === 'COLABORADOR' ? 'selected' : '' }}>COLABORADOR</option>
                    <option value="CLIENTE" {{ request('rol') === 'CLIENTE' ? 'selected' : '' }}>CLIENTE</option>
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
                            <th class="py-3.5 px-4">Usuario / Persona</th>
                            <th class="py-3.5 px-4">Email / Login</th>
                            <th class="py-3.5 px-4">Rol Asignado</th>
                            <th class="py-3.5 px-4">Último Acceso</th>
                            <th class="py-3.5 px-4 text-center">Estado</th>
                            <th class="py-3.5 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-finora-navy">
                        @forelse ($usuarios as $usr)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-bold text-sm text-finora-navy">
                                    {{ $usr->persona->nombre }} {{ $usr->persona->apellido }}
                                    @if (auth()->id() === $usr->id)
                                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full ml-1">(Tú)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $usr->persona->email }}
                                </td>
                                <td class="py-3 px-4 font-bold">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-100">
                                        {{ $usr->rol }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $usr->ultimo_acceso ? $usr->ultimo_acceso->format('d/m/Y H:i') : 'Sin registros' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if (auth()->id() !== $usr->id)
                                        <form action="{{ route('panel.usuarios.toggle', $usr) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="cursor-pointer px-2.5 py-1 rounded-full text-[10px] font-bold border transition-colors {{ $usr->activo ? 'bg-emerald-50 border-emerald-200 text-emerald-800 hover:bg-emerald-100' : 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100' }}">
                                                {{ $usr->activo ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-800">
                                            Activo
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('panel.usuarios.edit', $usr) }}" class="p-1.5 text-slate-600 hover:text-finora-blue hover:bg-blue-50 rounded-lg transition-colors inline-flex items-center" title="Editar usuario">
                                        <span class="material-symbols-outlined text-base">edit</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-finora-subtle">
                                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">no_accounts</span>
                                    <span>No se encontraron usuarios registrados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($usuarios->hasPages())
                <div class="p-4 border-t border-slate-200/80 bg-[#F8FAFC]">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('partials.panel-footer')
</body>
</html>
