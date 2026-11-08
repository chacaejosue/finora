<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Catálogo Digital Natura | Consultoría Independiente</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>

    <!-- Tailwind CSS CDN with Forms & Container Queries -->
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

    <!-- Custom CSS for specialized visuals -->
    <style data-purpose="custom-styling">
        body {
            background-color: #f7fafd;
            color: #0f172a;
        }
        .hero-gradient {
            background: radial-gradient(circle at 75% 35%, #bcecf9 0%, #e8f7fc 45%, #ffffff 80%);
        }
        .banner-gradient {
            background: linear-gradient(105deg, #dcf4fb 0%, #d1f0fa 35%, #edf9fd 70%, #d8f1f8 100%);
        }
        .newsletter-gradient {
            background: linear-gradient(90deg, #71def3 0%, #9eedfb 45%, #c5f4fd 100%);
        }
        .text-gradient {
            background: linear-gradient(135deg, #00c1de 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass-pill {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
        }

        /* Ocultar elementos secundarios preservados para no interferir con la réplica visual */
        .finora-theme-toggle,
        #aiChatLauncher,
        #floating-buttons,
        #fullCatalogSection {
            display: none !important;
        }

        /* Animación para el orbe de carrito */
        .finora-cart-orb {
            width: 20px;
            height: 20px;
            border-radius: 9999px;
            background: #00c1de;
            box-shadow: 0 0 15px rgba(0, 193, 222, 0.6);
            transition: all 0.75s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        .finora-cart-orb-flying {
            transform: scale(0.3);
            opacity: 0.2;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-[#f7fafd] min-h-screen selection:bg-cyan-100 selection:text-cyan-900">

    @php
        // Categorías funcionales desde la base de datos
        $categoryFallbackImages = [
            'perfumeria' => asset('images/categories/cat-perfumes.jpg'),
            'cuidado-facial' => asset('images/categories/cat-cremas.jpg'),
            'cuidado-corporal' => asset('images/categories/cat-aceites.jpg'),
            'maquillaje' => asset('images/categories/cat-masculinos.jpg'),
            'higiene-personal' => asset('images/categories/cat-femeninos.jpg'),
        ];

        $categoryItems = [];
        $activeCats = $categorias->isNotEmpty() ? $categorias->take(5) : collect();
        foreach ($activeCats as $index => $cat) {
            $catSlug = \Illuminate\Support\Str::slug($cat->nombre);
            $firstProductImage = $cat->productos->first()?->imagen_url;
            $catImg = $firstProductImage ?: ($categoryFallbackImages[$catSlug] ?? asset('images/categories/cat-perfumes.jpg'));

            $categoryItems[] = [
                'id' => $cat->id,
                'nombre' => $cat->nombre,
                'slug' => $catSlug,
                'imagen' => $catImg,
                'url' => route('categorias', ['categoria' => $catSlug]),
            ];
        }

        // Si la base de datos tuviese menos de 5 categorías, rellenar con fallback para mantener la rejilla de 5
        if (count($categoryItems) < 5) {
            $defaultCategories = [
                ['nombre' => 'Perfumes', 'slug' => 'perfumeria', 'imagen' => asset('images/categories/cat-perfumes.jpg')],
                ['nombre' => 'Cremas', 'slug' => 'cuidado-facial', 'imagen' => asset('images/categories/cat-cremas.jpg')],
                ['nombre' => 'Aceites', 'slug' => 'cuidado-corporal', 'imagen' => asset('images/categories/cat-aceites.jpg')],
                ['nombre' => 'Masculinos', 'slug' => 'perfumeria', 'imagen' => asset('images/categories/cat-masculinos.jpg')],
                ['nombre' => 'Femeninos', 'slug' => 'perfumeria', 'imagen' => asset('images/categories/cat-femeninos.jpg')],
            ];
            while (count($categoryItems) < 5) {
                $idx = count($categoryItems);
                $def = $defaultCategories[$idx];
                $categoryItems[] = [
                    'id' => $idx + 1,
                    'nombre' => $def['nombre'],
                    'slug' => $def['slug'],
                    'imagen' => $def['imagen'],
                    'url' => route('categorias', ['categoria' => $def['slug']]),
                ];
            }
        }

        // Selección de productos destacados: prioritarios por mayor cantidad de likes (corazones), y al azar para completar la portada
        $featuredSource = (isset($destacados) && $destacados->isNotEmpty())
            ? $destacados
            : (function() use ($productos) {
                if ($productos->isEmpty()) return collect();
                $liked = $productos->filter(fn ($p) => (int) ($p->likes ?? 0) > 0)->sortByDesc('likes')->take(4);
                $needed = 4 - $liked->count();
                if ($needed > 0) {
                    $likedIds = $liked->pluck('id');
                    $fill = $productos->reject(fn ($p) => $likedIds->contains($p->id))->shuffle()->take($needed);
                    return $liked->concat($fill)->values();
                }
                return $liked->values();
            })();

        $featuredProducts = [];
        foreach ($featuredSource as $idx => $dbProd) {
            $isOffer = (bool) $dbProd->es_oferta;
            $finalPrice = $dbProd->precio_final;
            $regularPrice = $dbProd->precio_venta_actual;

            $featuredProducts[] = [
                'id' => $dbProd->id,
                'codigo' => $dbProd->codigo,
                'nombre' => $dbProd->nombre,
                'linea' => $dbProd->marca_linea ?: ($dbProd->categoria?->nombre ?: 'Natura'),
                'categoriaNombre' => $dbProd->categoria?->nombre ?: 'Catálogo general',
                'desc' => $dbProd->descripcion ?: 'Producto disponible en catálogo para consulta y compra.',
                'imagen' => $dbProd->imagen_url ?: asset('images/branding/finora-icono.png'),
                'tag' => $dbProd->proveedor?->nombre ?: 'Natura',
                'precio' => $finalPrice,
                'precio_regular' => $regularPrice,
                'precio_display' => 'Bs ' . number_format($finalPrice, 2),
                'precio_original_display' => ($isOffer && $finalPrice < $regularPrice) ? 'Bs ' . number_format($regularPrice, 2) : null,
                'likes' => (int) ($dbProd->likes ?? 0),
                'descuento' => ($isOffer && $dbProd->descuento_porcentaje) ? '-' . $dbProd->descuento_porcentaje . '%' : null,
            ];
        }

        // Fallback en caso de base de datos vacía
        if (empty($featuredProducts)) {
            $featuredProducts = [
                ['id' => 1, 'codigo' => 'DEM-001', 'nombre' => 'Perfume Aurora', 'linea' => 'Perfumería', 'categoriaNombre' => 'Perfumería', 'desc' => 'Fragancia cautivadora con notas florales y maderosas.', 'precio' => 39.99, 'precio_display' => 'Bs 39.99', 'precio_original_display' => 'Bs 49.99', 'likes' => 28, 'descuento' => '-20%', 'imagen' => asset('images/prod-aurora.jpg'), 'tag' => 'Natura'],
                ['id' => 2, 'codigo' => 'DEM-002', 'nombre' => 'Crema Hidratante Soft', 'linea' => 'Cuidado facial', 'categoriaNombre' => 'Cuidado facial', 'desc' => 'Hidratación profunda 24h para una piel suave y radiante.', 'precio' => 29.99, 'precio_display' => 'Bs 29.99', 'precio_original_display' => null, 'likes' => 19, 'descuento' => null, 'imagen' => asset('images/prod-crema.jpg'), 'tag' => 'Natura'],
                ['id' => 3, 'codigo' => 'DEM-003', 'nombre' => 'Aceite Esencial Calm', 'linea' => 'Cuidado corporal', 'categoriaNombre' => 'Cuidado corporal', 'desc' => 'Aceite botánico relajante con extractos naturales.', 'precio' => 34.99, 'precio_display' => 'Bs 34.99', 'precio_original_display' => 'Bs 40.99', 'likes' => 14, 'descuento' => '-15%', 'imagen' => asset('images/prod-aceite.jpg'), 'tag' => 'Natura'],
                ['id' => 4, 'codigo' => 'DEM-004', 'nombre' => 'Colonia Essence Men', 'linea' => 'Perfumería', 'categoriaNombre' => 'Perfumería', 'desc' => 'Aroma fresco, sofisticado y de larga duración.', 'precio' => 32.99, 'precio_display' => 'Bs 32.99', 'precio_original_display' => null, 'likes' => 11, 'descuento' => null, 'imagen' => asset('images/prod-colonia.jpg'), 'tag' => 'Natura'],
            ];
        }

        // Catálogo completo preservado en memoria
        $categoriasLanding = $categorias->map(fn ($categoria) => [
            'nombre' => $categoria->nombre,
            'desc' => $categoria->descripcion ?: 'Productos disponibles en nuestro catálogo.',
            'imagen' => $categoria->productos->first()?->imagen_url ?: asset('images/branding/finora-icono.png'),
            'badge' => $categoria->nombre,
            'slug' => \Illuminate\Support\Str::slug($categoria->nombre),
        ]);
        $productosLanding = $productos->map(fn ($producto) => [
            'id' => $producto->id,
            'codigo' => $producto->codigo,
            'nombre' => $producto->nombre,
            'linea' => $producto->linea?->nombre ?: 'Catálogo general',
            'categoria' => \Illuminate\Support\Str::slug($producto->categoria->nombre),
            'categoriaNombre' => $producto->categoria->nombre,
            'desc' => $producto->descripcion ?: 'Producto disponible para consulta.',
            'imagen' => $producto->imagen_url ?: asset('images/branding/finora-icono.png'),
            'tag' => $producto->proveedor?->nombre ?: 'Natura',
            'precio' => $producto->precio_venta_actual,
        ]);
    @endphp

    <!-- BEGIN: MainHeader -->
    @include('partials.header')
    <!-- END: MainHeader -->

    <main>
        <!-- BEGIN: HeroSection -->
        <section id="inicio" class="relative overflow-hidden hero-gradient pt-8 pb-16 lg:py-16 scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    <!-- Hero Text Details -->
                    <div class="lg:col-span-6 space-y-6 z-10 text-left">
                        <span class="inline-flex items-center gap-2 text-[11px] font-bold tracking-wider uppercase text-cyan-800 bg-cyan-50/90 px-3.5 py-1.5 rounded-full border border-cyan-200/80 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Consultora Independiente Natura • Productos 100% Originales
                        </span>
                        <div class="space-y-1">
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#0e1b2e] tracking-tight leading-[1.1]">
                                Catálogo de Belleza<br>
                                <span class="text-gradient">Productos Originales Natura</span>
                            </h1>
                        </div>
                        <p class="text-slate-600 text-base sm:text-lg max-w-lg leading-relaxed font-normal">
                            Perfumería, cremas y cuidado personal con garantía de fábrica. Asesoramiento personalizado y pedidos directos coordinados por tu consultora de confianza.
                        </p>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a class="inline-flex items-center justify-center gap-3 px-8 py-3.5 bg-finora-cyan hover:bg-finora-cyanDark text-white font-semibold rounded-full shadow-lg shadow-cyan-400/30 hover:shadow-cyan-400/40 transition-all transform hover:-translate-y-0.5 cursor-pointer" href="#catalogo-destacados">
                                <span class="">Explorar catálogo Natura</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </a>
                            <a class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-full border border-rose-200/80 transition-all cursor-pointer" href="{{ route('ofertas') }}">
                                <svg class="w-4 h-4 text-rose-500 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.75 3.03a.75.75 0 00-.918-.588c-.99.23-1.954.675-2.82 1.32C6.708 5.485 5.25 8.196 5.25 11.25c0 1.94.636 3.733 1.714 5.178-.057-.34-.089-.69-.089-1.049a6.002 6.002 0 013.125-5.26 7.478 7.478 0 00-.73 2.651c.365-.77 1.01-1.38 1.81-1.722a4.5 4.5 0 011.66-.318c.245 0 .487.02.723.058a5.252 5.252 0 001.277-2.735c.18-.737.228-1.503.14-2.26a.75.75 0 00-.53-.615zm-.965 9.07c-.456.326-.827.76-1.077 1.26a3.75 3.75 0 00-.308 1.48c0 1.616.98 2.99 2.378 3.518a.75.75 0 00.978-.92c-.225-.632-.236-1.312-.03-1.95.207-.638.625-1.173 1.182-1.516a.75.75 0 00-.14-1.365 3.75 3.75 0 00-2.983.493z"/>
                                </svg>
                                <span>Ver promociones</span>
                            </a>
                        </div>
                    </div>
                    <!-- Hero Graphic Composition Carousel (En móvil aparece inmediatamente después del texto y botones principales) -->
                    <div class="lg:col-span-6 relative flex flex-col justify-center items-center pt-2 lg:pt-0" id="heroCarouselWrapper">
                        <!-- Soft Radial Backlight Glow -->
                        <div class="absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-cyan-200/50 filter blur-3xl -z-10 pointer-events-none"></div>

                        <!-- Slides Stage Container -->
                        <div class="relative w-full max-w-md sm:max-w-lg lg:max-w-xl min-h-[300px] sm:min-h-[380px] lg:min-h-[420px] flex items-end justify-center">

                            <!-- Slide 1 (Colección con pedestal) -->
                            <div class="hero-slide absolute inset-0 flex flex-col items-center justify-end transition-all duration-700 ease-out opacity-100 scale-100 z-10" data-slide-index="0">
                                <div class="relative w-full flex items-end justify-center">
                                    <img alt="Colección de Catálogo Natura Cosméticos y Perfumería"
                                         class="w-full max-h-[360px] sm:max-h-[410px] object-contain select-none drop-shadow-[0_15px_30px_rgba(0,193,222,0.22)] transition-transform duration-700 hover:scale-[1.02]"
                                         draggable="false"
                                         src="{{ asset('images/hero-pedestal.png') }}">
                                </div>
                            </div>

                            <!-- Slide 2 (Cosmética, labial, polvo compacto y roll-on) -->
                            <div class="hero-slide absolute inset-0 flex flex-col items-center justify-end transition-all duration-700 ease-out opacity-0 scale-95 pointer-events-none z-0" data-slide-index="1">
                                <div class="relative w-full flex items-center justify-center">
                                    <img alt="Cuidado Personal y Maquillaje Natura"
                                         class="w-full max-h-[360px] sm:max-h-[410px] object-contain select-none drop-shadow-[0_8px_24px_rgba(0,193,222,0.18)] transition-transform duration-700 hover:scale-[1.02]"
                                         draggable="false"
                                         src="{{ asset('images/hero-slide-2.png') }}">
                                </div>
                            </div>

                            <!-- Slide 3 (Perfume cristal, crema facial, jabón y protector solar) -->
                            <div class="hero-slide absolute inset-0 flex flex-col items-center justify-end transition-all duration-700 ease-out opacity-0 scale-95 pointer-events-none z-0" data-slide-index="2">
                                <div class="relative w-full flex items-center justify-center">
                                    <img alt="Fragancias Exclusivas y Cuidado Facial Natura"
                                         class="w-full max-h-[360px] sm:max-h-[410px] object-contain select-none drop-shadow-[0_8px_24px_rgba(0,193,222,0.18)] transition-transform duration-700 hover:scale-[1.02]"
                                         draggable="false"
                                         src="{{ asset('images/hero-slide-3.png') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination Dots (Pelotas interactivas y animadas) -->
                <div class="flex justify-center items-center gap-2.5 pt-6 pb-2" id="heroPagination" role="tablist" aria-label="Navegación de imágenes del hero">
                    <button type="button" class="hero-dot w-7 h-2.5 rounded-full bg-finora-cyan transition-all duration-300 shadow-xs cursor-pointer" data-slide-target="0" role="tab" aria-selected="true" aria-label="Ver diapositiva 1"></button>
                    <button type="button" class="hero-dot w-2.5 h-2.5 rounded-full bg-sky-200 hover:bg-sky-300 transition-all duration-300 cursor-pointer" data-slide-target="1" role="tab" aria-selected="false" aria-label="Ver diapositiva 2"></button>
                    <button type="button" class="hero-dot w-2.5 h-2.5 rounded-full bg-sky-200 hover:bg-sky-300 transition-all duration-300 cursor-pointer" data-slide-target="2" role="tab" aria-selected="false" aria-label="Ver diapositiva 3"></button>
                </div>

                <!-- Barra de Confianza y Propuesta de Valor (Posicionada bajo la presentación visual de forma limpia) -->
                <div class="mt-6 pt-6 border-t border-sky-100/80 flex flex-wrap items-center justify-center sm:justify-between gap-4 sm:gap-6 text-xs text-slate-600 font-medium">
                    <!-- Feature 1 -->
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-cyan-50 flex items-center justify-center text-finora-cyan shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-700">Envíos coordinados a Bolivia</span>
                    </div>
                    <!-- Feature 2 -->
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-cyan-50 flex items-center justify-center text-finora-cyan shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-700">Garantía 100% Original Natura</span>
                    </div>
                    <!-- Feature 3 -->
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-cyan-50 flex items-center justify-center text-finora-cyan shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-700">Atención directa WhatsApp</span>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: HeroSection -->

        <!-- BEGIN: CategoriesSection (Contenedor unificado de globos de categorías con proporción simétrica exacta) -->
        <section id="categorias" class="py-8 sm:py-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
            <div class="bg-white rounded-3xl shadow-soft border border-slate-100/80 p-4 sm:p-6 lg:py-6 lg:px-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-6">
                    @foreach($categoryItems as $cat)
                    <a class="group flex flex-col items-center text-center p-3 sm:p-4 hover:-translate-y-0.5 transition-all duration-300 cursor-pointer rounded-2xl hover:bg-slate-50/70 {{ $loop->last && count($categoryItems) % 2 !== 0 ? 'col-span-2 sm:col-span-1 max-w-[200px] mx-auto w-full' : '' }}" href="{{ $cat['url'] }}">
                        <!-- Globo de categoría con proporciones y esquinas redondeadas uniformes -->
                        <div class="w-full max-w-[150px] aspect-[4/3] overflow-hidden rounded-[1.5rem] bg-gradient-to-b from-sky-50 via-cyan-50/40 to-slate-100 mb-3 transition-transform duration-300 group-hover:scale-105 shadow-xs">
                            <img alt="{{ $cat['nombre'] }}" class="w-full h-full object-cover object-center select-none" draggable="false" src="{{ $cat['imagen'] }}" onerror="this.onerror=null; this.src='{{ asset('images/branding/finora-icono.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-4');">
                        </div>
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800 group-hover:text-finora-cyan transition-colors">{{ $cat['nombre'] }}</h3>
                        <span class="text-xs text-finora-cyan font-medium flex items-center justify-center gap-1 mt-1 group-hover:gap-1.5 transition-all">
                            Explorar <span>→</span>
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- END: CategoriesSection -->

        <!-- BEGIN: PromoBannerSection -->
        <section id="ofertas" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 scroll-mt-24">
            <div class="relative overflow-hidden rounded-3xl banner-gradient px-6 pt-8 pb-0 sm:px-10 sm:pt-10 lg:px-14 lg:pt-12 border border-sky-100/50 shadow-soft">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Text and CTA column -->
                    <div class="lg:col-span-6 z-10 space-y-4 pb-8 lg:pb-12">
                        <span class="text-xs font-bold tracking-wider uppercase text-cyan-700">
                            CAMPAÑA NATURA
                        </span>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-[#0e1b2e] leading-tight">
                            Cuida tu piel con<br>lo mejor de Natura
                        </h2>
                        <p class="text-slate-600 text-sm sm:text-base pb-2 font-normal">
                            Hasta 40% OFF en productos seleccionados de catálogo.
                        </p>
                        <div>
                            <a class="inline-flex items-center gap-2.5 px-7 py-3 bg-[#0e1b2e] hover:bg-slate-800 text-white font-medium rounded-full shadow-md transition-transform hover:-translate-y-0.5 cursor-pointer" href="{{ route('ofertas') }}">
                                <span class="">Ver ofertas de ciclo</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <!-- Model & Promo Graphic (skincare-model.png) -->
                    <div class="lg:col-span-6 relative flex justify-center lg:justify-end items-end self-end pt-4 lg:pt-0">
                        <!-- Model Image (Transparent PNG) -->
                        <div class="relative w-full max-w-sm sm:max-w-md lg:max-w-lg flex items-end justify-center">
                            <img alt="Cuida tu piel, realza tu belleza"
                                 class="w-full h-auto object-contain select-none pointer-events-none drop-shadow-[0_15px_30px_rgba(0,193,222,0.18)]"
                                 draggable="false"
                                 src="{{ asset('images/skincare-model.png') }}">
                        </div>

                        <!-- Promo Discount Badge -->
                        <div class="absolute right-2 sm:right-6 lg:right-6 top-4 sm:top-1/2 sm:-translate-y-1/2 z-20 bg-white/95 backdrop-blur-md rounded-full w-24 h-24 sm:w-28 sm:h-28 flex flex-col items-center justify-center shadow-xl border border-sky-100">
                            <span class="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase tracking-wider">Hasta</span>
                            <span class="text-2xl sm:text-3xl font-extrabold text-[#0e1b2e] leading-none">40%</span>
                            <span class="text-xs font-bold text-[#0e1b2e]">OFF</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END: PromoBannerSection -->

        <!-- BEGIN: FeaturedProductsSection -->
        <section id="catalogo-destacados" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 scroll-mt-24">
            <!-- Section Title & Link -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 pb-3 border-b border-slate-200/60 gap-4">
                <div>
                    <span class="text-xs font-bold tracking-wider uppercase text-finora-cyan block mb-1">
                        Favoritos de la Comunidad • Recomendados
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#0e1b2e]">
                        Lo más gustado de Natura
                    </h2>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-3">
                    <span class="sm:hidden text-xs text-finora-cyan font-bold flex items-center gap-1">
                        Desliza para explorar →
                    </span>
                    <a class="text-sm font-semibold text-[#0e1b2e] hover:text-finora-cyan flex items-center gap-1.5 transition-colors group cursor-pointer" href="{{ route('categorias') }}">
                        Ver catálogo completo
                        <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
            </div>
            <!-- Products Grid / Mobile Carousel (Carrusel swipeable fluido en móvil, rejilla 4 cols en desktop) -->
            <div class="flex sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 overflow-x-auto sm:overflow-visible no-scrollbar pb-4 sm:pb-0 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0">
                @foreach($featuredProducts as $prod)
                <div class="product-item min-w-[270px] max-w-[285px] sm:min-w-0 sm:max-w-none snap-start shrink-0 sm:shrink bg-white rounded-3xl p-4 sm:p-5 shadow-sm hover:shadow-card transition-all duration-300 border border-slate-100/90 flex flex-col justify-between group cursor-pointer"
                     role="button"
                     tabindex="0"
                     aria-label="Ver detalles de {{ $prod['nombre'] }}"
                     data-id="{{ $prod['id'] }}"
                     data-codigo="{{ $prod['codigo'] }}"
                     data-nombre="{{ $prod['nombre'] }}"
                     data-linea="{{ $prod['linea'] }}"
                     data-categoria-nombre="{{ $prod['categoriaNombre'] }}"
                     data-desc="{{ $prod['desc'] }}"
                     data-imagen="{{ $prod['imagen'] }}"
                     data-tag="{{ $prod['tag'] }}"
                     data-precio="{{ $prod['precio'] }}"
                     onclick="openProductModal(this)"
                     onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openProductModal(this); }">
                    <div class="relative">
                        <!-- Badge -->
                        @if($prod['descuento'])
                        <span class="absolute top-2 left-2 bg-finora-cyan text-white text-[11px] font-bold px-2 py-0.5 rounded-full z-10">
                            {{ $prod['descuento'] }}
                        </span>
                        @endif
                        <!-- Wishlist / Like Button -->
                        <button type="button" aria-label="Dar me gusta a {{ $prod['nombre'] }}"
                                class="wishlist-toggle-btn absolute top-2 right-2 p-1.5 bg-white/90 backdrop-blur rounded-full text-slate-400 hover:text-rose-500 shadow-sm transition-all z-10 cursor-pointer hover:scale-110 active:scale-95"
                                data-prod-id="{{ $prod['id'] }}"
                                onclick="event.stopPropagation(); window.finoraToggleLike?.(this);">
                            <svg class="heart-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                            </svg>
                        </button>
                        <!-- Product Image -->
                        <div class="w-full h-52 overflow-hidden rounded-2xl bg-slate-100/80 mb-4">
                            <img alt="{{ $prod['nombre'] }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-300 select-none" draggable="false" src="{{ $prod['imagen'] }}" onerror="this.onerror=null; this.src='{{ asset('images/branding/finora-icono.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-6');">
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mb-1">
                            <span class="text-finora-cyanDark font-bold uppercase tracking-wider">{{ $prod['tag'] }}</span>
                            <span class="truncate max-w-[120px]">{{ $prod['linea'] }}</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-1 line-clamp-1 group-hover:text-finora-cyan transition-colors">{{ $prod['nombre'] }}</h3>
                        <!-- Corazones / Me gusta comunitarios -->
                        <div class="flex items-center gap-1.5 mb-3 text-xs text-slate-500">
                            <div class="flex items-center text-rose-500 select-none" aria-hidden="true">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </div>
                            <span class="font-bold text-slate-700 likes-count" data-prod-id="{{ $prod['id'] }}">
                                {{ $prod['likes'] }}
                            </span>
                            <span class="text-slate-400 text-[11px]">me gusta</span>
                        </div>
                        <!-- Price and Buy Action -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-lg font-extrabold text-[#0e1b2e]">{{ $prod['precio_display'] }}</span>
                                @if($prod['precio_original_display'])
                                <span class="text-xs text-slate-400 line-through ml-1">{{ $prod['precio_original_display'] }}</span>
                                @endif
                            </div>
                            <button type="button" aria-label="Añadir {{ $prod['nombre'] }} al carrito"
                                    data-add-product="{{ $prod['id'] }}"
                                    onclick="event.stopPropagation(); window.finoraAddProduct(this)"
                                    class="w-10 h-10 rounded-full bg-finora-cyan hover:bg-finora-cyanDark text-white flex items-center justify-center shadow-md shadow-cyan-300/40 transition-transform active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty State Recovery en Portada -->
            <div id="landingEmptyCatalog" class="hidden rounded-3xl bg-white p-8 sm:p-12 text-center shadow-card border border-slate-100 mt-4">
                <div class="w-14 h-14 rounded-full bg-cyan-50 text-[#00c1de] flex items-center justify-center mx-auto mb-3.5 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-[#0e1b2e]">
                    No encontramos ese producto en la selección destacada
                </h3>
                <p id="landingEmptyMessage" class="text-xs sm:text-sm text-slate-500 mt-1.5 max-w-md mx-auto leading-relaxed">
                    Pero no te preocupes: tenemos más de 100 productos y promociones disponibles en nuestro catálogo completo por categorías.
                </p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('categorias') }}" class="px-5 py-2.5 rounded-full bg-[#00c1de] hover:bg-[#00a3bd] text-white text-xs font-bold shadow-md shadow-cyan-300/30 transition-all cursor-pointer">
                        Explorar catálogo por categorías →
                    </a>
                    <a href="{{ route('ofertas') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <path d="M12.75 3.03a.75.75 0 00-.918-.588c-.99.23-1.954.675-2.82 1.32C6.708 5.485 5.25 8.196 5.25 11.25c0 1.94.636 3.733 1.714 5.178-.057-.34-.089-.69-.089-1.049a6.002 6.002 0 013.125-5.26 7.478 7.478 0 00-.73 2.651c.365-.77 1.01-1.38 1.81-1.722a4.5 4.5 0 011.66-.318c.245 0 .487.02.723.058a5.252 5.252 0 001.277-2.735c.18-.737.228-1.503.14-2.26a.75.75 0 00-.53-.615zm-.965 9.07c-.456.326-.827.76-1.077 1.26a3.75 3.75 0 00-.308 1.48c0 1.616.98 2.99 2.378 3.518a.75.75 0 00.978-.92c-.225-.632-.236-1.312-.03-1.95.207-.638.625-1.173 1.182-1.516a.75.75 0 00-.14-1.365 3.75 3.75 0 00-2.983.493z"/>
                        </svg>
                        <span>Ver promociones de ciclo</span>
                    </a>
                </div>
            </div>
        </section>
        <!-- END: FeaturedProductsSection -->

        <!-- BEGIN: Contacto & Revista de Ciclo Section -->
        <section id="contacto" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 scroll-mt-24">
            <div class="relative overflow-hidden rounded-3xl newsletter-gradient p-6 sm:p-10 lg:p-12 shadow-soft border border-sky-100/60">
                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
                    <!-- Text and value propositions (Alineado a la izquierda, conciso y directo para evitar saturación de texto) -->
                    <div class="space-y-3 text-left max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 backdrop-blur-xs text-[#0e1b2e] text-[11px] font-bold tracking-wider uppercase shadow-xs border border-white/60">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Atención Directa • Revista Natura</span>
                        </div>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#0e1b2e] tracking-tight leading-tight">
                            ¿Quieres la Revista del Ciclo en PDF?
                        </h3>
                        <p class="text-xs sm:text-base text-slate-700 leading-relaxed font-normal">
                            Pídela directamente por WhatsApp con los lanzamientos de temporada y las promociones exclusivas de este ciclo.
                        </p>
                        <div class="pt-1 flex flex-wrap items-center justify-start gap-3 sm:gap-5 text-xs text-slate-700 font-semibold">
                            <div class="flex items-center gap-1.5">
                                <span class="text-cyan-700 font-bold">✓</span>
                                <span>Catálogo oficial en PDF</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-cyan-700 font-bold">✓</span>
                                <span>Ofertas y novedades</span>
                            </div>
                        </div>
                    </div>

                    <!-- Direct WhatsApp Action CTA -->
                    @php
                        $waPromoText = '¡Hola! Me gustaría recibir la revista digital del ciclo actual y las novedades de Natura.';
                        $waContactUrl = 'https://wa.me/' . preg_replace('/\D+/', '', config('services.whatsapp.phone')) . '?text=' . urlencode($waPromoText);
                    @endphp
                    <div class="flex flex-col items-stretch sm:items-center lg:items-end gap-2 w-full sm:w-auto shrink-0">
                        <a href="{{ $waContactUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 bg-[#0e1b2e] hover:bg-slate-900 text-white font-bold text-sm sm:text-base rounded-full shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 cursor-pointer group">
                            <svg class="w-5 h-5 text-emerald-400 fill-current group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                            </svg>
                            <span>Pedir Revista por WhatsApp</span>
                            <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                        <span class="text-[11px] text-slate-600 font-medium text-left sm:text-center">
                            Mensaje directo listo para enviar • Sin formularios
                        </span>
                    </div>
                </div>

                <!-- Subtle Floral Graphic Right Corner -->
                <div class="absolute -right-10 -bottom-10 w-44 h-44 opacity-20 pointer-events-none">
                    <svg fill="none" viewBox="0 0 200 200">
                        <path d="M100 0C100 55.2285 55.2285 100 0 100C55.2285 100 100 144.772 100 200C100 144.772 144.772 100 200 100C144.772 100 100 55.2285 100 0Z" fill="#008da2"></path>
                    </svg>
                </div>
            </div>
        </section>
        <!-- END: Contacto & Revista de Ciclo Section -->
    </main>

    <!-- BEGIN: MainFooter -->
    @include('partials.footer')
    <!-- END: MainFooter -->

    <!-- ==================== ELEMENTOS PRESERVADOS SIN VISIBILIDAD (NO ELIMINAR, SÓLO OCULTAR) ==================== -->
    <div id="fullCatalogSection" class="hidden" aria-hidden="true">
        <!-- Buscador del catálogo original -->
        <input id="catalog-search" type="search" placeholder="Buscar por nombre...">
        <div id="categoryChips">
            <button class="filter-chip active" data-cat="all">Todas</button>
            @foreach ($categoriasLanding as $cat)
                <button class="filter-chip" data-cat="{{ $cat['slug'] }}">{{ $cat['nombre'] }}</button>
            @endforeach
        </div>
        <!-- Contenedor con todos los productos de la BD -->
        <div id="products-container">
            @foreach($productosLanding as $prod)
            <div class="product-item"
                 data-id="{{ $prod['id'] }}"
                 data-cat="{{ $prod['categoria'] }}"
                 data-codigo="{{ $prod['codigo'] }}"
                 data-categoria-nombre="{{ $prod['categoriaNombre'] }}"
                 data-nombre="{{ $prod['nombre'] }}"
                 data-linea="{{ $prod['linea'] }}"
                 data-desc="{{ $prod['desc'] }}"
                 data-imagen="{{ $prod['imagen'] }}"
                 data-tag="{{ $prod['tag'] }}"
                 data-precio="{{ $prod['precio'] }}">
                 <button type="button" data-add-product="{{ $prod['id'] }}" onclick="event.stopPropagation(); window.finoraAddProduct(this)">Agregar</button>
            </div>
            @endforeach
        </div>
        <!-- Mensaje de estado vacío -->
        <div id="landingEmptyCatalog" class="hidden">
            <p id="landingEmptyMessage">No encontramos productos.</p>
        </div>
    </div>

    <!-- Asistente de IA (Preservado y oculto) -->
    <div id="aiChatLauncher" class="hidden" aria-hidden="true">
        <span id="aiChatPrompt">¿Necesitas ayuda para elegir?</span>
        <button id="aiChatBtn" type="button" onclick="document.getElementById('aiChatDialog')?.showModal()" aria-label="Asistente de compra"></button>
    </div>
    <dialog id="aiChatDialog" class="hidden fixed inset-0 m-auto w-[min(92vw,28rem)] rounded-3xl bg-white p-5 shadow-2xl backdrop:bg-black/50" aria-hidden="true">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-sm font-bold text-slate-800">Asistente Finora</h3>
            <button type="button" onclick="document.getElementById('aiChatDialog').close()">✕</button>
        </div>
        <div id="aiChatMessages" class="py-4 text-xs"></div>
        <div id="aiChatOptions" class="flex flex-wrap gap-2 pt-2 border-t"></div>
    </dialog>

    <!-- Botones flotantes (Preservados y ocultos) -->
    <div id="floating-buttons" class="hidden" aria-hidden="true">
        <a id="btn-whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', config('services.whatsapp.phone')) }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
        <button id="btn-scroll-top" type="button" onclick="window.scrollTo({top:0, behavior:'smooth'})">Top</button>
    </div>

    <!-- Selector de tema (Preservado y oculto) -->
    <div class="finora-theme-toggle hidden" aria-hidden="true"></div>

    <!-- ==================== MODAL DE PRODUCTO (FUNCIONALIDAD COMPLETA) ==================== -->
    <dialog id="productModal" aria-labelledby="modalNombre" aria-describedby="modalDesc"
            class="fixed inset-0 m-auto w-[min(94vw,32rem)] rounded-3xl bg-white shadow-2xl p-0 border border-slate-100 backdrop:bg-black/60 backdrop:backdrop-blur-xs">
        <div class="flex flex-col max-h-[90vh]">
            <div class="relative h-64 sm:h-72 w-full rounded-t-3xl overflow-hidden bg-slate-50 shrink-0 flex items-center justify-center p-4">
                <img id="modalImage" src="" alt="" class="w-full h-full object-contain select-none cursor-zoom-in transition-transform duration-300" draggable="false"/>
                <button onclick="document.getElementById('productModal').close()" class="absolute top-3.5 right-3.5 w-9 h-9 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-md text-slate-700 hover:scale-105 active:scale-95 transition-all shadow-md cursor-pointer" aria-label="Cerrar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                </button>
                <span id="modalTag" class="absolute top-3.5 left-3.5 px-3 py-1 rounded-full bg-finora-cyan text-white font-bold text-[10px] uppercase shadow-xs"></span>

                <!-- Controles interactivos de zoom preservados -->
                <div class="absolute bottom-3 right-3 flex items-center gap-1 bg-white/90 backdrop-blur-md rounded-full px-2 py-1 shadow-sm border border-slate-200/60 z-10">
                    <button id="modalZoomOut" type="button" class="w-6 h-6 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-600 font-bold text-xs" title="Reducir">-</button>
                    <button id="modalZoomReset" type="button" class="text-[10px] font-semibold text-slate-500 hover:text-slate-800 px-1" title="Restablecer">100%</button>
                    <button id="modalZoomIn" type="button" class="w-6 h-6 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-600 font-bold text-xs" title="Ampliar">+</button>
                </div>
            </div>
            <div class="p-6 flex flex-col gap-4 overflow-y-auto">
                <div>
                    <span id="modalLinea" class="text-xs font-bold uppercase tracking-wider text-finora-cyan"></span>
                    <h3 id="modalNombre" class="font-bold text-xl sm:text-2xl text-[#0e1b2e] mt-0.5"></h3>
                    <p id="modalDesc" class="text-sm text-slate-600 mt-2 leading-relaxed"></p>
                </div>
                <dl class="grid grid-cols-2 gap-2.5 rounded-2xl bg-slate-50 p-3.5 text-xs">
                    <div><dt class="text-slate-400">Código</dt><dd id="modalCodigo" class="font-bold text-slate-900"></dd></div>
                    <div><dt class="text-slate-400">Categoría</dt><dd id="modalCategoria" class="font-bold text-slate-900"></dd></div>
                    <div><dt class="text-slate-400">Proveedor</dt><dd id="modalProveedor" class="font-bold text-slate-900"></dd></div>
                    <div><dt class="text-slate-400">Precio estimado</dt><dd id="modalPrecio" class="font-bold text-finora-cyan"></dd></div>
                </dl>
                <div class="flex flex-col gap-2 pt-2 border-t border-slate-100">
                    <a id="modalWaLink" href="#" target="_blank" rel="noopener noreferrer"
                       class="w-full py-3 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-md cursor-pointer">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                        </svg>
                        <span>Consultar o pedir por WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Actualizar badge de carrito desde localStorage
            const updateCartBadge = () => {
                const badge = document.getElementById('publicCartBadge');
                if (badge) {
                    const cart = JSON.parse(localStorage.getItem('finora-carrito') || '[]');
                    const count = cart.reduce((t, i) => t + Number(i.qty || 0), 0);
                    badge.textContent = count;
                }
            };
            updateCartBadge();
            window.addEventListener('storage', updateCartBadge);

            // Modal de detalles de producto y controles de zoom
            const modalImage = document.getElementById('modalImage');
            let modalZoom = 1;
            const updateModalZoom = () => {
                if (!modalImage) return;
                modalImage.style.transform = `scale(${modalZoom})`;
                modalImage.classList.toggle('cursor-zoom-out', modalZoom > 1);
                modalImage.classList.toggle('cursor-zoom-in', modalZoom === 1);
                const zoomResetBtn = document.getElementById('modalZoomReset');
                if (zoomResetBtn) zoomResetBtn.textContent = `${Math.round(modalZoom * 100)}%`;
            };

            document.getElementById('modalZoomIn')?.addEventListener('click', (e) => {
                e.stopPropagation();
                modalZoom = Math.min(2.5, modalZoom + 0.25);
                updateModalZoom();
            });
            document.getElementById('modalZoomOut')?.addEventListener('click', (e) => {
                e.stopPropagation();
                modalZoom = Math.max(1, modalZoom - 0.25);
                updateModalZoom();
            });
            document.getElementById('modalZoomReset')?.addEventListener('click', (e) => {
                e.stopPropagation();
                modalZoom = 1;
                updateModalZoom();
            });
            modalImage?.addEventListener('wheel', (event) => {
                event.preventDefault();
                modalZoom = Math.min(2.5, Math.max(1, modalZoom + (event.deltaY < 0 ? 0.25 : -0.25)));
                updateModalZoom();
            }, { passive: false });

            window.openProductModal = function(card) {
                const modal = document.getElementById('productModal');
                if (!modal) return;
                modalZoom = 1;
                updateModalZoom();
                document.getElementById('modalImage').src = card.dataset.imagen;
                document.getElementById('modalImage').alt = card.dataset.nombre;
                document.getElementById('modalTag').textContent = card.dataset.tag;
                document.getElementById('modalLinea').textContent = card.dataset.linea;
                document.getElementById('modalNombre').textContent = card.dataset.nombre;
                document.getElementById('modalDesc').textContent = card.dataset.desc;
                document.getElementById('modalCodigo').textContent = card.dataset.codigo || 'No registrado';
                document.getElementById('modalCategoria').textContent = card.dataset.categoriaNombre || 'Catálogo general';
                document.getElementById('modalProveedor').textContent = card.dataset.tag || 'No registrado';
                document.getElementById('modalPrecio').textContent = card.dataset.precio ? `Bs ${Number(card.dataset.precio).toLocaleString('es-BO', {minimumFractionDigits: 2})}` : '$39.990';
                document.getElementById('modalWaLink').href =
                    'https://wa.me/{{ preg_replace('/\D+/', '', config('services.whatsapp.phone')) }}?text=' + encodeURIComponent('Hola deseo consultar sobre el producto: ' + card.dataset.nombre);
                modal.showModal();
            };

            const productModal = document.getElementById('productModal');
            if (productModal) {
                productModal.addEventListener('click', (e) => {
                    const rect = productModal.getBoundingClientRect();
                    if (e.clientX < rect.left || e.clientX > rect.right ||
                        e.clientY < rect.top  || e.clientY > rect.bottom) {
                        productModal.close();
                    }
                });
            }

            // Inicializar estados de likes (corazones)
            if (window.finoraInitLikes) {
                window.finoraInitLikes();
            }

            // Suscripción al newsletter
            const newsletterForm = document.getElementById('newsletterForm');
            const newsletterBtn = document.getElementById('newsletterBtn');
            const newsletterInput = document.getElementById('newsletterInput');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (newsletterBtn && newsletterInput?.value) {
                        const original = newsletterBtn.innerHTML;
                        newsletterBtn.innerHTML = '<span>¡Suscrito!</span>';
                        newsletterBtn.classList.remove('bg-[#0e1b2e]', 'hover:bg-slate-800');
                        newsletterBtn.classList.add('bg-emerald-600');
                        newsletterInput.value = '';
                        setTimeout(() => {
                            newsletterBtn.innerHTML = original;
                            newsletterBtn.classList.remove('bg-emerald-600');
                            newsletterBtn.classList.add('bg-[#0e1b2e]', 'hover:bg-slate-800');
                        }, 3000);
                    }
                });
            }

            // Carrusel Hero Interactivo con animación de diapositivas y movimiento de pelotas
            const heroSlides = document.querySelectorAll('.hero-slide');
            const heroDots = document.querySelectorAll('.hero-dot');
            const heroWrapper = document.getElementById('heroCarouselWrapper');
            let currentHeroIndex = 0;
            let heroTimer = null;

            function switchHeroSlide(targetIndex) {
                if (!heroSlides.length) return;
                currentHeroIndex = (targetIndex + heroSlides.length) % heroSlides.length;

                heroSlides.forEach((slide, i) => {
                    const isActive = i === currentHeroIndex;
                    slide.classList.toggle('opacity-100', isActive);
                    slide.classList.toggle('scale-100', isActive);
                    slide.classList.toggle('pointer-events-auto', isActive);
                    slide.classList.toggle('z-10', isActive);

                    slide.classList.toggle('opacity-0', !isActive);
                    slide.classList.toggle('scale-95', !isActive);
                    slide.classList.toggle('pointer-events-none', !isActive);
                    slide.classList.toggle('z-0', !isActive);
                });

                heroDots.forEach((dot, i) => {
                    const isActive = i === currentHeroIndex;
                    dot.setAttribute('aria-selected', String(isActive));
                    if (isActive) {
                        dot.classList.remove('w-2.5', 'bg-sky-200', 'hover:bg-sky-300');
                        dot.classList.add('w-7', 'bg-finora-cyan', 'shadow-xs');
                    } else {
                        dot.classList.remove('w-7', 'bg-finora-cyan', 'shadow-xs');
                        dot.classList.add('w-2.5', 'bg-sky-200', 'hover:bg-sky-300');
                    }
                });
            }

            function startHeroAutoplay() {
                if (heroTimer) clearInterval(heroTimer);
                heroTimer = setInterval(() => {
                    switchHeroSlide(currentHeroIndex + 1);
                }, 9800);
            }

            function pauseHeroAutoplay() {
                if (heroTimer) {
                    clearInterval(heroTimer);
                    heroTimer = null;
                }
            }

            heroDots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    switchHeroSlide(index);
                    startHeroAutoplay();
                });
            });

            if (heroWrapper) {
                heroWrapper.addEventListener('mouseenter', pauseHeroAutoplay);
                heroWrapper.addEventListener('mouseleave', startHeroAutoplay);
            }

            if (heroSlides.length > 1) {
                startHeroAutoplay();
            }

            // Control de visibilidad del botón volver arriba al desplazar cierta cantidad
            const backToTopBtn = document.getElementById('backToTopLanding');
            if (backToTopBtn) {
                const handleScroll = () => {
                    if (window.scrollY > 250) {
                        backToTopBtn.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
                        backToTopBtn.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
                    } else {
                        backToTopBtn.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                        backToTopBtn.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
                    }
                };
                window.addEventListener('scroll', handleScroll, { passive: true });
                handleScroll();

                backToTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>

    <!-- Botón flotante para ir arriba en landing -->
    <button id="backToTopLanding"
        type="button"
        class="fixed bottom-20 sm:bottom-6 right-6 z-40 w-11 h-11 flex items-center justify-center rounded-full bg-white shadow-lg border border-slate-200/80 text-slate-700 hover:bg-[#00c1de] hover:text-white hover:border-[#00c1de] transition-all duration-300 hover:scale-110 active:scale-95 cursor-pointer opacity-0 translate-y-4 pointer-events-none group"
        aria-label="Volver arriba"
        title="Volver arriba"
        onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <svg class="w-4 h-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"></path>
        </svg>
    </button>
</body>
</html>