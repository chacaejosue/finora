<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Editar Producto | Panel del Consultor</title>

    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="openPanelDrawerBtn" type="button" class="p-2 rounded-xl text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors cursor-pointer" title="Navegación de módulos">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.productos.index') }}">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Editar Producto</span>
                    </div>
                </a>
            </div>

            <a href="{{ route('panel.productos.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 px-3 py-1.5 rounded-xl transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span>Volver a la lista</span>
            </a>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6">
            <h1 class="font-heading text-2xl font-extrabold text-finora-navy">Editar Producto</h1>
            <p class="text-xs text-finora-subtle mt-1">Actualiza los datos del producto {{ $producto->codigo }}.</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
                <p class="text-xs font-bold text-red-700 mb-1">Se encontraron los siguientes errores:</p>
                <ul class="list-disc list-inside text-xs text-red-600 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('panel.productos.update', $producto) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 space-y-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Código -->
                <div>
                    <label for="codigo" class="block text-xs font-bold text-finora-navy mb-1.5">Código Único de Producto *</label>
                    <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $producto->codigo) }}" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                </div>

                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-xs font-bold text-finora-navy mb-1.5">Nombre Comercial *</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-xs font-bold text-finora-navy mb-1.5">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Proveedor -->
                <div>
                    <label for="proveedor_id" class="block text-xs font-bold text-finora-navy mb-1.5">Proveedor *</label>
                    <select id="proveedor_id" name="proveedor_id" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @foreach ($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('proveedor_id', $producto->proveedor_id) == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Categoría -->
                <div>
                    <label for="categoria_id" class="block text-xs font-bold text-finora-navy mb-1.5">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Marca / Línea -->
                <div>
                    <label for="marca_linea" class="block text-xs font-bold text-finora-navy mb-1.5">Marca / Línea (Opcional)</label>
                    <input type="text" id="marca_linea" name="marca_linea" value="{{ old('marca_linea', $producto->marca_linea) }}" placeholder="Ej. Ekos, Tododia, Samsung..." class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Precio de venta actual -->
                <div>
                    <label for="precio_venta_actual" class="block text-xs font-bold text-finora-navy mb-1.5">Precio de Venta Actual *</label>
                    <input type="number" step="0.01" min="0" id="precio_venta_actual" name="precio_venta_actual" value="{{ old('precio_venta_actual', $producto->precio_venta_actual) }}" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                </div>

                <!-- Carga de Imagen -->
                <div>
                    <label for="imagen" class="block text-xs font-bold text-finora-navy mb-1.5">Cambiar Imagen (Archivo)</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-finora-blue hover:file:bg-blue-100 cursor-pointer">
                </div>
            </div>

            <!-- Previsualización de imagen actual y URL -->
            <div>
                <label for="imagen_url" class="block text-xs font-bold text-finora-navy mb-1.5">O bien, URL de imagen externa</label>
            {{-- Solo se precarga la URL si es una URL externa. Las rutas de storage/demo
                 se muestran únicamente en la previsualización, no en este campo. --}}
            @php
                $imagenGuardada = $producto->imagen_principal ?? '';
                $valorUrlCampo  = old('imagen_url',
                    (str_starts_with($imagenGuardada, 'http://') || str_starts_with($imagenGuardada, 'https://'))
                        ? $imagenGuardada
                        : ''
                );
            @endphp
            <input type="url" id="imagen_url" name="imagen_url" value="{{ $valorUrlCampo }}" placeholder="https://ejemplo.com/imagen.jpg" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
            </div>

            @if ($producto->imagen_url)
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-3">
                    <img src="{{ $producto->imagen_url }}" alt="Actual" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                    <span class="text-xs text-finora-subtle">Imagen actual del producto</span>
                </div>
            @endif

            <!-- Interruptores -->
            <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-6">
                <label class="inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="publicado" value="1" {{ old('publicado', $producto->publicado) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-finora-blue focus:ring-finora-blue">
                    <span class="ml-2 text-xs font-bold text-finora-navy">Publicar en el catálogo público</span>
                </label>

                <label class="inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', $producto->activo) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-finora-blue focus:ring-finora-blue">
                    <span class="ml-2 text-xs font-bold text-finora-navy">Producto activo</span>
                </label>
            </div>

            <!-- Botones -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('panel.productos.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="finora-gradient-btn px-6 py-2.5 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn cursor-pointer">
                    Actualizar Producto
                </button>
            </div>
        </form>
    </main>

    @include('partials.panel-footer')
</body>
</html>
