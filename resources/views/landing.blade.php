<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Descubre tu esencia | Belleza que Inspira</title>
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

        // Productos destacados funcionales: selección al azar de 4 productos de la base de datos
        $randomDbProducts = $productos->isNotEmpty() ? $productos->shuffle()->take(4) : collect();

        $featuredProducts = [];
        $sampleRatings = ['4.8', '4.7', '4.9', '4.8'];
        $sampleReviews = ['2.3k', '1.1k', '842', '1.5k'];
        $sampleDiscounts = ['-20%', null, '-15%', null];

        foreach ($randomDbProducts as $idx => $dbProd) {
            $featuredProducts[] = [
                'id' => $dbProd->id,
                'codigo' => $dbProd->codigo,
                'nombre' => $dbProd->nombre,
                'linea' => $dbProd->linea?->nombre ?: ($dbProd->categoria?->nombre ?: 'Finora'),
                'categoriaNombre' => $dbProd->categoria?->nombre ?: 'Catálogo general',
                'desc' => $dbProd->descripcion ?: 'Producto disponible en catálogo para consulta y compra.',
                'imagen' => $dbProd->imagen_url ?: asset('images/branding/finora-icono.png'),
                'tag' => $dbProd->proveedor?->nombre ?: 'Finora',
                'precio' => $dbProd->precio_venta_actual,
                'precio_display' => 'Bs ' . number_format($dbProd->precio_venta_actual, 2),
                'precio_original_display' => $sampleDiscounts[$idx % 4] ? 'Bs ' . number_format($dbProd->precio_venta_actual * 1.25, 2) : null,
                'rating' => $sampleRatings[$idx % 4],
                'reviews' => $sampleReviews[$idx % 4],
                'descuento' => $sampleDiscounts[$idx % 4],
            ];
        }

        // Fallback en caso de base de datos vacía
        if (empty($featuredProducts)) {
            $featuredProducts = [
                ['id' => 1, 'codigo' => 'DEM-001', 'nombre' => 'Perfume Aurora', 'linea' => 'Perfumería', 'categoriaNombre' => 'Perfumería', 'desc' => 'Fragancia cautivadora con notas florales y maderosas.', 'precio' => 39.99, 'precio_display' => 'Bs 39.99', 'precio_original_display' => 'Bs 49.99', 'rating' => '4.8', 'reviews' => '2.3k', 'descuento' => '-20%', 'imagen' => asset('images/prod-aurora.jpg'), 'tag' => 'Finora'],
                ['id' => 2, 'codigo' => 'DEM-002', 'nombre' => 'Crema Hidratante Soft', 'linea' => 'Cuidado facial', 'categoriaNombre' => 'Cuidado facial', 'desc' => 'Hidratación profunda 24h para una piel suave y radiante.', 'precio' => 29.99, 'precio_display' => 'Bs 29.99', 'precio_original_display' => null, 'rating' => '4.7', 'reviews' => '1.1k', 'descuento' => null, 'imagen' => asset('images/prod-crema.jpg'), 'tag' => 'Finora'],
                ['id' => 3, 'codigo' => 'DEM-003', 'nombre' => 'Aceite Esencial Calm', 'linea' => 'Cuidado corporal', 'categoriaNombre' => 'Cuidado corporal', 'desc' => 'Aceite botánico relajante con extractos naturales.', 'precio' => 34.99, 'precio_display' => 'Bs 34.99', 'precio_original_display' => 'Bs 40.99', 'rating' => '4.9', 'reviews' => '842', 'descuento' => '-15%', 'imagen' => asset('images/prod-aceite.jpg'), 'tag' => 'Finora'],
                ['id' => 4, 'codigo' => 'DEM-004', 'nombre' => 'Colonia Essence Men', 'linea' => 'Perfumería', 'categoriaNombre' => 'Perfumería', 'desc' => 'Aroma fresco, sofisticado y de larga duración.', 'precio' => 32.99, 'precio_display' => 'Bs 32.99', 'precio_original_display' => null, 'rating' => '4.8', 'reviews' => '1.5k', 'descuento' => null, 'imagen' => asset('images/prod-colonia.jpg'), 'tag' => 'Finora'],
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
            'tag' => $producto->proveedor?->nombre ?: 'Producto',
            'precio' => $producto->precio_venta_actual,
        ]);
    @endphp

    <!-- BEGIN: MainHeader -->
    @include('partials.header')
    <!-- END: MainHeader -->

    <main>
        <!-- BEGIN: HeroSection -->
        <section class="relative overflow-hidden hero-gradient pt-8 pb-16 lg:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    <!-- Hero Text Details -->
                    <div class="lg:col-span-6 space-y-6 z-10 text-left">
                        <span class="inline-block text-xs font-bold tracking-widest uppercase text-finora-cyan bg-cyan-50/80 px-3 py-1 rounded-full border border-cyan-100">
                            Belleza que te acompaña
                        </span>
                        <div class="space-y-1">
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-[#0e1b2e] tracking-tight leading-[1.1]">
                                Finora<br>
                                Descubre tu <span class="text-gradient">esencia</span>
                            </h1>
                        </div>
                        <p class="text-slate-600 text-base sm:text-lg max-w-lg leading-relaxed font-normal">
                            Perfumes, cremas y aceites de la más alta calidad para que te sientas bien, siempre.
                        </p>
                        <div>
                            <a class="inline-flex items-center gap-3 px-8 py-3.5 bg-finora-cyan hover:bg-finora-cyanDark text-white font-semibold rounded-full shadow-lg shadow-cyan-400/30 hover:shadow-cyan-400/40 transition-all transform hover:-translate-y-0.5 cursor-pointer" href="#catalogo-destacados">
                                <span class="">Comprar ahora</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </a>
                        </div>
                        <!-- Value Propositions / Badges -->
                        <div class="pt-6 flex flex-wrap items-center gap-6 sm:gap-8 text-xs text-slate-600 font-medium">
                            <!-- Feature 1 -->
                            <div class="flex items-center gap-2">
                                <div class="text-finora-cyan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                        <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                    </svg>
                                </div>
                                <span class="">Envíos a todo el país</span>
                            </div>
                            <!-- Feature 2 -->
                            <div class="flex items-center gap-2">
                                <div class="text-finora-cyan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                    </svg>
                                </div>
                                <span class="">Compra segura</span>
                            </div>
                            <!-- Feature 3 -->
                            <div class="flex items-center gap-2">
                                <div class="text-finora-cyan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                    </svg>
                                </div>
                                <span class="">Soporte 24/7</span>
                            </div>
                        </div>
                    </div>
                    <!-- Hero Graphic Composition Carousel -->
                    <div class="lg:col-span-6 relative flex flex-col justify-center items-center pt-4 lg:pt-0" id="heroCarouselWrapper">
                        <!-- Soft Radial Backlight Glow -->
                        <div class="absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-cyan-200/50 filter blur-3xl -z-10 pointer-events-none"></div>

                        <!-- Slides Stage Container -->
                        <div class="relative w-full max-w-md sm:max-w-lg lg:max-w-xl min-h-[300px] sm:min-h-[380px] lg:min-h-[420px] flex items-end justify-center">

                            <!-- Slide 1 (Colección con pedestal) -->
                            <div class="hero-slide absolute inset-0 flex flex-col items-center justify-end transition-all duration-700 ease-out opacity-100 scale-100 z-10" data-slide-index="0">
                                <div class="relative w-full flex items-end justify-center">
                                    <img alt="Colección Finora Cosméticos y Perfumería"
                                         class="w-full max-h-[360px] sm:max-h-[410px] object-contain select-none drop-shadow-[0_15px_30px_rgba(0,193,222,0.22)] transition-transform duration-700 hover:scale-[1.02]"
                                         draggable="false"
                                         src="{{ asset('images/hero-pedestal.png') }}">
                                </div>
                            </div>

                            <!-- Slide 2 (Cosmética, labial, polvo compacto y roll-on) -->
                            <div class="hero-slide absolute inset-0 flex flex-col items-center justify-end transition-all duration-700 ease-out opacity-0 scale-95 pointer-events-none z-0" data-slide-index="1">
                                <div class="relative w-full flex items-center justify-center">
                                    <img alt="Cuidado Personal y Maquillaje Finora"
                                         class="w-full max-h-[360px] sm:max-h-[410px] object-contain select-none drop-shadow-[0_8px_24px_rgba(0,193,222,0.18)] transition-transform duration-700 hover:scale-[1.02]"
                                         draggable="false"
                                         src="{{ asset('images/hero-slide-2.png') }}">
                                </div>
                            </div>

                            <!-- Slide 3 (Perfume cristal, crema facial, jabón y protector solar) -->
                            <div class="hero-slide absolute inset-0 flex flex-col items-center justify-end transition-all duration-700 ease-out opacity-0 scale-95 pointer-events-none z-0" data-slide-index="2">
                                <div class="relative w-full flex items-center justify-center">
                                    <img alt="Fragancias Exclusivas y Cuidado Facial Finora"
                                         class="w-full max-h-[360px] sm:max-h-[410px] object-contain select-none drop-shadow-[0_8px_24px_rgba(0,193,222,0.18)] transition-transform duration-700 hover:scale-[1.02]"
                                         draggable="false"
                                         src="{{ asset('images/hero-slide-3.png') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pagination Dots (Pelotas interactivas y animadas) -->
                <div class="flex justify-center items-center gap-2.5 pt-8" id="heroPagination" role="tablist" aria-label="Navegación de imágenes del hero">
                    <button type="button" class="hero-dot w-7 h-2.5 rounded-full bg-finora-cyan transition-all duration-300 shadow-xs cursor-pointer" data-slide-target="0" role="tab" aria-selected="true" aria-label="Ver diapositiva 1"></button>
                    <button type="button" class="hero-dot w-2.5 h-2.5 rounded-full bg-sky-200 hover:bg-sky-300 transition-all duration-300 cursor-pointer" data-slide-target="1" role="tab" aria-selected="false" aria-label="Ver diapositiva 2"></button>
                    <button type="button" class="hero-dot w-2.5 h-2.5 rounded-full bg-sky-200 hover:bg-sky-300 transition-all duration-300 cursor-pointer" data-slide-target="2" role="tab" aria-selected="false" aria-label="Ver diapositiva 3"></button>
                </div>
            </div>
        </section>
        <!-- END: HeroSection -->

        <!-- BEGIN: CategoriesSection (Contenedor unificado con divisores y esquinas internas redondeadas) -->
        <section class="py-10 sm:py-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" id="categorias">
            <div class="bg-white rounded-3xl shadow-soft border border-slate-100/80 p-3 sm:p-5 lg:py-6 lg:px-2">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 divide-y sm:divide-y-0 lg:divide-x divide-slate-200/60">
                    @foreach($categoryItems as $cat)
                    <a class="group flex flex-col items-center text-center p-3 sm:p-4 hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" href="{{ $cat['url'] }}">
                        <!-- Cuadrado interno con esquinas pronunciadamente redondeadas -->
                        <div class="w-full max-w-[155px] aspect-[4/3] sm:h-28 overflow-hidden rounded-[1.5rem] bg-gradient-to-b from-sky-50 via-cyan-50/40 to-slate-100 mb-3 transition-transform duration-300 group-hover:scale-105 shadow-xs">
                            <img alt="{{ $cat['nombre'] }}" class="w-full h-full object-cover object-center select-none" draggable="false" src="{{ $cat['imagen'] }}" onerror="this.onerror=null; this.src='{{ asset('images/branding/finora-icono.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-4');">
                        </div>
                        <h3 class="text-sm sm:text-base font-semibold text-slate-800 group-hover:text-finora-cyan transition-colors">{{ $cat['nombre'] }}</h3>
                        <span class="text-xs text-finora-cyan font-medium flex items-center justify-center gap-1 mt-1 group-hover:gap-1.5 transition-all">
                            Explora <span>→</span>
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- END: CategoriesSection -->

        <!-- BEGIN: PromoBannerSection -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16" id="ofertas">
            <div class="relative overflow-hidden rounded-3xl banner-gradient px-6 pt-8 pb-0 sm:px-10 sm:pt-10 lg:px-14 lg:pt-12 border border-sky-100/50 shadow-soft">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Text and CTA column -->
                    <div class="lg:col-span-6 z-10 space-y-4 pb-8 lg:pb-12">
                        <span class="text-xs font-bold tracking-wider uppercase text-cyan-600">
                            OFERTA ESPECIAL
                        </span>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-[#0e1b2e] leading-tight">
                            Cuida tu piel,<br>realza tu belleza
                        </h2>
                        <p class="text-slate-600 text-sm sm:text-base pb-2 font-normal">
                            Hasta 40% OFF en cremas seleccionadas.
                        </p>
                        <div>
                            <a class="inline-flex items-center gap-2.5 px-7 py-3 bg-[#0e1b2e] hover:bg-slate-800 text-white font-medium rounded-full shadow-md transition-transform hover:-translate-y-0.5 cursor-pointer" href="#catalogo-destacados">
                                <span class="">Comprar oferta</span>
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
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20" id="catalogo-destacados">
            <!-- Section Title & Link -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 pb-3 border-b border-slate-200/60 gap-4">
                <div>
                    <span class="text-xs font-bold tracking-wider uppercase text-finora-cyan block mb-1">
                        Nuestros Favoritos
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#0e1b2e]">
                        Lo que todos aman
                    </h2>
                </div>
                <a class="text-sm font-semibold text-[#0e1b2e] hover:text-finora-cyan flex items-center gap-1.5 transition-colors group cursor-pointer" href="{{ route('categorias') }}">
                    Ver todos los productos
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $prod)
                <div class="product-item bg-white rounded-3xl p-4 sm:p-5 shadow-sm hover:shadow-card transition-all duration-300 border border-slate-100/90 flex flex-col justify-between group cursor-pointer"
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
                        <!-- Wishlist Button -->
                        <button type="button" aria-label="Favorito" class="wishlist-toggle-btn absolute top-2 right-2 p-1.5 bg-white/80 backdrop-blur rounded-full text-slate-400 hover:text-rose-500 shadow-sm transition-colors z-10 cursor-pointer"
                                data-prod-id="{{ $prod['id'] }}"
                                onclick="event.stopPropagation();">
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
                        <h3 class="font-bold text-slate-900 text-base mb-1 line-clamp-1 group-hover:text-finora-cyan transition-colors">{{ $prod['nombre'] }}</h3>
                        <!-- Rating -->
                        <div class="flex items-center gap-1 mb-3 text-xs text-slate-500">
                            <div class="flex text-amber-400">
                                ★★★★★
                            </div>
                            <span class="font-semibold text-slate-700">{{ $prod['rating'] }}</span>
                            <span class="">({{ $prod['reviews'] }})</span>
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
        </section>
        <!-- END: FeaturedProductsSection -->

        <!-- BEGIN: NewsletterSection -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="relative overflow-hidden rounded-3xl newsletter-gradient p-6 sm:p-10 shadow-soft">
                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                    <!-- Logo & Invite message -->
                    <div class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 lg:gap-6">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ asset('images/branding/finora-isotipo.png') }}" class="w-8 h-8 object-contain" alt="Finora">
                            <span class="text-2xl font-bold tracking-tight text-[#0e1b2e]">Finora</span>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-[#0e1b2e]">Sé parte de Finora</h3>
                            <p class="text-xs sm:text-sm text-slate-700">Recibe novedades, ofertas y consejos de belleza.</p>
                        </div>
                    </div>
                    <!-- Subscription Input Group -->
                    <form id="newsletterForm" class="w-full lg:w-auto flex-1 max-w-md flex items-center bg-white rounded-full p-1.5 shadow-sm border border-white/60">
                        <input id="newsletterInput" class="w-full bg-transparent px-4 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 border-none focus:ring-0 focus:outline-none" placeholder="Tu correo electrónico" required="" type="email">
                        <button id="newsletterBtn" class="shrink-0 px-5 sm:px-6 py-2.5 bg-[#0e1b2e] hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold rounded-full flex items-center gap-1.5 transition-colors cursor-pointer" type="submit">
                            <span class="">Suscribirme</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                <!-- Subtle Floral Graphic Right Corner -->
                <div class="absolute -right-10 -bottom-10 w-44 h-44 opacity-25 pointer-events-none">
                    <svg fill="none" viewBox="0 0 200 200">
                        <path d="M100 0C100 55.2285 55.2285 100 0 100C55.2285 100 100 144.772 100 200C100 144.772 144.772 100 200 100C144.772 100 100 55.2285 100 0Z" fill="#008da2"></path>
                    </svg>
                </div>
            </div>
        </section>
        <!-- END: NewsletterSection -->
    </main>

    <!-- BEGIN: MainFooter -->
    <footer class="bg-white border-t border-sky-100/70 pt-16 pb-8 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Columns Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 pb-12 border-b border-slate-100">
                <!-- Col 1: Brand & Bio -->
                <div class="space-y-4">
                    <a aria-label="Finora Inicio" class="flex items-center gap-2" href="{{ route('landing') }}">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" class="w-8 h-8 object-contain" alt="Finora">
                        <span class="text-2xl font-bold tracking-tight text-[#0e1b2e]">Finora</span>
                    </a>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-sm">
                        Inspiramos tu día a día con productos pensados especialmente para resaltar tu belleza auténtica.
                    </p>
                </div>
                <!-- Col 2: Enlaces -->
                <div class="space-y-3 md:justify-self-center">
                    <h4 class="font-bold text-[#0e1b2e] text-xs uppercase tracking-wider">Enlaces</h4>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-500">
                        <li><a class="hover:text-finora-cyan transition-colors" href="{{ route('landing') }}">Inicio</a></li>
                        <li><a class="hover:text-finora-cyan transition-colors" href="{{ route('categorias') }}">Categorías</a></li>
                        <li><a class="hover:text-finora-cyan transition-colors" href="#ofertas">Ofertas</a></li>
                        <li><a class="hover:text-finora-cyan transition-colors" href="https://wa.me/{{ preg_replace('/\D+/', '', config('services.whatsapp.phone')) }}" target="_blank" rel="noopener noreferrer">Contacto</a></li>
                    </ul>
                </div>
                <!-- Col 3: Ayuda -->
                <div class="space-y-3 md:justify-self-end">
                    <h4 class="font-bold text-[#0e1b2e] text-xs uppercase tracking-wider">Ayuda</h4>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-500">
                        <li><button type="button" onclick="document.getElementById('policyModalFaq')?.showModal()" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Preguntas frecuentes</button></li>
                        <li><button type="button" onclick="document.getElementById('policyModalEnvios')?.showModal()" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Envíos y devoluciones</button></li>
                        <li><button type="button" onclick="document.getElementById('policyModalTerminos')?.showModal()" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Términos y condiciones</button></li>
                        <li><button type="button" onclick="document.getElementById('policyModalPrivacidad')?.showModal()" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Política de privacidad</button></li>
                    </ul>
                </div>
            </div>
            <!-- Bottom Legal Copyright Bar -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <p class="">© 2026 Finora. Todos los derechos reservados.</p>
                <a class="text-finora-cyan font-medium flex items-center gap-1 hover:underline" href="#">
                    <span class="">Belleza que inspira</span>
                    <span class="">→</span>
                </a>
            </div>
        </div>
    </footer>
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
                 <button type="button" data-add-product="{{ $prod['id'] }}" onclick="window.finoraAddProduct(this)">Agregar</button>
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

    <!-- ==================== MODALES DE AYUDA Y POLÍTICAS (PRESERVADOS) ==================== -->
    <dialog id="policyModalFaq" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-xl text-slate-900">Preguntas Frecuentes</h3>
            <button onclick="document.getElementById('policyModalFaq').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            </button>
        </div>
        <div class="py-4 flex flex-col gap-4 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-900">¿Cómo realizo un pedido?</h4>
                <p class="mt-1 text-xs leading-relaxed">Puedes añadir los productos a tu carrito y completar el pedido en línea, o hacer clic en "Consultar por WhatsApp" para ser atendido directamente por tu consultor independiente.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">¿Cuáles son las formas de pago?</h4>
                <p class="mt-1 text-xs leading-relaxed">Aceptamos pago contra entrega (efectivo), transferencias bancarias y código QR Simple en Bolivia.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">¿Los productos son 100% originales?</h4>
                <p class="mt-1 text-xs leading-relaxed">Sí, todos los productos provienen de distribuidores autorizados con sellos y fechas de vencimiento garantizadas.</p>
            </div>
        </div>
    </dialog>

    <dialog id="policyModalEnvios" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-xl text-slate-900">Envíos y Devoluciones</h3>
            <button onclick="document.getElementById('policyModalEnvios').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            </button>
        </div>
        <div class="py-4 flex flex-col gap-4 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-900">Tiempos y modalidades de entrega</h4>
                <p class="mt-1 text-xs leading-relaxed">Las entregas urbanas locales se coordinan en un plazo de 24 a 48 horas tras confirmar el pedido. Para envíos interdepartamentales en Bolivia, utilizamos encomiendas y mensajería con código de seguimiento.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">Política de devoluciones</h4>
                <p class="mt-1 text-xs leading-relaxed">Si recibes un producto con falla de fábrica o envoltura dañada, puedes solicitar el cambio inmediato comunicándote dentro de las 48 horas posteriores a la recepción con tu consultor.</p>
            </div>
        </div>
    </dialog>

    <dialog id="policyModalTerminos" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-xl text-slate-900">Términos y Condiciones</h3>
            <button onclick="document.getElementById('policyModalTerminos').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            </button>
        </div>
        <div class="py-4 flex flex-col gap-4 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-900">Alcance del Catálogo Digital</h4>
                <p class="mt-1 text-xs leading-relaxed">Finora es una plataforma tecnológica para consultores independientes. Los precios exhibidos son precios de referencia comercial sujetos a disponibilidad de inventario.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">Confirmación y Entrega</h4>
                <p class="mt-1 text-xs leading-relaxed">El pedido se formaliza al coordinar la entrega y forma de pago con el consultor. Nos reservamos el derecho de ajustar promociones vigentes según stock.</p>
            </div>
        </div>
    </dialog>

    <dialog id="policyModalPrivacidad" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-xl text-slate-900">Política de Privacidad</h3>
            <button onclick="document.getElementById('policyModalPrivacidad').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            </button>
        </div>
        <div class="py-4 flex flex-col gap-4 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-900">Protección de Datos Personales</h4>
                <p class="mt-1 text-xs leading-relaxed">La información suministrada por los clientes (nombre, teléfono, dirección y correo electrónico) se utiliza exclusivamente para la gestión y despacho de sus pedidos.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-900">Confidencialidad</h4>
                <p class="mt-1 text-xs leading-relaxed">No compartimos ni vendemos tu información a terceros ni a plataformas de publicidad externa bajo ninguna circunstancia.</p>
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

            // Toggle de menú móvil responsive
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMenuDrawer = document.getElementById('mobileMenuDrawer');
            if (mobileMenuToggle && mobileMenuDrawer) {
                mobileMenuToggle.addEventListener('click', () => {
                    mobileMenuDrawer.classList.toggle('hidden');
                });
            }

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

            // Wishlist hearts
            const storedWishlist = JSON.parse(localStorage.getItem('finora-favoritos') || '[]');
            document.querySelectorAll('.wishlist-toggle-btn').forEach(btn => {
                const prodId = btn.getAttribute('data-prod-id');
                const icon = btn.querySelector('.heart-icon');
                if (storedWishlist.includes(prodId)) {
                    btn.classList.add('text-rose-500');
                    btn.classList.remove('text-slate-400');
                    if (icon) icon.setAttribute('fill', 'currentColor');
                }
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    let wishlist = JSON.parse(localStorage.getItem('finora-favoritos') || '[]');
                    const index = wishlist.indexOf(prodId);
                    if (index > -1) {
                        wishlist.splice(index, 1);
                        btn.classList.remove('text-rose-500');
                        btn.classList.add('text-slate-400');
                        if (icon) icon.setAttribute('fill', 'none');
                    } else {
                        wishlist.push(prodId);
                        btn.classList.add('text-rose-500');
                        btn.classList.remove('text-slate-400');
                        if (icon) icon.setAttribute('fill', 'currentColor');
                    }
                    localStorage.setItem('finora-favoritos', JSON.stringify(wishlist));
                });
            });

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
        });
    </script>
</body>
</html>