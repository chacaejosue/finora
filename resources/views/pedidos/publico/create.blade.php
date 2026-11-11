<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bolsa de Pedidos | Catálogo Oficial Natura</title>
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
    </style>
</head>
<body class="min-h-screen bg-[#f7fafd] text-slate-800 antialiased selection:bg-[#00c1de] selection:text-white">
    <!-- Encabezado estilo luxury unificado -->
    @include('partials.header')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-10 pb-20">
        <!-- Hero Header de la Bolsa -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-slate-200/60">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 border border-cyan-200/80 text-[11px] font-bold uppercase tracking-wider text-cyan-900 shadow-xs mb-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Bolsa Oficial de Pedidos • Consultoría Natura</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0e1b2e] tracking-tight">
                    Arma tu pedido
                </h1>
                <p class="text-slate-500 text-sm sm:text-base mt-1.5 max-w-xl leading-relaxed">
                    Tu Bolsa de Compras oficial. Revisa los artículos seleccionados y formaliza tu solicitud directamente por WhatsApp.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('categorias') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-700 hover:text-[#00c1de] hover:border-cyan-300 font-bold text-xs sm:text-sm transition-all shadow-xs group">
                    <span class="group-hover:-translate-x-1 transition-transform">←</span>
                    <span>Seguir explorando catálogo</span>
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs sm:text-sm text-rose-700 font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Layout Moderno de 2 Columnas Estilo Luxury Store -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- COLUMNA IZQUIERDA: Artículos en la Bolsa (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <section class="rounded-3xl border border-slate-100/90 bg-white p-5 sm:p-7 shadow-card">
                    <!-- Header de Artículos -->
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-cyan-50 text-[#00c1de] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <h2 class="text-base sm:text-lg font-bold text-[#0e1b2e]">Artículos Seleccionados</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <span id="cartCount" class="rounded-full bg-cyan-50 border border-cyan-100 px-3 py-1 text-xs font-bold text-cyan-800">
                                0 productos
                            </span>
                            <button type="button" id="clearCartBtn" class="hidden text-xs text-slate-400 hover:text-rose-500 font-semibold px-2 py-1 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer" title="Vaciar todos los artículos">
                                Vaciar bolsa
                            </button>
                        </div>
                    </div>

                    <!-- Lista dinámica de productos -->
                    <div id="cartItems" class="mt-4 space-y-3"></div>

                    <!-- Estado Vacío -->
                    <div id="cartEmpty" class="mt-4 rounded-3xl bg-slate-50/70 border border-dashed border-slate-200/80 p-8 sm:p-12 text-center text-slate-500">
                        <div class="w-16 h-16 rounded-full bg-cyan-50 text-[#00c1de] flex items-center justify-center mx-auto mb-4 shadow-xs">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-800">Tu bolsa de compras está vacía</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                            Aún no has añadido ningún producto. Revisa nuestras fragancias, hidratantes y ofertas de ciclo disponibles.
                        </p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('categorias') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#00c1de] text-white text-xs font-bold shadow-md shadow-cyan-300/30 hover:bg-[#00a3bd] transition-all cursor-pointer">
                                <span>Explorar Catálogo</span>
                                <span>→</span>
                            </a>
                            <a href="{{ route('ofertas') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold hover:bg-rose-100 transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.75 3.03a.75.75 0 00-.918-.588c-.99.23-1.954.675-2.82 1.32C6.708 5.485 5.25 8.196 5.25 11.25c0 1.94.636 3.733 1.714 5.178-.057-.34-.089-.69-.089-1.049a6.002 6.002 0 013.125-5.26 7.478 7.478 0 00-.73 2.651c.365-.77 1.01-1.38 1.81-1.722a4.5 4.5 0 011.66-.318c.245 0 .487.02.723.058a5.252 5.252 0 001.277-2.735c.18-.737.228-1.503.14-2.26a.75.75 0 00-.53-.615zm-.965 9.07c-.456.326-.827.76-1.077 1.26a3.75 3.75 0 00-.308 1.48c0 1.616.98 2.99 2.378 3.518a.75.75 0 00.978-.92c-.225-.632-.236-1.312-.03-1.95.207-.638.625-1.173 1.182-1.516a.75.75 0 00-.14-1.365 3.75 3.75 0 00-2.983.493z"/>
                                </svg>
                                <span>Ver Ofertas de Ciclo</span>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Panel de Tranquilidad y Garantías (Trust Badges con Iconos SVG) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-cyan-50 text-[#00c1de] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800">100% Originales</h3>
                            <p class="text-[11px] text-slate-400">Directo de catálogo Natura</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800">Atención Directa</h3>
                            <p class="text-[11px] text-slate-400">Asesoría de tu consultora</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800">Entrega Acordada</h3>
                            <p class="text-[11px] text-slate-400">Punto y hora a convenir</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Resumen Financiero y Formulario Sticky (5 cols) -->
            <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">
                <section class="rounded-3xl border border-slate-100/90 bg-white p-5 sm:p-7 shadow-card" aria-labelledby="checkout-title">
                    <h2 id="checkout-title" class="text-lg sm:text-xl font-extrabold text-[#0e1b2e]">
                        Resumen del Pedido
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Ingresa tus datos para generar tu pedido y enviarlo por WhatsApp.
                    </p>

                    <!-- Resumen Financiero Destacado Arriba -->
                    <div class="mt-4 rounded-2xl bg-[#f8fcfe] border border-cyan-100/80 p-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Subtotal de selección</span>
                            <span id="orderSubtotal" class="font-bold text-slate-800">Bs 0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <span>Coordinación de despacho</span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Directa por WhatsApp</span>
                            </span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-200/60 flex items-baseline justify-between">
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Total Estimado</span>
                                <span class="text-[10px] text-slate-400">Sin pagos por adelantado</span>
                            </div>
                            <span id="orderTotal" class="text-2xl font-black text-[#00c1de]">Bs 0.00</span>
                        </div>
                    </div>

                    <!-- Formulario Minimalista Inmediatamente Accesible Sin Scroll -->
                    <form id="publicOrderForm" action="{{ route('carrito.pedido') }}" method="POST" class="mt-5 space-y-4">
                        @csrf

                        <!-- Campo Nombre -->
                        <div>
                            <label for="inputNombre" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Tu Nombre y Apellido *</span>
                            </label>
                            <input id="inputNombre" name="nombre" autocomplete="name" maxlength="100" value="{{ old('nombre') }}" required
                                   placeholder="Ej. María Fernández"
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                        </div>

                        <!-- Campo WhatsApp -->
                        <div>
                            <label for="inputTelefono" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                                </svg>
                                <span>Tu Teléfono WhatsApp *</span>
                            </label>
                            <input id="inputTelefono" type="tel" name="telefono" autocomplete="tel" inputmode="tel" maxlength="30" value="{{ old('telefono') }}" required
                                   placeholder="Ej. 70012345"
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                        </div>

                        <!-- Selector de Forma de Pago con Iconos SVG (SIN EMOJIS) -->
                        <div>
                            <span class="block text-xs font-bold text-slate-700 mb-1.5">Forma de pago preferida</span>
                            <input type="hidden" name="preferencia_pago" id="preferenciaPagoInput" value="{{ old('preferencia_pago', 'POR_CONFIRMAR') }}">
                            <div class="grid grid-cols-3 gap-2" id="pagoChips">
                                <!-- Chip 1: Por WhatsApp -->
                                <button type="button" data-pago="POR_CONFIRMAR"
                                        class="pago-chip py-2.5 px-2 rounded-2xl text-[11px] font-bold border transition-all flex flex-col items-center justify-center gap-1 cursor-pointer bg-cyan-50 border-[#00c1de] text-[#008da2] shadow-xs">
                                    <svg class="w-4 h-4 text-emerald-600 fill-current" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                                    </svg>
                                    <span>Por WhatsApp</span>
                                </button>

                                <!-- Chip 2: Efectivo o QR -->
                                <button type="button" data-pago="CONTADO"
                                        class="pago-chip py-2.5 px-2 rounded-2xl text-[11px] font-semibold border transition-all flex flex-col items-center justify-center gap-1 cursor-pointer bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Efectivo o QR</span>
                                </button>

                                <!-- Chip 3: A crédito -->
                                <button type="button" data-pago="CREDITO"
                                        class="pago-chip py-2.5 px-2 rounded-2xl text-[11px] font-semibold border transition-all flex flex-col items-center justify-center gap-1 cursor-pointer bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <span>A crédito</span>
                                </button>
                            </div>
                        </div>

                        <!-- Toggle desplegable para campos opcionales secundarios -->
                        <div class="pt-1">
                            <button type="button" id="toggleOpcionalesBtn"
                                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#00c1de] transition-colors cursor-pointer group">
                                <span id="toggleIcon" class="w-4 h-4 rounded-full bg-slate-100 group-hover:bg-cyan-50 group-hover:text-[#00c1de] flex items-center justify-center text-xs font-bold leading-none transition-colors">+</span>
                                <span>Agregar dirección o nota especial (opcional)</span>
                            </button>
                            
                            <div id="opcionalesContainer" class="hidden mt-3 space-y-3 p-4 rounded-2xl bg-slate-50/70 border border-slate-200/70 transition-all">
                                <label class="block text-xs font-semibold text-slate-700">
                                    <span>Dirección o referencia de entrega</span>
                                    <input name="direccion" autocomplete="street-address" maxlength="255" value="{{ old('direccion') }}"
                                           placeholder="Ciudad, zona, calle o lugar de encuentro"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#00c1de]">
                                </label>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="block text-xs font-semibold text-slate-700">
                                        <span>Segundo apellido</span>
                                        <input name="apellido" autocomplete="family-name" maxlength="100" value="{{ old('apellido') }}"
                                               placeholder="Tu segundo apellido"
                                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#00c1de]">
                                    </label>
                                    <label class="block text-xs font-semibold text-slate-700">
                                        <span>Correo electrónico</span>
                                        <input type="email" name="email" autocomplete="email" maxlength="255" value="{{ old('email') }}"
                                               placeholder="tu@correo.com"
                                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#00c1de]">
                                    </label>
                                </div>
                                
                                <label class="block text-xs font-semibold text-slate-700">
                                    <span>Nota o indicación especial</span>
                                    <textarea name="observaciones" rows="2"
                                              placeholder="Indicaciones para tu consultora (ej. envolver para regalo o llamar antes de entregar)..."
                                              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#00c1de]">{{ old('observaciones') }}</textarea>
                                </label>
                            </div>
                        </div>

                        <div id="cartPayload"></div>

                        <!-- Botón directo de confirmación WhatsApp -->
                        <button id="submitOrder" type="submit" disabled
                                class="w-full rounded-full bg-emerald-500 hover:bg-emerald-600 px-6 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2.5 cursor-pointer disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none hover:scale-[1.01] active:scale-95">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                            </svg>
                            <span>Confirmar pedido y enviar por WhatsApp</span>
                        </button>

                        <p class="text-center text-[11px] text-slate-400 leading-relaxed">
                            Al confirmar, tu pedido se registra de inmediato y se abre WhatsApp con el detalle listo para que tu consultora coordine el despacho.
                        </p>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <!-- Pie de página simétrico moderno -->
    @include('partials.footer')

    @php
        $productosParaCarrito = $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'precio' => (float) $producto->precio_final,
                'precio_regular' => (float) $producto->precio_venta_actual,
                'es_oferta' => (bool) $producto->es_oferta,
                'imagen' => $producto->imagen_url ?: asset('images/branding/finora-icono.png'),
                'categoria' => $producto->categoria?->nombre ?: 'Natura',
                'marca_linea' => $producto->marca_linea ?: ($producto->categoria?->nombre ?: 'Natura'),
            ];
        })->values();
    @endphp

    <script>
        window.finoraProducts = @json($productosParaCarrito);

        document.addEventListener('DOMContentLoaded', () => {
            const products = new Map(window.finoraProducts.map(product => [String(product.id), product]));
            const storageKey = 'finora-carrito';
            let cart = JSON.parse(window.localStorage.getItem(storageKey) || '[]').filter(item => products.has(String(item.id)));
            const items = document.getElementById('cartItems');
            const empty = document.getElementById('cartEmpty');
            const count = document.getElementById('cartCount');
            const clearBtn = document.getElementById('clearCartBtn');
            const payload = document.getElementById('cartPayload');
            const submit = document.getElementById('submitOrder');
            const orderSubtotal = document.getElementById('orderSubtotal');
            const orderTotal = document.getElementById('orderTotal');

            let displayCurrency = 'BOB';
            let exchangeRate = 12;

            function escapeHtml(value) {
                return String(value).replace(/[&<>"']/g, character => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[character]));
            }

            function save() {
                window.localStorage.setItem(storageKey, JSON.stringify(cart));
                window.dispatchEvent(new Event('storage'));
            }

            function formatMoney(value) {
                return `Bs ${Number(value).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }

            function render() {
                items.innerHTML = '';
                let totalItems = 0;
                let total = 0;

                cart.forEach((item, index) => {
                    const product = products.get(String(item.id));
                    totalItems += item.qty;
                    total += product.precio * item.qty;

                    const row = document.createElement('article');
                    row.className = 'flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-[#f8fcfe] border border-slate-200/70 hover:border-cyan-200 transition-colors group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3.5 min-w-0 flex-1">
                            <div class="w-16 h-16 sm:w-18 sm:h-18 shrink-0 rounded-2xl bg-white border border-slate-100 p-1 flex items-center justify-center overflow-hidden shadow-xs">
                                <img src="${escapeHtml(product.imagen)}" alt="${escapeHtml(product.nombre)}"
                                     onerror="this.onerror=null; this.src='{{ asset('images/branding/finora-icono.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-2');"
                                     class="w-full h-full object-cover rounded-xl" draggable="false">
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold text-[#00c1de] uppercase tracking-wider block truncate">${escapeHtml(product.marca_linea || product.categoria)}</span>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 line-clamp-1 group-hover:text-[#00c1de] transition-colors" title="${escapeHtml(product.nombre)}">${escapeHtml(product.nombre)}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs font-semibold text-slate-700">${formatMoney(product.precio)}</span>
                                    ${product.es_oferta && product.precio < product.precio_regular ? `<span class="text-[10px] text-slate-400 line-through">${formatMoney(product.precio_regular)}</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-3 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <!-- Stepper de cantidad -->
                            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-full p-1 shadow-xs">
                                <button type="button" data-minus="${index}"
                                        class="h-6 w-6 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center transition-all cursor-pointer"
                                        title="Restar cantidad" aria-label="Restar una unidad">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                </button>
                                <span class="w-7 text-center text-xs font-extrabold text-slate-800 select-none">${item.qty}</span>
                                <button type="button" data-plus="${index}"
                                        class="h-6 w-6 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center transition-all cursor-pointer"
                                        title="Sumar cantidad" aria-label="Sumar una unidad">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <!-- Subtotal del producto -->
                            <span class="text-sm font-extrabold text-[#0e1b2e] min-w-[70px] text-right">
                                ${formatMoney(product.precio * item.qty)}
                            </span>
                            <!-- Botón Eliminar con icono SVG -->
                            <button type="button" data-remove="${index}"
                                    class="p-2 text-slate-400 hover:text-rose-500 rounded-xl hover:bg-rose-50 transition-colors cursor-pointer"
                                    title="Quitar producto de la bolsa" aria-label="Eliminar ${escapeHtml(product.nombre)}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>`;
                    items.appendChild(row);
                });

                count.textContent = `${totalItems} producto${totalItems === 1 ? '' : 's'}`;
                empty.classList.toggle('hidden', cart.length > 0);
                if (clearBtn) clearBtn.classList.toggle('hidden', cart.length === 0);
                submit.disabled = cart.length === 0;

                const formattedTotal = formatMoney(total);
                if (orderSubtotal) orderSubtotal.textContent = formattedTotal;
                if (orderTotal) orderTotal.textContent = formattedTotal;

                payload.innerHTML = cart.map((item, index) =>
                    `<input type="hidden" name="carrito[${index}][producto_id]" value="${Number(item.id)}"><input type="hidden" name="carrito[${index}][cantidad]" value="${item.qty}">`
                ).join('');

                save();
            }

            document.addEventListener('finora:currency-changed', event => {
                displayCurrency = event.detail.currency;
                exchangeRate = event.detail.rate;
                render();
            });

            items.addEventListener('click', event => {
                const button = event.target.closest('button');
                if (!button) return;
                const index = Number(button.dataset.plus ?? button.dataset.minus ?? button.dataset.remove);
                if (button.dataset.plus !== undefined) cart[index].qty = Math.min(cart[index].qty + 1, 99);
                if (button.dataset.minus !== undefined) cart[index].qty -= 1;
                if (button.dataset.remove !== undefined || cart[index]?.qty <= 0) cart.splice(index, 1);
                render();
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    cart = [];
                    render();
                });
            }

            document.getElementById('publicOrderForm').addEventListener('submit', event => {
                if (!cart.length) event.preventDefault();
            });

            // Chips interactivos de preferencia de pago (Con iconos SVG limpios, cero emojis)
            const pagoInput = document.getElementById('preferenciaPagoInput');
            const pagoChips = document.querySelectorAll('.pago-chip');
            pagoChips.forEach(chip => {
                chip.addEventListener('click', () => {
                    const val = chip.getAttribute('data-pago');
                    if (pagoInput) pagoInput.value = val;
                    pagoChips.forEach(c => {
                        const isSelected = c === chip;
                        c.classList.toggle('bg-cyan-50', isSelected);
                        c.classList.toggle('border-[#00c1de]', isSelected);
                        c.classList.toggle('text-[#008da2]', isSelected);
                        c.classList.toggle('font-bold', isSelected);
                        c.classList.toggle('shadow-xs', isSelected);
                        c.classList.toggle('bg-white', !isSelected);
                        c.classList.toggle('border-slate-200', !isSelected);
                        c.classList.toggle('text-slate-600', !isSelected);
                        c.classList.toggle('font-semibold', !isSelected);
                    });
                });
            });

            // Toggle para campos opcionales secundarios
            const toggleBtn = document.getElementById('toggleOpcionalesBtn');
            const opcionalesContainer = document.getElementById('opcionalesContainer');
            const toggleIcon = document.getElementById('toggleIcon');
            if (toggleBtn && opcionalesContainer) {
                toggleBtn.addEventListener('click', () => {
                    const isHidden = opcionalesContainer.classList.contains('hidden');
                    opcionalesContainer.classList.toggle('hidden', !isHidden);
                    if (toggleIcon) toggleIcon.textContent = isHidden ? '−' : '+';
                });
            }

            render();
        });
    </script>
</body>
</html>
