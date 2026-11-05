<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo por Categorías - Finora | Belleza que Inspira</title>

    <!-- Favicon de Finora -->
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}">

    <!-- Tipografía: Plus Jakarta Sans (Mismo diseño de la nueva Landing) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Forms & Container Queries (Misma configuración visual de la Landing) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        finora: {
                            cyan: '#00c1de',
                            cyanDark: '#00a3bd',
                            cyanLight: '#e0f8fc',
                            blueDark: '#0e1b2e',
                            ice: '#f0f9ff',
                            surface: '#f8fcfe',
                            textPrimary: '#0f172a',
                            textSecondary: '#64748b'
                        }
                    },
                    boxShadow: {
                        'soft': '0 10px 30px -5px rgba(0, 193, 222, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        'card': '0 12px 32px 0 rgba(14, 27, 46, 0.06)',
                        'glow': '0 0 25px rgba(0, 193, 222, 0.25)',
                    }
                }
            }
        }
    </script>

    <!-- Carga de estilos y scripts del proyecto mediante Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f7fafd;
            color: #0f172a;
        }
        .shadow-soft {
            box-shadow: 0 10px 30px -5px rgba(0, 193, 222, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        }
        .shadow-card {
            box-shadow: 0 12px 32px 0 rgba(14, 27, 46, 0.06);
        }
        .category-filter {
            transition: all 0.2s ease-in-out;
        }
        .category-filter.active,
        .category-filter[aria-pressed="true"],
        .category-filter.bg-secondary {
            background-color: #00c1de !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px 0 rgba(0, 193, 222, 0.3) !important;
        }
        .category-filter[aria-pressed="false"]:not(.bg-secondary) {
            background-color: #f8fafc;
            color: #334155;
            border: 1px solid #f1f5f9;
        }
        .category-filter[aria-pressed="false"]:not(.bg-secondary):hover {
            background-color: #e0f8fc;
            color: #008da2;
        }
    </style>
</head>
<body class="min-h-screen bg-[#f7fafd] text-slate-800 antialiased selection:bg-[#00c1de] selection:text-white">
    <!-- Encabezado principal del sitio estilo luxury -->
    @include('partials.header')

    <!-- Contenido principal del catálogo por categorías -->
    <main id="catalogo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-10 pb-20">
        <!-- Encabezado y presentación de la página de categorías -->
        <div class="mb-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 border border-cyan-100 text-xs font-bold uppercase tracking-wider text-[#00c1de]">
                Catálogo Exclusivo
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0e1b2e] tracking-tight mt-2.5">
                Explora por Categoría
            </h1>
            <p class="text-slate-500 text-sm sm:text-base mt-2 max-w-xl leading-relaxed">
                Descubre fragancias, cremas y productos seleccionados con atención directa de tu consultor de confianza.
            </p>
            @if ($categoriaSeleccionada)
                <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-cyan-50 border border-cyan-200 px-3.5 py-1 text-xs font-bold text-cyan-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#00c1de] animate-pulse"></span>
                    <span>Categoría seleccionada: {{ $categoriaSeleccionada->nombre }}</span>
                </div>
            @elseif (request()->filled('categoria'))
                <div class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-amber-50 border border-amber-200 p-3 text-xs font-semibold text-amber-800">
                    <span>No encontramos esa categoría. Mostramos todos los productos disponibles.</span>
                </div>
            @endif
        </div>

        <!-- Grid: Panel lateral de filtros y sección de productos -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Panel lateral sticky: Filtros por categoría -->
            <aside class="lg:col-span-3 lg:sticky lg:top-24 self-start rounded-3xl bg-white p-5 lg:p-6 shadow-sm border border-slate-100/90" aria-label="Filtros del catálogo">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h2 class="text-base font-bold text-[#0e1b2e]">Categorías</h2>
                    <span class="text-xs font-semibold text-slate-400">{{ count($categorias) + 1 }} filtros</span>
                </div>
                <div class="flex flex-wrap lg:flex-col gap-2" id="categoryFilters">
                    <button type="button" data-category="all" aria-pressed="true"
                        class="category-filter w-full rounded-2xl px-4 py-2.5 text-left text-xs sm:text-sm font-semibold flex items-center justify-between cursor-pointer">
                        <span>Todas</span>
                        <span class="text-xs opacity-75">({{ count($productos) }})</span>
                    </button>
                    @foreach ($categorias as $categoria)
                        <button type="button" data-category="{{ \Illuminate\Support\Str::slug($categoria->nombre) }}" aria-pressed="false"
                            class="category-filter w-full rounded-2xl px-4 py-2.5 text-left text-xs sm:text-sm font-medium flex items-center justify-between cursor-pointer">
                            <span class="truncate">{{ $categoria->nombre }}</span>
                            <span class="text-xs opacity-60">→</span>
                        </button>
                    @endforeach
                </div>
            </aside>

            <!-- Sección principal: Búsqueda y rejilla de productos -->
            <section class="lg:col-span-9 min-w-0" aria-label="Productos del catálogo">
                <!-- Buscador sticky moderno dentro de la columna principal -->
                <div class="sticky top-20 lg:top-24 z-20 bg-[#f7fafd]/95 backdrop-blur-sm pb-4 pt-1">
                    <div class="rounded-2xl bg-white p-2.5 sm:p-3 shadow-sm border border-slate-100/90 flex items-center gap-3">
                        <div class="relative w-full flex items-center">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <input id="catalogSearch" type="search" autocomplete="off"
                                value="{{ request('buscar') }}"
                                placeholder="Buscar por nombre o descripción (ej. Ilía, Tododia, Chronos)..."
                                class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm leading-normal bg-slate-50 border border-slate-200/80 rounded-full focus:outline-none focus:ring-2 focus:ring-[#00c1de] focus:border-transparent text-slate-700 placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <!-- Contador dinámico de resultados -->
                <div class="flex items-center justify-between mb-4 px-1">
                    <p id="resultsCount" class="text-xs sm:text-sm font-semibold text-slate-500" role="status" aria-live="polite">
                        {{ count($productos) }} {{ count($productos) === 1 ? 'producto disponible' : 'productos disponibles' }}
                    </p>
                </div>

                <!-- Rejilla con tarjetas de productos del catálogo (Estilo Luxury Landing) -->
                <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($productos as $producto)
                        <article data-product-category="{{ \Illuminate\Support\Str::slug($producto->categoria->nombre) }}"
                            class="catalog-product product-item bg-white rounded-3xl p-4 sm:p-5 shadow-sm hover:shadow-card transition-all duration-300 border border-slate-100/90 flex flex-col justify-between group">
                            
                            <div>
                                <!-- Imagen del producto con auto-relleno suave y esquinas redondeadas -->
                                <div class="relative w-full h-52 overflow-hidden rounded-2xl bg-slate-100 mb-4">
                                    <img src="{{ $producto->imagen_url ?: asset('images/branding/finora-icono.png') }}"
                                         alt="{{ $producto->nombre }}"
                                         onerror="this.onerror=null; this.src='{{ asset('images/branding/finora-icono.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-6');"
                                         loading="lazy"
                                         class="w-full h-full object-cover object-center select-none group-hover:scale-105 transition-transform duration-500"
                                         draggable="false">
                                    <span class="absolute top-2.5 left-2.5 px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-[#0e1b2e] text-[10px] font-bold uppercase tracking-wider shadow-xs">
                                        {{ $producto->categoria->nombre }}
                                    </span>
                                </div>

                                <!-- Detalle del producto -->
                                <span class="text-[11px] font-bold text-[#00c1de] uppercase tracking-wider">
                                    {{ $producto->proveedor?->nombre ?? 'Finora' }}
                                </span>
                                <h2 class="font-bold text-slate-900 text-base mt-0.5 mb-1 line-clamp-1 group-hover:text-[#00c1de] transition-colors">
                                    {{ $producto->nombre }}
                                </h2>
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                    {{ $producto->descripcion ?: 'Producto exclusivo disponible en catálogo para consulta y despacho.' }}
                                </p>
                            </div>

                            <!-- Precios y Acciones de Compra -->
                            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Precio consultor:</span>
                                    <span data-money-bob="{{ $producto->precio_venta_actual }}" class="text-lg font-extrabold text-[#0e1b2e]">
                                        @money($producto->precio_venta_actual)
                                    </span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <button type="button" data-add-product="{{ $producto->id }}" data-cart-url="{{ route('carrito') }}"
                                        class="w-full py-2.5 px-4 rounded-full bg-[#00c1de] hover:bg-[#00a3bd] text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md shadow-cyan-300/30 transition-transform active:scale-95 cursor-pointer">
                                        <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24">
                                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                        <span>Agregar al carrito</span>
                                    </button>
                                    <a href="{{ config('services.whatsapp.phone') ? 'https://wa.me/' . preg_replace('/\D+/', '', config('services.whatsapp.phone')) . '?text=' . urlencode('Hola, deseo consultar por el producto: ' . $producto->nombre) : '#' }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="w-full py-2 px-4 rounded-full bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 border border-slate-200 text-slate-700 font-medium text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 fill-current" viewBox="0 0 24 24">
                                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                                        </svg>
                                        <span>Consultar por WhatsApp</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Mensaje de estado cuando no hay resultados de búsqueda -->
                <div id="emptyCatalog" class="rounded-3xl bg-white p-12 text-center shadow-sm border border-slate-100 mt-6" style="display: {{ count($productos) === 0 ? 'block' : 'none' }}" role="status">
                    <div class="w-16 h-16 rounded-full bg-cyan-50 text-[#00c1de] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-[#0e1b2e]">No encontramos productos</h2>
                    <p id="emptyCatalogMessage" class="text-slate-500 text-xs sm:text-sm mt-1 max-w-md mx-auto">
                        Prueba buscando con otro término o seleccionando una categoría diferente.
                    </p>
                </div>
            </section>
        </div>
    </main>

    <!-- Pie de página simétrico moderno -->
    @include('partials.footer')

    <!-- Botón flotante para volver arriba -->
    <button id="backToTopCatalog"
        class="fixed bottom-6 right-6 z-50 w-11 h-11 flex items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 text-slate-700 hover:bg-[#00c1de] hover:text-white hover:border-[#00c1de] transition-all hover:scale-105 active:scale-95 cursor-pointer opacity-0 translate-y-4 pointer-events-none"
        aria-label="Volver arriba" title="Volver arriba"
        onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"></path>
        </svg>
    </button>

    <script>
        window.finoraCatalogProducts = @json($productosBusqueda->map(fn ($producto) => [
            'nombre' => $producto->nombre,
            'categoria' => $producto->categoria?->nombre,
        ])->values());

        document.addEventListener('DOMContentLoaded', () => {
            // Mostrar/ocultar botón volver arriba de forma suave
            const backToTopBtn = document.getElementById('backToTopCatalog');
            if (backToTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 220) {
                        backToTopBtn.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
                        backToTopBtn.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
                    } else {
                        backToTopBtn.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                        backToTopBtn.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
                    }
                }, { passive: true });
            }

            // Si hay parámetro ?categoria= en la URL, hacer scroll suave al catálogo
            const params = new URLSearchParams(window.location.search);
            if (params.has('categoria')) {
                document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    </script>
</body>
</html>
