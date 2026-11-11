<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo Oficial Natura | Consultoría Independiente</title>

    <!-- Favicon de Finora -->
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}">

    <!-- Tipografía: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f7fafd;
            color: #0f172a;
        }
        .filter-pill {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }
        .filter-pill.is-active {
            background-color: #00c1de !important;
            color: #ffffff !important;
            border-color: #00c1de !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px 0 rgba(0, 193, 222, 0.35) !important;
        }
        .filter-pill.is-active-oferta {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%) !important;
            color: #ffffff !important;
            border-color: #f43f5e !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px 0 rgba(244, 63, 94, 0.35) !important;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="min-h-screen bg-[#f7fafd] text-slate-800 antialiased selection:bg-[#00c1de] selection:text-white">
    <!-- Encabezado principal del sitio estilo luxury -->
    @include('partials.header')

    <!-- Contenido principal del catálogo moderno -->
    <main id="catalogo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-10 pb-20">
        <!-- Hero Header del Catálogo -->
        <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 pb-6 border-b border-slate-200/60">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 border border-cyan-200/80 text-[11px] font-bold uppercase tracking-wider text-cyan-900 shadow-xs mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Catálogo de Belleza • Consultoría Natura</span>
                </div>
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-[#0e1b2e] tracking-tight">
                    Catálogo de Productos
                </h1>
                <p class="text-slate-500 text-xs sm:text-base mt-1.5 max-w-xl leading-relaxed">
                    Perfumes, cremas, maquillaje y cuidado personal 100% originales Natura. Asesoramiento directo y pedidos por WhatsApp.
                </p>
                <div id="activeFilterBanner" class="{{ (!$categoriaSeleccionada && !$soloOfertas && empty($soloStock)) ? 'hidden' : '' }} mt-3">
                    @if ($categoriaSeleccionada)
                        <div class="inline-flex items-center gap-2 rounded-full bg-cyan-50 border border-cyan-200 px-3.5 py-1 text-xs font-bold text-cyan-800 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#00c1de] animate-pulse"></span>
                            <span>Categoría seleccionada: {{ $categoriaSeleccionada->nombre }}</span>
                            <a href="{{ route('categorias') }}" class="ml-2 inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer" title="Ver todo el catálogo">
                                <span>✕ Limpiar filtro</span>
                            </a>
                        </div>
                    @elseif ($soloOfertas)
                        <div class="inline-flex items-center gap-2 rounded-full bg-rose-50 border border-rose-200 px-3.5 py-1 text-xs font-bold text-rose-800 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <span>Filtro activo: Productos en Oferta</span>
                            <a href="{{ route('categorias') }}" class="ml-2 inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer" title="Ver todo el catálogo">
                                <span>✕ Limpiar filtro</span>
                            </a>
                        </div>
                    @elseif (!empty($soloStock))
                        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 border border-emerald-200 px-3.5 py-1 text-xs font-bold text-emerald-800 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Filtro activo: Stock Físico</span>
                            <a href="{{ route('categorias') }}" class="ml-2 inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer" title="Ver todo el catálogo">
                                <span>✕ Limpiar filtro</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acceso directo a promociones de ciclo -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('ofertas') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-gradient-to-r from-rose-50 to-orange-50 border border-rose-200/80 text-rose-700 hover:border-rose-300 font-bold text-xs sm:text-sm transition-all shadow-xs hover:scale-105 active:scale-95">
                    <svg class="w-4 h-4 text-rose-500 fill-current" viewBox="0 0 24 24">
                        <path d="M12.75 3.03a.75.75 0 00-.918-.588c-.99.23-1.954.675-2.82 1.32C6.708 5.485 5.25 8.196 5.25 11.25c0 1.94.636 3.733 1.714 5.178-.057-.34-.089-.69-.089-1.049a6.002 6.002 0 013.125-5.26 7.478 7.478 0 00-.73 2.651c.365-.77 1.01-1.38 1.81-1.722a4.5 4.5 0 011.66-.318c.245 0 .487.02.723.058a5.252 5.252 0 001.277-2.735c.18-.737.228-1.503.14-2.26a.75.75 0 00-.53-.615zm-.965 9.07c-.456.326-.827.76-1.077 1.26a3.75 3.75 0 00-.308 1.48c0 1.616.98 2.99 2.378 3.518a.75.75 0 00.978-.92c-.225-.632-.236-1.312-.03-1.95.207-.638.625-1.173 1.182-1.516a.75.75 0 00-.14-1.365 3.75 3.75 0 00-2.983.493z"/>
                    </svg>
                    <span>Ver ofertas de ciclo ({{ $totalOfertas }})</span>
                </a>
            </div>
        </div>

        <!-- Barra de filtros moderna tipo App/Luxury Store -->
        <div class="sticky top-20 z-30 bg-[#f7fafd]/95 backdrop-blur-md pt-2 pb-3 mb-6 sm:mb-8 -mx-4 px-4 sm:mx-0 sm:px-0 border-b border-slate-200/50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 mb-2.5">
                <!-- Buscador rápido integrado con botón limpiar -->
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </span>
                    <input id="catalogSearch" type="search" autocomplete="off"
                           value="{{ request('buscar') }}"
                           placeholder="Buscar por código de revista (ej. 59848), nombre o línea..."
                           class="w-full pl-10 pr-9 py-2 text-xs sm:text-sm bg-white border border-slate-200/90 rounded-full focus:outline-none focus:ring-2 focus:ring-[#00c1de] focus:border-transparent text-slate-800 placeholder-slate-400 shadow-xs transition-all">
                    <button id="clearSearchBtn" type="button" onclick="window.finoraClearSearch?.()"
                            class="{{ request('buscar') ? '' : 'hidden' }} absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-rose-500 cursor-pointer"
                            title="Limpiar búsqueda">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Contador de productos y botón Limpiar Filtros -->
                <div class="flex items-center justify-between sm:justify-end gap-2 shrink-0">
                    <a id="clearFiltersBtn" href="{{ route('categorias') }}" onclick="if (!window.finoraIsServerFiltered) { event.preventDefault(); window.finoraResetCatalogFilter?.(); }"
                       class="{{ $isServerFiltered ? '' : 'hidden' }} inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-500 border border-rose-200/80 px-3.5 py-1.5 rounded-full shadow-2xs transition-all cursor-pointer"
                       title="Restablecer todos los filtros y búsqueda">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Limpiar filtros</span>
                    </a>

                    <span id="resultsCount" class="text-xs font-bold text-slate-500 whitespace-nowrap bg-white px-3.5 py-1.5 rounded-full border border-slate-200/70 shadow-2xs">
                        {{ count($productos) }} productos
                    </span>
                </div>
            </div>

            <!-- Fila de categorías: Mismo tamaño vertical exacto (h-9), iconos y contadores uniformes -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-1 sm:flex-wrap" id="categoryFilters" role="tablist" aria-label="Filtrar catálogo">
                <!-- Todas -->
                <button type="button" data-category="all" role="tab" aria-selected="{{ (!$soloOfertas && !$categoriaSeleccionada && empty($soloStock)) ? 'true' : 'false' }}"
                        class="category-filter filter-pill h-9 shrink-0 inline-flex items-center gap-1.5 px-3.5 sm:px-4 rounded-full text-xs font-semibold {{ (!$soloOfertas && !$categoriaSeleccionada && empty($soloStock)) ? 'is-active' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }} cursor-pointer shadow-xs transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Todos</span>
                    <span class="text-[10px] opacity-75 font-bold">({{ $totalProductos ?? count($productos) }})</span>
                </button>

                <!-- Ofertas -->
                <button type="button" data-category="ofertas" role="tab" aria-selected="{{ $soloOfertas ? 'true' : 'false' }}"
                        class="category-filter filter-pill h-9 shrink-0 inline-flex items-center gap-1.5 px-3.5 sm:px-4 rounded-full text-xs font-semibold {{ $soloOfertas ? 'is-active-oferta' : 'bg-white text-rose-600 border border-rose-200/80 hover:bg-rose-50' }} cursor-pointer shadow-xs transition-all">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M12.75 3.03a.75.75 0 00-.918-.588c-.99.23-1.954.675-2.82 1.32C6.708 5.485 5.25 8.196 5.25 11.25c0 1.94.636 3.733 1.714 5.178-.057-.34-.089-.69-.089-1.049a6.002 6.002 0 013.125-5.26 7.478 7.478 0 00-.73 2.651c.365-.77 1.01-1.38 1.81-1.722a4.5 4.5 0 011.66-.318c.245 0 .487.02.723.058a5.252 5.252 0 001.277-2.735c.18-.737.228-1.503.14-2.26a.75.75 0 00-.53-.615zm-.965 9.07c-.456.326-.827.76-1.077 1.26a3.75 3.75 0 00-.308 1.48c0 1.616.98 2.99 2.378 3.518a.75.75 0 00.978-.92c-.225-.632-.236-1.312-.03-1.95.207-.638.625-1.173 1.182-1.516a.75.75 0 00-.14-1.365 3.75 3.75 0 00-2.983.493z"/>
                    </svg>
                    <span>En Oferta</span>
                    <span class="text-[10px] opacity-75 font-bold">({{ $totalOfertas }})</span>
                </button>

                <!-- Entrega Inmediata (Stock Físico) -->
                <button type="button" data-category="en-stock" role="tab" aria-selected="{{ !empty($soloStock) ? 'true' : 'false' }}"
                        class="category-filter filter-pill h-9 shrink-0 inline-flex items-center gap-1.5 px-3.5 sm:px-4 rounded-full text-xs font-semibold {{ !empty($soloStock) ? 'is-active-stock' : 'bg-white text-emerald-700 border border-emerald-200/90 hover:bg-emerald-50' }} cursor-pointer shadow-xs transition-all">
                    <svg class="w-3.5 h-3.5 fill-none stroke-current" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Entrega Inmediata</span>
                    <span class="text-[10px] opacity-75 font-bold">({{ $totalEnStock ?? 0 }})</span>
                </button>

                <!-- Categorías dinámicas -->
                @foreach ($categorias as $categoria)
                    @php
                        $catSlug = \Illuminate\Support\Str::slug($categoria->nombre);
                        $isSelected = $categoriaSeleccionada && $categoriaSeleccionada->id === $categoria->id;
                        $catCount = $categoria->productos_count ?? $categoria->productos()->where('activo', true)->where('publicado', true)->count();
                    @endphp
                    <button type="button" data-category="{{ $catSlug }}" role="tab" aria-selected="{{ $isSelected ? 'true' : 'false' }}"
                            class="category-filter filter-pill h-9 shrink-0 inline-flex items-center gap-1.5 px-3.5 sm:px-4 rounded-full text-xs font-semibold {{ $isSelected ? 'is-active' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }} cursor-pointer shadow-xs transition-all">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 shrink-0"></span>
                        <span>{{ $categoria->nombre }}</span>
                        <span class="text-[10px] opacity-75 font-bold">({{ $catCount }})</span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Rejilla Moderna 4-columnas de productos -->
        <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($productos as $producto)
                @php
                    $isOffer = $producto->es_oferta;
                    $discountPct = $producto->descuento_porcentaje;
                    $finalPrice = $producto->precio_final;
                    $regularPrice = $producto->precio_venta_actual;
                    $lineName = $producto->marca_linea ?: ($producto->categoria?->nombre ?: 'Natura');
                    $hasStock = $producto->stock_disponible > 0;
                @endphp
                <article data-product-category="{{ \Illuminate\Support\Str::slug($producto->categoria->nombre) }}"
                         data-is-offer="{{ $isOffer ? 'true' : 'false' }}"
                         data-in-stock="{{ $hasStock ? 'true' : 'false' }}"
                         class="catalog-product product-item bg-white rounded-3xl p-4 shadow-sm card-hover-lift border border-slate-100/90 flex flex-col justify-between group cursor-pointer">
                    
                    <div>
                        <!-- Contenedor de Imagen con Insignia de Oferta y Botón de Me Gusta -->
                        <div class="relative w-full aspect-square sm:h-56 overflow-hidden rounded-2xl bg-gradient-to-b from-slate-50 to-slate-100/80 mb-3.5">
                            @if($isOffer && $discountPct)
                                <span class="absolute top-2.5 left-2.5 z-10 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-500 text-white text-[10px] font-extrabold uppercase tracking-wider shadow-md">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                        <path d="M12.75 3.03a.75.75 0 00-.918-.588c-.99.23-1.954.675-2.82 1.32C6.708 5.485 5.25 8.196 5.25 11.25c0 1.94.636 3.733 1.714 5.178-.057-.34-.089-.69-.089-1.049a6.002 6.002 0 013.125-5.26 7.478 7.478 0 00-.73 2.651c.365-.77 1.01-1.38 1.81-1.722a4.5 4.5 0 011.66-.318c.245 0 .487.02.723.058a5.252 5.252 0 001.277-2.735c.18-.737.228-1.503.14-2.26a.75.75 0 00-.53-.615zm-.965 9.07c-.456.326-.827.76-1.077 1.26a3.75 3.75 0 00-.308 1.48c0 1.616.98 2.99 2.378 3.518a.75.75 0 00.978-.92c-.225-.632-.236-1.312-.03-1.95.207-.638.625-1.173 1.182-1.516a.75.75 0 00-.14-1.365 3.75 3.75 0 00-2.983.493z"/>
                                    </svg>
                                    <span>-{{ $discountPct }}% OFF</span>
                                </span>
                            @else
                                <span class="absolute top-2.5 left-2.5 z-10 px-2.5 py-0.5 rounded-full bg-white/90 backdrop-blur-md text-[#0e1b2e] text-[10px] font-bold uppercase tracking-wider shadow-xs">
                                    {{ $producto->categoria->nombre }}
                                </span>
                            @endif

                            @if ($hasStock)
                                <span class="absolute bottom-2.5 left-2.5 z-10 px-2 py-0.5 rounded-full bg-emerald-600/95 backdrop-blur-xs text-white text-[10px] font-bold shadow-xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    <span>Entrega Inmediata</span>
                                </span>
                            @else
                                <span class="absolute bottom-2.5 left-2.5 z-10 px-2 py-0.5 rounded-full bg-slate-800/80 backdrop-blur-xs text-white text-[10px] font-medium shadow-xs">
                                    A pedido
                                </span>
                            @endif

                            <!-- Botón Me Gusta (Corazón) -->
                            <button type="button" aria-label="Dar me gusta a {{ $producto->nombre }}"
                                    class="wishlist-toggle-btn absolute top-2.5 right-2.5 z-10 p-2 bg-white/90 backdrop-blur-md rounded-full text-slate-400 hover:text-rose-500 shadow-sm transition-all cursor-pointer hover:scale-110 active:scale-95"
                                    data-prod-id="{{ $producto->id }}"
                                    onclick="event.stopPropagation(); window.finoraToggleLike?.(this);">
                                <svg class="heart-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                </svg>
                            </button>

                            <img src="{{ $producto->imagen_url ?: asset('images/branding/finora-icono.png') }}"
                                 alt="{{ $producto->nombre }}"
                                 onerror="this.onerror=null; this.src='{{ asset('images/branding/finora-icono.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-6');"
                                 loading="lazy"
                                 class="w-full h-full object-cover object-center select-none group-hover:scale-105 transition-transform duration-500"
                                 draggable="false">
                        </div>

                        <!-- Info del Producto -->
                        <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mb-1">
                            <span class="text-[#00c1de] font-bold uppercase tracking-wider truncate max-w-[140px]">{{ $lineName }}</span>
                            <span>{{ $producto->codigo }}</span>
                        </div>
                        <h2 class="font-bold text-slate-900 text-sm sm:text-base line-clamp-1 group-hover:text-[#00c1de] transition-colors" title="{{ $producto->nombre }}">
                            {{ $producto->nombre }}
                        </h2>

                        <!-- Corazones / Me gusta comunitarios -->
                        <div class="flex items-center gap-1.5 mt-1 mb-2 text-xs text-slate-500">
                            <svg class="w-3.5 h-3.5 text-rose-500 fill-current shrink-0 select-none" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="font-bold text-slate-700 likes-count" data-prod-id="{{ $producto->id }}">{{ $producto->likes }}</span>
                            <span class="text-slate-400 text-[11px]">me gusta</span>
                        </div>
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mt-1 mb-3">
                            {{ $producto->descripcion ?: 'Producto 100% original disponible en catálogo para consulta y despacho.' }}
                        </p>
                    </div>

                    <!-- Precios y Botón de Acción -->
                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5">
                        <div class="flex items-baseline justify-between">
                            <div>
                                <span class="text-lg font-extrabold text-[#0e1b2e]">
                                    @money($finalPrice)
                                </span>
                                @if($isOffer && $finalPrice < $regularPrice)
                                    <span class="text-xs text-slate-400 line-through ml-1.5">
                                        @money($regularPrice)
                                    </span>
                                @endif
                            </div>
                            @if($isOffer)
                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                                    Precio de ciclo
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-5 gap-2">
                            <!-- Botón Principal: Agregar al Carrito (4 cols) -->
                            <button type="button"
                                    data-add-product="{{ $producto->id }}"
                                    onclick="event.stopPropagation(); window.finoraAddProduct(this)"
                                    class="col-span-4 py-2.5 px-3 rounded-full bg-[#00c1de] hover:bg-[#00a3bd] text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md shadow-cyan-300/30 transition-transform active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24">
                                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                                <span>Agregar</span>
                            </button>

                            <!-- Botón WhatsApp Consulta Rápida (1 col) -->
                            <a href="{{ config('services.whatsapp.phone') ? 'https://wa.me/' . preg_replace('/\D+/', '', config('services.whatsapp.phone')) . '?text=' . urlencode('¡Hola! Deseo consultar por el producto: ' . $producto->nombre . ' (' . $producto->codigo . ')') : '#' }}"
                               target="_blank" rel="noopener noreferrer"
                               title="Consultar por WhatsApp"
                               aria-label="Consultar sobre {{ $producto->nombre }} por WhatsApp"
                               class="col-span-1 py-2.5 rounded-full bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 text-emerald-700 flex items-center justify-center transition-colors cursor-pointer">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Mensaje inteligente de estado cuando no hay resultados de búsqueda (Empty-State Recovery) -->
        <div id="emptyCatalog" class="rounded-3xl bg-white p-8 sm:p-12 text-center shadow-card border border-slate-100 mt-6" style="display: {{ count($productos) === 0 ? 'block' : 'none' }}" role="status">
            <div class="w-16 h-16 rounded-full bg-cyan-50 text-[#00c1de] flex items-center justify-center mx-auto mb-4 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </div>
            
            <h2 class="text-xl sm:text-2xl font-bold text-[#0e1b2e]">
                No encontramos coincidencias exactas
            </h2>
            
            <p id="emptyCatalogMessage" class="text-slate-600 text-xs sm:text-sm mt-2 max-w-lg mx-auto leading-relaxed">
                No te preocupes, tenemos productos similares y alternativas en nuestras distintas categorías del catálogo.
            </p>

            <!-- Sugerencia dinámica inteligente si el producto está en otra categoría -->
            <div id="emptyCategorySuggestion" class="hidden mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-cyan-50 border border-cyan-200/80 text-xs font-semibold text-cyan-900 mx-auto">
                <svg class="w-4 h-4 text-[#00c1de] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Hay productos relacionados en:</span>
                <button type="button" id="suggestedCategoryBtn" class="font-bold underline text-[#00c1de] hover:text-[#00a3bd] cursor-pointer">
                    Ver categoría sugerida →
                </button>
            </div>

            <!-- Botones de acción rápida: Ver todas las categorías y explorar colecciones -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                    Explora alternativas disponibles
                </span>
                
                <div class="flex flex-wrap items-center justify-center gap-2 max-w-xl">
                    <button type="button" onclick="window.finoraResetCatalogFilter?.()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#00c1de] text-white text-xs font-bold shadow-md shadow-cyan-300/30 hover:bg-[#00a3bd] transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Ver todo el catálogo</span>
                    </button>
                    <a href="{{ route('ofertas') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <path d="M12.75 3.03a.75.75 0 00-.918-.588c-.99.23-1.954.675-2.82 1.32C6.708 5.485 5.25 8.196 5.25 11.25c0 1.94.636 3.733 1.714 5.178-.057-.34-.089-.69-.089-1.049a6.002 6.002 0 013.125-5.26 7.478 7.478 0 00-.73 2.651c.365-.77 1.01-1.38 1.81-1.722a4.5 4.5 0 011.66-.318c.245 0 .487.02.723.058a5.252 5.252 0 001.277-2.735c.18-.737.228-1.503.14-2.26a.75.75 0 00-.53-.615zm-.965 9.07c-.456.326-.827.76-1.077 1.26a3.75 3.75 0 00-.308 1.48c0 1.616.98 2.99 2.378 3.518a.75.75 0 00.978-.92c-.225-.632-.236-1.312-.03-1.95.207-.638.625-1.173 1.182-1.516a.75.75 0 00-.14-1.365 3.75 3.75 0 00-2.983.493z"/>
                        </svg>
                        <span>Ver ofertas de ciclo</span>
                    </a>
                    @foreach ($categorias as $cat)
                        <button type="button" data-switch-category="{{ \Illuminate\Support\Str::slug($cat->nombre) }}"
                                class="quick-category-switch px-3 py-1.5 rounded-full bg-slate-50 hover:bg-cyan-50 hover:text-[#00c1de] border border-slate-200/80 text-slate-600 text-xs font-medium transition-colors cursor-pointer">
                            {{ $cat->nombre }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Canal directo WhatsApp por si buscan algo de la revista física -->
            <div class="mt-6 pt-4 text-xs text-slate-500 max-w-md mx-auto">
                <p>¿Buscas un producto específico de la revista física de Natura?</p>
                <a href="{{ config('services.whatsapp.phone') ? 'https://wa.me/' . preg_replace('/\D+/', '', config('services.whatsapp.phone')) . '?text=' . urlencode('¡Hola! Estoy buscando un producto específico de Natura que no encontré en la web.') : '#' }}"
                   target="_blank" rel="noopener noreferrer"
                   class="mt-2 inline-flex items-center gap-1.5 font-bold text-emerald-600 hover:text-emerald-700 underline">
                    <span>Pregúntale directamente a tu consultora por WhatsApp</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </main>

    <!-- Pie de página simétrico moderno -->
    @include('partials.footer')

    <!-- Botón flotante para volver arriba -->
    <button id="backToTopCatalog"
        class="fixed bottom-20 sm:bottom-6 right-6 z-50 w-11 h-11 flex items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 text-slate-700 hover:bg-[#00c1de] hover:text-white hover:border-[#00c1de] transition-all hover:scale-105 active:scale-95 cursor-pointer opacity-0 translate-y-4 pointer-events-none"
        aria-label="Volver arriba" title="Volver arriba"
        onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"></path>
        </svg>
    </button>

    @php
        $catalogProductsJson = $productosBusqueda->map(fn ($producto) => [
            'nombre' => $producto->nombre,
            'categoria' => $producto->categoria?->nombre,
            'es_oferta' => (bool) ($producto->en_oferta || ($producto->precio_oferta && $producto->precio_oferta < $producto->precio_venta_actual)),
        ])->values();
    @endphp

    <script>
        window.finoraCatalogProducts = @json($catalogProductsJson);
        window.finoraIsServerFiltered = {{ $isServerFiltered ? 'true' : 'false' }};
        window.finoraCatalogoUrl = "{{ route('categorias') }}";
        window.finoraOfertasUrl = "{{ route('ofertas') }}";

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

            // Si hay parámetro ?categoria= o ?buscar= o ?ofertas= en la URL, hacer scroll suave al catálogo
            const params = new URLSearchParams(window.location.search);
            if (params.has('categoria') || params.has('buscar') || params.has('ofertas')) {
                document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    </script>
</body>
</html>
