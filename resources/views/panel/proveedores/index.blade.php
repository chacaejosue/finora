<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Gestión de Proveedores | Panel del Consultor</title>

    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.productos.index') }}">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Proveedores Comerciales</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('panel.productos.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Productos</span>
                </a>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulario de Alta -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm h-fit">
                <h2 class="font-heading text-lg font-bold text-finora-navy mb-4">Registrar Proveedor</h2>

                <form action="{{ route('panel.proveedores.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="nombre" class="block text-xs font-bold text-finora-navy mb-1">Nombre del Proveedor *</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Natura Cosméticos" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    </div>

                    <div>
                        <label for="telefono" class="block text-xs font-bold text-finora-navy mb-1">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="+56 9 1234 5678" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-finora-navy mb-1">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="proveedor@email.com" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    </div>

                    <div>
                        <label for="sitio_web" class="block text-xs font-bold text-finora-navy mb-1">Sitio Web</label>
                        <input type="url" id="sitio_web" name="sitio_web" value="{{ old('sitio_web') }}" placeholder="https://..." class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    </div>

                    <button type="submit" class="finora-gradient-btn w-full py-2.5 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn cursor-pointer">
                        Guardar Proveedor
                    </button>
                </form>
            </div>

            <!-- Tabla de Proveedores -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
                <div class="p-4 bg-[#F8FAFC] border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-heading text-sm font-bold text-finora-navy">Proveedores Registrados</h2>
                    <span class="text-xs text-finora-subtle font-medium">{{ $proveedores->total() }} registros</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F8FAFC] border-b border-slate-200 text-finora-subtle uppercase tracking-wider font-bold">
                            <tr>
                                <th class="py-3 px-4">Nombre</th>
                                <th class="py-3 px-4">Contacto</th>
                                <th class="py-3 px-4 text-center">Productos</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-finora-navy">
                            @forelse ($proveedores as $prov)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-finora-navy">
                                        {{ $prov->nombre }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">
                                        <div>{{ $prov->email ?: '-' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $prov->telefono ?: '' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold">
                                        {{ $prov->productos_count }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $prov->activo ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700' }}">
                                            {{ $prov->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <form action="{{ route('panel.proveedores.destroy', $prov) }}" method="POST" class="inline" onsubmit="return confirm('¿Desactivar proveedor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded cursor-pointer" title="Desactivar">
                                                <span class="material-symbols-outlined text-base">power_settings_new</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-finora-subtle">No hay proveedores registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($proveedores->hasPages())
                    <div class="p-3 border-t border-slate-200">
                        {{ $proveedores->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    @include('partials.panel-footer')
</body>
</html>
