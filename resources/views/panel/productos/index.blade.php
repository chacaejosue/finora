<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Gestión de Productos | Panel del Consultor</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>

    <!-- Tipografías de Google -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

    <!-- Iconos Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.index') }}">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Gestión de Productos</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-3">
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Notificación de éxito -->
        @if (session('exito'))
            <div role="alert" class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center justify-between text-emerald-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span>{{ session('exito') }}</span>
                </div>
            </div>
        @endif

        <!-- Banner de gestión y enlaces maestros -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy">Catálogo de Productos</h1>
                <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-1">
                    Administra las fichas de productos, sus precios, imágenes y visibilidad en el catálogo público.
                </p>
            </div>

            @if(auth()->user()?->rol === 'CONSULTOR')
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('panel.proveedores.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 hover:border-finora-blue text-finora-navy transition-colors">
                        Proveedores
                    </a>
                    <a href="{{ route('panel.categorias.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-200 hover:border-finora-blue text-finora-navy transition-colors">
                        Categorías
                    </a>
                    <a href="{{ route('panel.productos.create') }}" class="finora-gradient-btn px-4 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Nuevo Producto
                    </a>
                </div>
            @endif
        </div>

        <!-- Filtros y Búsqueda -->
        <form method="GET" action="{{ route('panel.productos.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Buscador -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-bold text-finora-navy mb-1">Buscar por código o nombre</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ej. NAT-ILIA-001 o Kaiak" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
            </div>

            <!-- Filtro Categoría -->
            <div>
                <label for="categoria_id" class="block text-xs font-bold text-finora-navy mb-1">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro Estado Publicado -->
            <div>
                <label for="publicado" class="block text-xs font-bold text-finora-navy mb-1">Catálogo Público</label>
                <select id="publicado" name="publicado" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('publicado') === '1' ? 'selected' : '' }}>Publicados</option>
                    <option value="0" {{ request('publicado') === '0' ? 'selected' : '' }}>Borradores (Ocultos)</option>
                </select>
            </div>

            <!-- Botón Filtrar -->
            <div class="flex gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-finora-navy text-white text-xs font-semibold rounded-xl hover:bg-finora-dark transition-colors inline-flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">filter_list</span>
                    Filtrar
                </button>
                @if (request()->hasAny(['search', 'categoria_id', 'linea_id', 'publicado', 'activo']))
                    <a href="{{ route('panel.productos.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors inline-flex items-center justify-center" title="Limpiar filtros">
                        <span class="material-symbols-outlined text-sm">restart_alt</span>
                    </a>
                @endif
            </div>
        </form>

        <!-- Tabla de Productos -->
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-slate-200 text-finora-subtle uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4">Producto</th>
                            <th class="py-3.5 px-4">Código</th>
                            <th class="py-3.5 px-4">Categoría / Línea</th>
                            <th class="py-3.5 px-4">Proveedor</th>
                            <th class="py-3.5 px-4">Precio Venta</th>
                            <th class="py-3.5 px-4 text-center">Stock</th>
                            <th class="py-3.5 px-4 text-center">Publicado</th>
                            <th class="py-3.5 px-4 text-center">Estado</th>
                            <th class="py-3.5 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-finora-navy">
                        @forelse ($productos as $prod)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-semibold flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200 flex items-center justify-center">
                                        @if ($prod->imagen_url)
                                            <img src="{{ $prod->imagen_url }}" alt="{{ $prod->nombre }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-slate-400 text-lg">image</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-finora-navy">{{ $prod->nombre }}</span>
                                        @if ($prod->descripcion)
                                            <span class="text-[11px] text-finora-subtle truncate max-w-xs">{{ $prod->descripcion }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600 font-bold">
                                    {{ $prod->codigo }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold">{{ $prod->categoria->nombre }}</span>
                                        <span class="text-[10px] text-finora-subtle">{{ $prod->linea ? $prod->linea->nombre : 'Sin línea' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $prod->proveedor->nombre }}
                                </td>
                                <td class="py-3 px-4 font-bold text-finora-navy">
                                    @money($prod->precio_venta_actual)
                                </td>
                                <td class="py-3 px-4 text-center font-bold {{ $prod->stock_disponible > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                    {{ $prod->stock_disponible }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('panel.productos.toggle', $prod) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="campo" value="publicado">
                                        <button type="submit" class="cursor-pointer px-2.5 py-1 rounded-full text-[10px] font-bold border transition-colors {{ $prod->publicado ? 'bg-cyan-50 border-cyan-200 text-cyan-800 hover:bg-cyan-100' : 'bg-slate-100 border-slate-200 text-slate-500 hover:bg-slate-200' }}">
                                            {{ $prod->publicado ? 'Visible en Web' : 'Borrador' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('panel.productos.toggle', $prod) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="campo" value="activo">
                                        <button type="submit" class="cursor-pointer px-2.5 py-1 rounded-full text-[10px] font-bold border transition-colors {{ $prod->activo ? 'bg-emerald-50 border-emerald-200 text-emerald-800 hover:bg-emerald-100' : 'bg-red-50 border-red-200 text-red-700 hover:bg-red-100' }}">
                                            {{ $prod->activo ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('panel.productos.edit', $prod) }}" class="p-1.5 text-slate-600 hover:text-finora-blue hover:bg-blue-50 rounded-lg transition-colors" title="Editar producto">
                                            <span class="material-symbols-outlined text-base">edit</span>
                                        </a>
                                        <form action="{{ route('panel.productos.destroy', $prod) }}" method="POST" class="inline" onsubmit="return confirm('¿Deseas desactivar este producto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Desactivar producto">
                                                <span class="material-symbols-outlined text-base">power_settings_new</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-finora-subtle">
                                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">inventory_2</span>
                                    <span>No se encontraron productos registrados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($productos->hasPages())
                <div class="p-4 border-t border-slate-200/80 bg-[#F8FAFC]">
                    {{ $productos->links() }}
                </div>
            @endif
        </div>

    </main>

    @include('partials.panel-footer')
</body>
</html>
