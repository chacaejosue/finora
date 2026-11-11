<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Editar Producto | Panel del Consultor</title>

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

            <!-- Precio Catálogo, Nivel de Consultora y Costo a Pagar -->
            <div class="rounded-2xl border border-blue-100 bg-blue-50/40 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-blue-100/70 pb-3">
                    <div>
                        <h3 class="font-heading font-bold text-sm text-finora-navy flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-finora-blue">calculate</span>
                            Calculadora de Nivel Natura y Costo Consultora
                        </h3>
                        <p class="text-[11px] text-finora-subtle">Ingresa el precio oficial que aparece en la revista y elige tu nivel para calcular automáticamente cuánto le pagas a Natura y tu ganancia.</p>
                    </div>
                    <div id="badge-ganancia-live" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold shrink-0">
                        <span class="material-symbols-outlined text-sm">trending_up</span>
                        <span id="texto-ganancia-live">Ganancia: Bs 0.00 (0%)</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Precio de venta actual al cliente (Revista) -->
                    <div>
                        <label for="precio_venta_actual" class="block text-xs font-bold text-finora-navy mb-1.5">Precio Oficial en Revista (Bs) *</label>
                        <input type="number" step="0.01" min="0" id="precio_venta_actual" name="precio_venta_actual" value="{{ old('precio_venta_actual', $producto->precio_venta_actual) }}" required placeholder="Ej. 610.00" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none font-bold text-finora-blue">
                        <p class="text-[10px] text-slate-500 mt-1">Precio oficial al público según catálogo Natura.</p>
                    </div>

                    <!-- Costo Referencial / Actual (Bs) -->
                    <div>
                        <label for="costo_promedio" class="block text-xs font-bold text-finora-navy mb-1.5">Tu Costo Natura a Pagar (Bs)</label>
                        <input type="number" step="0.01" min="0" id="costo_promedio" name="costo_promedio" value="{{ old('costo_promedio', $producto->costo_promedio) }}" placeholder="Ej. 427.00" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none font-semibold">
                        <p class="text-[10px] text-slate-500 mt-1">Lo que te factura Natura con tu descuento de nivel (CPP actual).</p>
                    </div>
                </div>

                <!-- Botones de descuento por nivel oficial de consultora -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="text-[11px] font-bold text-slate-600 mr-1">Calcular con tu nivel Natura:</span>
                    <button type="button" class="btn-nivel px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-finora-blue hover:text-white hover:border-finora-blue text-xs font-bold text-slate-700 transition-colors cursor-pointer" data-descuento="25" title="25% de ganancia directa">-25% (Bronce)</button>
                    <button type="button" class="btn-nivel px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-finora-blue hover:text-white hover:border-finora-blue text-xs font-bold text-slate-700 transition-colors cursor-pointer" data-descuento="30" title="30% de ganancia directa">-30% (Plata)</button>
                    <button type="button" class="btn-nivel px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-finora-blue hover:text-white hover:border-finora-blue text-xs font-bold text-slate-700 transition-colors cursor-pointer" data-descuento="32" title="32% de ganancia directa">-32% (Oro)</button>
                    <button type="button" class="btn-nivel px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-finora-blue hover:text-white hover:border-finora-blue text-xs font-bold text-slate-700 transition-colors cursor-pointer" data-descuento="35" title="35% de ganancia directa">-35% (Diamante)</button>
                    <button type="button" class="btn-nivel px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-finora-blue hover:text-white hover:border-finora-blue text-xs font-bold text-slate-700 transition-colors cursor-pointer" data-descuento="37" title="37% de ganancia directa">-37% (Diamante Azul)</button>
                </div>
            </div>

            <!-- Sección de Oferta / Rebaja Promocional (Estilo Marketplace) -->
            @php
                $estaEnOferta = old('en_oferta', $producto->en_oferta ? '1' : '');
                $precioOfertaValor = old('precio_oferta', $producto->precio_oferta);
            @endphp
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200/80 pb-3">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="en_oferta" name="en_oferta" value="1" {{ $estaEnOferta ? 'checked' : '' }} class="h-4 w-4 rounded border-amber-400 text-amber-600 focus:ring-amber-500">
                        <span class="ml-2.5 font-heading font-bold text-sm text-amber-950 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-amber-600">local_fire_department</span>
                            Poner este producto en Oferta / Rebaja
                        </span>
                    </label>
                    <div id="badge-oferta-live" class="hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-100 text-amber-900 text-xs font-bold shrink-0">
                        <span class="material-symbols-outlined text-sm">sell</span>
                        <span id="texto-oferta-live">Ahorro: Bs 0.00 (0% OFF)</span>
                    </div>
                </div>

                <div id="panel-oferta-campos" class="{{ $estaEnOferta ? '' : 'hidden' }} space-y-3">
                    <p class="text-[11px] text-amber-900/80">
                        Al activar la oferta, en la tienda y catálogo público se mostrará el <strong>precio original tachado</strong> y el precio rebajado visible con su etiqueta de descuento.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="precio_oferta" class="block text-xs font-bold text-amber-950 mb-1.5">Precio de Oferta / Rebajado (Bs)</label>
                            <input type="number" step="0.01" min="0" id="precio_oferta" name="precio_oferta" value="{{ $precioOfertaValor }}" placeholder="Ej. 80.00" class="w-full bg-white border border-amber-300 rounded-xl px-3.5 py-2.5 text-xs text-amber-950 focus:ring-2 focus:ring-amber-500 outline-none font-bold text-amber-700">
                            <p class="text-[10px] text-amber-800 mt-1">Debe ser menor al precio regular de venta (<span id="ref-precio-regular" class="font-bold">Bs {{ number_format($producto->precio_venta_actual, 2) }}</span>).</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-amber-950 mb-1.5">Descuento rápido (% OFF):</span>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" class="btn-descuento px-3 py-1.5 rounded-lg border border-amber-300 bg-white hover:bg-amber-600 hover:text-white hover:border-amber-600 text-xs font-bold text-amber-900 transition-colors cursor-pointer" data-descuento="10">-10%</button>
                                <button type="button" class="btn-descuento px-3 py-1.5 rounded-lg border border-amber-300 bg-white hover:bg-amber-600 hover:text-white hover:border-amber-600 text-xs font-bold text-amber-900 transition-colors cursor-pointer" data-descuento="15">-15%</button>
                                <button type="button" class="btn-descuento px-3 py-1.5 rounded-lg border border-amber-300 bg-white hover:bg-amber-600 hover:text-white hover:border-amber-600 text-xs font-bold text-amber-900 transition-colors cursor-pointer" data-descuento="20">-20%</button>
                                <button type="button" class="btn-descuento px-3 py-1.5 rounded-lg border border-amber-300 bg-white hover:bg-amber-600 hover:text-white hover:border-amber-600 text-xs font-bold text-amber-900 transition-colors cursor-pointer" data-descuento="25">-25%</button>
                                <button type="button" class="btn-descuento px-3 py-1.5 rounded-lg border border-amber-300 bg-white hover:bg-amber-600 hover:text-white hover:border-amber-600 text-xs font-bold text-amber-900 transition-colors cursor-pointer" data-descuento="30">-30%</button>
                            </div>
                        </div>
                    </div>

                    <div id="resumen-visual-oferta" class="hidden p-3 bg-white/90 rounded-xl border border-amber-200 text-xs flex items-center justify-between">
                        <span class="text-slate-600 font-medium">Vista previa en la tienda:</span>
                        <div class="flex items-center gap-2">
                            <span id="preview-precio-tachado" class="text-xs text-slate-400 line-through">Bs 0.00</span>
                            <span id="preview-precio-oferta" class="text-sm font-black text-amber-600">Bs 0.00</span>
                            <span id="preview-badge-descuento" class="px-2 py-0.5 rounded-md bg-amber-500 text-white font-bold text-[10px]">-0%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carga de Imagen -->
            <div>
                <label for="imagen" class="block text-xs font-bold text-finora-navy mb-1.5">Cambiar Imagen (Archivo)</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-finora-blue hover:file:bg-blue-100 cursor-pointer">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Calculadora de nivel oficial Natura y ganancia
            const inputPrecio = document.getElementById('precio_venta_actual');
            const inputCosto = document.getElementById('costo_promedio');
            const textoGanancia = document.getElementById('texto-ganancia-live');
            const btnsNivel = document.querySelectorAll('.btn-nivel');

            function actualizarBadgeGanancia() {
                const precio = parseFloat(inputPrecio?.value) || 0;
                const costo = parseFloat(inputCosto?.value) || 0;
                if (precio > 0 && costo > 0) {
                    const ganancia = precio - costo;
                    const pctDesc = Math.round(((precio - costo) / precio) * 100);
                    textoGanancia.textContent = `Tu Ganancia: Bs ${ganancia.toFixed(2)} (${pctDesc}% desc. Natura)`;
                } else if (precio > 0 && costo === 0) {
                    textoGanancia.textContent = `Precio Revista: Bs ${precio.toFixed(2)}`;
                } else {
                    textoGanancia.textContent = 'Ganancia: Bs 0.00 (0%)';
                }
            }

            btnsNivel.forEach(btn => {
                btn.addEventListener('click', () => {
                    const pctDescuento = parseFloat(btn.getAttribute('data-descuento')) || 0;
                    const precioRevista = parseFloat(inputPrecio?.value) || 0;
                    if (precioRevista > 0) {
                        const costoCalculado = precioRevista * (1 - pctDescuento / 100);
                        inputCosto.value = costoCalculado.toFixed(2);
                        actualizarBadgeGanancia();
                    } else {
                        inputPrecio?.focus();
                    }
                });
            });

            inputCosto?.addEventListener('input', actualizarBadgeGanancia);
            inputPrecio?.addEventListener('input', () => {
                actualizarBadgeGanancia();
                actualizarOfertaLive();
            });
            actualizarBadgeGanancia();

            // Lógica de Oferta / Rebaja (Estilo Marketplace)
            const checkOferta = document.getElementById('en_oferta');
            const panelOferta = document.getElementById('panel-oferta-campos');
            const inputPrecioOferta = document.getElementById('precio_oferta');
            const badgeOfertaLive = document.getElementById('badge-oferta-live');
            const textoOfertaLive = document.getElementById('texto-oferta-live');
            const refPrecioRegular = document.getElementById('ref-precio-regular');
            const resumenOferta = document.getElementById('resumen-visual-oferta');
            const previewTachado = document.getElementById('preview-precio-tachado');
            const previewOferta = document.getElementById('preview-precio-oferta');
            const previewBadge = document.getElementById('preview-badge-descuento');
            const btnsDescuento = document.querySelectorAll('.btn-descuento');

            function actualizarOfertaLive() {
                const precioRegular = parseFloat(inputPrecio?.value) || 0;
                const precioOferta = parseFloat(inputPrecioOferta?.value) || 0;
                const isOferta = checkOferta?.checked;

                if (refPrecioRegular) {
                    refPrecioRegular.textContent = `Bs ${precioRegular.toFixed(2)}`;
                }

                if (!isOferta) {
                    panelOferta?.classList.add('hidden');
                    badgeOfertaLive?.classList.add('hidden');
                    resumenOferta?.classList.add('hidden');
                    return;
                }

                panelOferta?.classList.remove('hidden');

                if (precioRegular > 0 && precioOferta > 0 && precioOferta < precioRegular) {
                    const ahorro = precioRegular - precioOferta;
                    const pctDescuento = Math.round((ahorro / precioRegular) * 100);

                    badgeOfertaLive?.classList.remove('hidden');
                    textoOfertaLive.textContent = `Ahorro: Bs ${ahorro.toFixed(2)} (${pctDescuento}% OFF)`;

                    resumenOferta?.classList.remove('hidden');
                    previewTachado.textContent = `Bs ${precioRegular.toFixed(2)}`;
                    previewOferta.textContent = `Bs ${precioOferta.toFixed(2)}`;
                    previewBadge.textContent = `-${pctDescuento}%`;
                } else {
                    badgeOfertaLive?.classList.add('hidden');
                    resumenOferta?.classList.add('hidden');
                }
            }

            checkOferta?.addEventListener('change', () => {
                actualizarOfertaLive();
                if (checkOferta.checked && (!inputPrecioOferta.value || parseFloat(inputPrecioOferta.value) === 0)) {
                    inputPrecioOferta.focus();
                }
            });

            inputPrecioOferta?.addEventListener('input', actualizarOfertaLive);

            btnsDescuento.forEach(btn => {
                btn.addEventListener('click', () => {
                    const pct = parseFloat(btn.getAttribute('data-descuento')) || 0;
                    const precioRegular = parseFloat(inputPrecio?.value) || 0;
                    if (precioRegular > 0) {
                        const calculado = precioRegular * (1 - pct / 100);
                        inputPrecioOferta.value = calculado.toFixed(2);
                        if (!checkOferta.checked) {
                            checkOferta.checked = true;
                        }
                        actualizarOfertaLive();
                    } else {
                        inputPrecio?.focus();
                    }
                });
            });

            actualizarOfertaLive();
        });
    </script>
</body>
</html>
