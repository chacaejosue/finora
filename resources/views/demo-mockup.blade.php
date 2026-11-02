<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Finora - Vista Previa de Maqueta (Mockup Demo)</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    
    <!-- Tipografías de Finora -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-heading { font-family: 'Outfit', sans-serif; }
        .font-sans { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="bg-[#F4F7FB] text-slate-800 font-sans antialiased min-h-screen selection:bg-cyan-500 selection:text-white">

    <!-- Barra de utilidades superior (no sale en la foto si activas Modo Captura) -->
    <header id="controlsBar" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs py-3 px-4 sm:px-8">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-7 w-auto object-contain"/>
                <div>
                    <h1 class="text-xs font-bold text-slate-900 leading-tight">Vista Previa de Maqueta Demo</h1>
                    <p class="text-[10px] text-slate-500">Módulo: Ventas a crédito, pedidos con reserva y cobranzas</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleCleanMode()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-colors cursor-pointer shadow-xs" title="Oculta esta barra y paneles para sacar captura perfecta">
                    <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                    <span>Modo Captura (Foto limpia)</span>
                </button>
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Ir a Inicio</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-12">

        <!-- Notificación flotante informativa -->
        <div id="noticeBanner" class="p-4 rounded-2xl bg-blue-50 border border-blue-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-blue-900">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-blue-600 text-lg">verified</span>
                <span>
                    <strong>100% fiel a Finora:</strong> Muestra métricas de venta directa de catálogo, gestión de créditos en cuotas con amortización, reserva de stock de pedidos y tasa oficial BCB (6.96 Bs/USD).
                </span>
            </div>
            <span class="font-mono text-[11px] text-blue-700 font-semibold bg-white px-2.5 py-1 rounded-md border border-blue-100 shrink-0">
                Archivo: resources/views/temp-finora-mockup.blade.php
            </span>
        </div>

        <!-- ÁREA PRINCIPAL DE CAPTURA / SCREENSHOT -->
        <section id="captureStage" class="relative rounded-3xl bg-white border border-slate-200 shadow-xl p-6 sm:p-12 overflow-hidden flex flex-col items-center justify-center">
            
            <!-- Resplandores ambientales de fondo para que la foto quede profesional y atractiva -->
            <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-cyan-400/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-blue-600/15 blur-3xl pointer-events-none"></div>

            <div class="w-full max-w-md relative z-10">
                <!-- INCLUSIÓN DIRECTA DEL MOCKUP -->
                @include('temp-finora-mockup')
            </div>

            <!-- Botón flotante para salir del modo foto -->
            <button id="exitCleanBtn" type="button" onclick="toggleCleanMode()" class="hidden fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-slate-900 text-white text-xs font-bold shadow-2xl hover:scale-105 transition-transform cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">close</span>
                <span>Restaurar vista completa</span>
            </button>
        </section>

        <!-- SECCIÓN DE CÓDIGO LISTO PARA COPIAR -->
        <section id="codeSection" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-heading text-lg font-bold text-slate-900">Código fuente de la maqueta</h2>
                    <p class="text-xs text-slate-500">Copia la versión que mejor se adapte a tu stack (React / JSX o Blade / HTML):</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tarjeta 1: Versión React / JSX (Framer Motion) -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                            <span class="font-bold text-xs text-slate-800">React / Next.js (JSX con motion.div)</span>
                        </div>
                        <button onclick="copySnippet('reactCode', this)" class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 py-1 px-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">content_copy</span>
                            <span>Copiar JSX</span>
                        </button>
                    </div>
                    <pre class="p-4 text-[11px] font-mono text-slate-700 bg-slate-900 text-slate-100 overflow-x-auto max-h-96"><code id="reactCode">&lt;/* Columna derecha: Maqueta visual e interfaz destacada Finora */&gt;
&lt;motion.div
  className="lg:col-span-5 relative flex flex-col items-center lg:-translate-y-6 w-full max-w-lg mx-auto overflow-hidden"
  initial=@{{ opacity: 0, scale: 0.96 }}
  animate=@{{ opacity: 1, scale: 1 }}
  transition=@{{ duration: 0.5, delay: 0.1 }}
&gt;
  &lt;div className="relative w-full rounded-2xl bg-surface-container-lowest border border-surface-container-high/70 p-3 sm:p-4 shadow-xl overflow-hidden"&gt;
    &lt;div className="flex items-center justify-between gap-2 pb-3 px-1 border-b border-surface-container-low mb-3"&gt;
      &lt;div className="flex items-center gap-1.5 min-w-0"&gt;
        &lt;span className="w-2.5 h-2.5 rounded-full bg-rose-500/80 shrink-0" /&gt;
        &lt;span className="w-2.5 h-2.5 rounded-full bg-amber-400/80 shrink-0" /&gt;
        &lt;span className="w-2.5 h-2.5 rounded-full bg-emerald-500/80 shrink-0" /&gt;
        &lt;span className="ml-1 sm:ml-2 font-mono text-[10px] sm:text-xs text-outline truncate max-w-[110px] xs:max-w-[140px] sm:max-w-none"&gt;
          finora.local/panel/ventas
        &lt;/span&gt;
      &lt;/div&gt;
      &lt;span className="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold px-2 sm:px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 shrink-0"&gt;
        &lt;span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" /&gt; Espacio: Ventas &amp; Cobranzas
      &lt;/span&gt;
    &lt;/div&gt;

    &lt;div className="overflow-hidden rounded-xl bg-surface-container-low border border-surface-container-high/30 shadow-inner p-3.5 space-y-3"&gt;
      &lt;div className="flex items-center justify-between gap-2"&gt;
        &lt;div className="flex items-center gap-2 min-w-0"&gt;
          {/* Isotipo vectorial oficial Finora */}
          &lt;div className="w-7 h-7 rounded-lg bg-white shadow-xs p-1 flex items-center justify-center shrink-0 border border-slate-100"&gt;
            &lt;svg viewBox="0 0 100 100" className="w-full h-full" fill="none"&gt;
              &lt;defs&gt;
                &lt;linearGradient id="finStem" x1="0%" y1="0%" x2="50%" y2="100%"&gt;
                  &lt;stop offset="0%" stopColor="#0238B8" /&gt;
                  &lt;stop offset="100%" stopColor="#008FFF" /&gt;
                &lt;/linearGradient&gt;
                &lt;linearGradient id="finCyan" x1="0%" y1="100%" x2="100%" y2="0%"&gt;
                  &lt;stop offset="0%" stopColor="#00F8E7" /&gt;
                  &lt;stop offset="100%" stopColor="#00A3FF" /&gt;
                &lt;/linearGradient&gt;
              &lt;/defs&gt;
              &lt;path d="M22 18 C22 14, 26 10, 31 10 L48 10 C62 10, 72 20, 72 34 C72 45, 64 54, 53 57 L53 82 C53 86, 49 90, 44 90 C39 90, 35 86, 35 82 L35 27 C35 22, 31 18, 22 18 Z" fill="url(#finStem)" /&gt;
              &lt;path d="M38 34 L76 34 C82 34, 87 39, 87 45 C87 51, 82 56, 76 56 L46 56" stroke="url(#finCyan)" strokeWidth="12" strokeLinecap="round" /&gt;
            &lt;/svg&gt;
          &lt;/div&gt;
          &lt;div className="min-w-0"&gt;
            &lt;p className="font-heading text-xs font-bold text-on-surface truncate"&gt;Control Comercial 360°&lt;/p&gt;
            &lt;p className="text-[10px] text-on-surface-variant truncate"&gt;Ciclo activo • Trazabilidad de catálogo y cuotas&lt;/p&gt;
          &lt;/div&gt;
        &lt;/div&gt;
        &lt;span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#0062FF]/10 text-[#0062FF] font-mono text-[10px] font-bold shrink-0 border border-[#0062FF]/15"&gt;
          &lt;TrendingUp className="w-3 h-3" /&gt; BCB: 6.96 Bs
        &lt;/span&gt;
      &lt;/div&gt;

      &lt;div className="grid grid-cols-3 gap-2"&gt;
        &lt;div className="rounded-lg bg-surface-container-lowest border border-surface-container-high/40 p-2.5"&gt;
          &lt;p className="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant"&gt;Ventas ciclo&lt;/p&gt;
          &lt;p className="mt-1 font-heading text-xs sm:text-sm font-extrabold text-on-surface"&gt;Bs 8,450.00&lt;/p&gt;
          &lt;p className="mt-0.5 font-mono text-[9px] font-semibold text-[#0062FF]"&gt;+18.4% mes&lt;/p&gt;
        &lt;/div&gt;
        &lt;div className="rounded-lg bg-surface-container-lowest border border-[#0062FF]/25 p-2.5"&gt;
          &lt;p className="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant"&gt;Por cobrar&lt;/p&gt;
          &lt;p className="mt-1 font-heading text-xs sm:text-sm font-extrabold text-[#0062FF]"&gt;Bs 2,140.00&lt;/p&gt;
          &lt;p className="mt-0.5 text-[9px] text-on-surface-variant"&gt;4 créditos activos&lt;/p&gt;
        &lt;/div&gt;
        &lt;div className="rounded-lg bg-surface-container-lowest border border-surface-container-high/40 p-2.5"&gt;
          &lt;p className="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant"&gt;Cobranzas&lt;/p&gt;
          &lt;p className="mt-1 font-heading text-xs sm:text-sm font-extrabold text-on-surface"&gt;Bs 6,310.00&lt;/p&gt;
          &lt;p className="mt-0.5 text-[9px] text-emerald-600 dark:text-emerald-400 font-medium"&gt;92% puntual&lt;/p&gt;
        &lt;/div&gt;
      &lt;/div&gt;

      &lt;div className="rounded-lg bg-surface-container-lowest border border-surface-container-high/40 p-3 space-y-2.5"&gt;
        &lt;div className="flex items-center justify-between"&gt;
          &lt;span className="text-[11px] font-bold text-on-surface"&gt;Créditos y pedidos recientes&lt;/span&gt;
          &lt;span className="font-mono text-[10px] font-semibold text-[#0062FF]"&gt;Kardex &amp; Cuotas&lt;/span&gt;
        &lt;/div&gt;
        &lt;div className="space-y-2"&gt;
          &lt;div className="p-2 rounded-lg bg-surface-container-low/70 border border-surface-container-high/30"&gt;
            &lt;div className="flex items-center justify-between text-xs"&gt;
              &lt;span className="font-semibold text-on-surface truncate pr-1"&gt;Carla Ríos • Kaiak Urbe&lt;/span&gt;
              &lt;span className="font-mono text-[10px] font-bold text-[#0062FF] shrink-0"&gt;Cuota 2/3 (67%)&lt;/span&gt;
            &lt;/div&gt;
            &lt;div className="mt-1.5 h-1.5 w-full rounded-full bg-surface-container-high overflow-hidden"&gt;
              &lt;div className="h-full w-2/3 rounded-full bg-gradient-to-r from-[#0062FF] to-[#00D2DF]" /&gt;
            &lt;/div&gt;
          &lt;/div&gt;

          &lt;div className="flex items-center justify-between gap-2 px-1 pt-0.5"&gt;
            &lt;div className="flex items-center gap-2 min-w-0"&gt;
              &lt;div className="w-6 h-6 rounded-md bg-emerald-500/15 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-500/20"&gt;
                &lt;Check className="w-3.5 h-3.5" /&gt;
              &lt;/div&gt;
              &lt;div className="min-w-0"&gt;
                &lt;p className="text-[11px] font-semibold text-on-surface truncate"&gt;Recibo #PAG-184 • Abono en cuota&lt;/p&gt;
                &lt;p className="text-[9px] text-on-surface-variant truncate"&gt;QR / Banco • Bruno Mendoza (Venta #042)&lt;/p&gt;
              &lt;/div&gt;
            &lt;/div&gt;
            &lt;span className="font-mono text-[10px] font-bold text-emerald-600 dark:text-emerald-400 shrink-0"&gt;+Bs 210.00&lt;/span&gt;
          &lt;/div&gt;

          &lt;div className="flex items-center justify-between gap-2 px-1 pt-0.5 border-t border-surface-container-high/20"&gt;
            &lt;div className="flex items-center gap-2 min-w-0"&gt;
              &lt;div className="w-6 h-6 rounded-md bg-[#0062FF]/15 text-[#0062FF] flex items-center justify-center shrink-0 border border-[#0062FF]/20"&gt;
                &lt;PackageCheck className="w-3.5 h-3.5" /&gt;
              &lt;/div&gt;
              &lt;div className="min-w-0"&gt;
                &lt;p className="text-[11px] font-semibold text-on-surface truncate"&gt;Pedido #PED-305 • Reserva confirmada&lt;/p&gt;
                &lt;p className="text-[9px] text-on-surface-variant truncate"&gt;Ana López • 2 productos (Cuidado facial)&lt;/p&gt;
              &lt;/div&gt;
            &lt;/div&gt;
            &lt;span className="font-mono text-[10px] font-bold text-on-surface shrink-0"&gt;Bs 380.00&lt;/span&gt;
          &lt;/div&gt;
        &lt;/div&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/motion.div&gt;</code></pre>
                </div>

                <!-- Tarjeta 2: Versión Laravel Blade / HTML nativo -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="font-bold text-xs text-slate-800">Laravel Blade (HTML + Tailwind)</span>
                        </div>
                        <button onclick="copySnippet('bladeCode', this)" class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 py-1 px-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">content_copy</span>
                            <span>Copiar Blade</span>
                        </button>
                    </div>
                    <pre class="p-4 text-[11px] font-mono text-slate-700 bg-slate-900 text-slate-100 overflow-x-auto max-h-96"><code id="bladeCode">&lt;!-- Puedes incluirlo en Blade con: &#64;include('temp-finora-mockup') --&gt;
&lt;div class="lg:col-span-5 relative flex flex-col items-center lg:-translate-y-6 w-full max-w-lg mx-auto overflow-hidden"&gt;
  &lt;div class="relative w-full rounded-2xl bg-white border border-slate-200/80 p-3 sm:p-4 shadow-xl overflow-hidden"&gt;
    
    &lt;div class="flex items-center justify-between gap-2 pb-3 px-1 border-b border-slate-100 mb-3"&gt;
      &lt;div class="flex items-center gap-1.5 min-w-0"&gt;
        &lt;span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 shrink-0"&gt;&lt;/span&gt;
        &lt;span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 shrink-0"&gt;&lt;/span&gt;
        &lt;span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 shrink-0"&gt;&lt;/span&gt;
        &lt;span class="ml-1 sm:ml-2 font-mono text-[10px] sm:text-xs text-slate-500 truncate"&gt;
          finora.local/panel/ventas
        &lt;/span&gt;
      &lt;/div&gt;
      &lt;span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold px-2 sm:px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/70 shrink-0"&gt;
        &lt;span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"&gt;&lt;/span&gt; Espacio: Ventas &amp; Cobranzas
      &lt;/span&gt;
    &lt;/div&gt;

    &lt;div class="overflow-hidden rounded-xl bg-slate-50/80 border border-slate-200/50 shadow-inner p-3.5 space-y-3"&gt;
      &lt;div class="flex items-center justify-between gap-2"&gt;
        &lt;div class="flex items-center gap-2 min-w-0"&gt;
          &lt;div class="w-7 h-7 rounded-lg bg-white shadow-xs p-1 flex items-center justify-center shrink-0 border border-slate-100"&gt;
            &lt;svg viewBox="0 0 100 100" class="w-full h-full" fill="none"&gt;
              &lt;defs&gt;
                &lt;linearGradient id="finStemBlade" x1="0%" y1="0%" x2="50%" y2="100%"&gt;
                  &lt;stop offset="0%" stop-color="#0238B8"/&gt;
                  &lt;stop offset="100%" stop-color="#008FFF"/&gt;
                &lt;/linearGradient&gt;
                &lt;linearGradient id="finCyanBlade" x1="0%" y1="100%" x2="100%" y2="0%"&gt;
                  &lt;stop offset="0%" stop-color="#00F8E7"/&gt;
                  &lt;stop offset="100%" stop-color="#00A3FF"/&gt;
                &lt;/linearGradient&gt;
              &lt;/defs&gt;
              &lt;path d="M22 18 C22 14, 26 10, 31 10 L48 10 C62 10, 72 20, 72 34 C72 45, 64 54, 53 57 L53 82 C53 86, 49 90, 44 90 C39 90, 35 86, 35 82 L35 27 C35 22, 31 18, 22 18 Z" fill="url(#finStemBlade)"/&gt;
              &lt;path d="M38 34 L76 34 C82 34, 87 39, 87 45 C87 51, 82 56, 76 56 L46 56" stroke="url(#finCyanBlade)" stroke-width="12" stroke-linecap="round"/&gt;
            &lt;/svg&gt;
          &lt;/div&gt;
          &lt;div class="min-w-0"&gt;
            &lt;p class="font-heading text-xs font-bold text-slate-900 truncate"&gt;Control Comercial 360°&lt;/p&gt;
            &lt;p class="text-[10px] text-slate-500 truncate"&gt;Ciclo activo • Trazabilidad de catálogo y cuotas&lt;/p&gt;
          &lt;/div&gt;
        &lt;/div&gt;
        &lt;span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-[#0062FF] font-mono text-[10px] font-bold shrink-0 border border-blue-200/60"&gt;
          BCB: 6.96 Bs
        &lt;/span&gt;
      &lt;/div&gt;

      &lt;div class="grid grid-cols-3 gap-2"&gt;
        &lt;div class="rounded-lg bg-white border border-slate-200/70 p-2.5 shadow-2xs"&gt;
          &lt;p class="text-[9px] font-semibold uppercase tracking-wider text-slate-500"&gt;Ventas ciclo&lt;/p&gt;
          &lt;p class="mt-1 font-heading text-xs sm:text-sm font-extrabold text-slate-900"&gt;Bs 8,450.00&lt;/p&gt;
          &lt;p class="mt-0.5 font-mono text-[9px] font-semibold text-[#0062FF]"&gt;+18.4% mes&lt;/p&gt;
        &lt;/div&gt;
        &lt;div class="rounded-lg bg-white border border-[#0062FF]/25 p-2.5 shadow-2xs"&gt;
          &lt;p class="text-[9px] font-semibold uppercase tracking-wider text-slate-500"&gt;Por cobrar&lt;/p&gt;
          &lt;p class="mt-1 font-heading text-xs sm:text-sm font-extrabold text-[#0062FF]"&gt;Bs 2,140.00&lt;/p&gt;
          &lt;p class="mt-0.5 text-[9px] text-slate-500"&gt;4 créditos activos&lt;/p&gt;
        &lt;/div&gt;
        &lt;div class="rounded-lg bg-white border border-slate-200/70 p-2.5 shadow-2xs"&gt;
          &lt;p class="text-[9px] font-semibold uppercase tracking-wider text-slate-500"&gt;Cobranzas&lt;/p&gt;
          &lt;p class="mt-1 font-heading text-xs sm:text-sm font-extrabold text-slate-900"&gt;Bs 6,310.00&lt;/p&gt;
          &lt;p class="mt-0.5 text-[9px] text-emerald-600 font-medium"&gt;92% puntual&lt;/p&gt;
        &lt;/div&gt;
      &lt;/div&gt;

      &lt;div class="rounded-lg bg-white border border-slate-200/70 p-3 space-y-2.5 shadow-2xs"&gt;
        &lt;div class="flex items-center justify-between"&gt;
          &lt;span class="text-[11px] font-bold text-slate-900"&gt;Créditos y pedidos recientes&lt;/span&gt;
          &lt;span class="font-mono text-[10px] font-semibold text-[#0062FF]"&gt;Kardex &amp; Cuotas&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="space-y-2"&gt;
          &lt;div class="p-2 rounded-lg bg-slate-50/80 border border-slate-200/60"&gt;
            &lt;div class="flex items-center justify-between text-xs"&gt;
              &lt;span class="font-semibold text-slate-800 truncate pr-1"&gt;Carla Ríos • Kaiak Urbe&lt;/span&gt;
              &lt;span class="font-mono text-[10px] font-bold text-[#0062FF] shrink-0"&gt;Cuota 2/3 (67%)&lt;/span&gt;
            &lt;/div&gt;
            &lt;div class="mt-1.5 h-1.5 w-full rounded-full bg-slate-200 overflow-hidden"&gt;
              &lt;div class="h-full w-2/3 rounded-full bg-gradient-to-r from-[#0062FF] to-[#00D2DF]"&gt;&lt;/div&gt;
            &lt;/div&gt;
          &lt;/div&gt;

          &lt;div class="flex items-center justify-between gap-2 px-1 pt-0.5"&gt;
            &lt;div class="flex items-center gap-2 min-w-0"&gt;
              &lt;div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100"&gt;
                ✓
              &lt;/div&gt;
              &lt;div class="min-w-0"&gt;
                &lt;p class="text-[11px] font-semibold text-slate-800 truncate"&gt;Recibo #PAG-184 • Abono en cuota&lt;/p&gt;
                &lt;p class="text-[9px] text-slate-500 truncate"&gt;QR / Banco • Bruno Mendoza (Venta #042)&lt;/p&gt;
              &lt;/div&gt;
            &lt;/div&gt;
            &lt;span class="font-mono text-[10px] font-bold text-emerald-600 shrink-0"&gt;+Bs 210.00&lt;/span&gt;
          &lt;/div&gt;

          &lt;div class="flex items-center justify-between gap-2 px-1 pt-0.5 border-t border-slate-100"&gt;
            &lt;div class="flex items-center gap-2 min-w-0"&gt;
              &lt;div class="w-6 h-6 rounded-md bg-blue-50 text-[#0062FF] flex items-center justify-center shrink-0 border border-blue-100"&gt;
                📦
              &lt;/div&gt;
              &lt;div class="min-w-0"&gt;
                &lt;p class="text-[11px] font-semibold text-slate-800 truncate"&gt;Pedido #PED-305 • Reserva confirmada&lt;/p&gt;
                &lt;p class="text-[9px] text-slate-500 truncate"&gt;Ana López • 2 productos (Cuidado facial)&lt;/p&gt;
              &lt;/div&gt;
            &lt;/div&gt;
            &lt;span class="font-mono text-[10px] font-bold text-slate-700 shrink-0"&gt;Bs 380.00&lt;/span&gt;
          &lt;/div&gt;
        &lt;/div&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </section>

    </main>

    <script>
        function toggleCleanMode() {
            const controls = document.getElementById('controlsBar');
            const notice = document.getElementById('noticeBanner');
            const code = document.getElementById('codeSection');
            const exitBtn = document.getElementById('exitCleanBtn');
            const stage = document.getElementById('captureStage');

            const isClean = controls.classList.contains('hidden');

            if (isClean) {
                controls.classList.remove('hidden');
                notice.classList.remove('hidden');
                code.classList.remove('hidden');
                exitBtn.classList.add('hidden');
                stage.classList.remove('min-h-screen', 'rounded-none', 'border-0');
            } else {
                controls.classList.add('hidden');
                notice.classList.add('hidden');
                code.classList.add('hidden');
                exitBtn.classList.remove('hidden');
                stage.classList.add('min-h-screen', 'rounded-none', 'border-0');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function copySnippet(elementId, btn) {
            const text = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<span class="material-symbols-outlined text-[14px]">check</span> ¡Copiado!';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 2000);
            });
        }
    </script>
</body>
</html>
