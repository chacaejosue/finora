{{--
  Finora - Maqueta Visual de Interfaz Destacada (Mockup Demo)
  Uso: Puedes incluirlo con @include('temp-finora-mockup') o copiar el bloque interior directamente.
  Basado en la pantalla de gestión comercial, ventas a crédito y cobranzas de Finora.
--}}
<div class="lg:col-span-5 relative flex flex-col items-center lg:-translate-y-6 w-full max-w-lg mx-auto overflow-hidden">
    <div class="relative w-full rounded-2xl bg-white border border-slate-200/80 p-3 sm:p-4 shadow-xl overflow-hidden">
        
        <!-- Barra de navegación tipo navegador / ventana macOS -->
        <div class="flex items-center justify-between gap-2 pb-3 px-1 border-b border-slate-100 mb-3">
            <div class="flex items-center gap-1.5 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 shrink-0"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80 shrink-0"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 shrink-0"></span>
                <span class="ml-1 sm:ml-2 font-mono text-[10px] sm:text-xs text-slate-500 truncate max-w-[110px] xs:max-w-[140px] sm:max-w-none">
                    finora.local/panel/ventas
                </span>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold px-2 sm:px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/70 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Espacio: Ventas & Cobranzas
            </span>
        </div>

        <!-- Cuerpo del panel interactivo demo -->
        <div class="overflow-hidden rounded-xl bg-slate-50/80 border border-slate-200/50 shadow-inner p-3.5 space-y-3">
            
            <!-- Encabezado de la pantalla -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <!-- Isotipo vectorial Finora -->
                    <div class="w-7 h-7 rounded-lg bg-white shadow-xs p-1 flex items-center justify-center shrink-0 border border-slate-100">
                        <svg viewBox="0 0 100 100" class="w-full h-full" fill="none">
                            <defs>
                                <linearGradient id="finStemBlade" x1="0%" y1="0%" x2="50%" y2="100%">
                                    <stop offset="0%" stop-color="#0238B8"/>
                                    <stop offset="100%" stop-color="#008FFF"/>
                                </linearGradient>
                                <linearGradient id="finCyanBlade" x1="0%" y1="100%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#00F8E7"/>
                                    <stop offset="100%" stop-color="#00A3FF"/>
                                </linearGradient>
                            </defs>
                            <path d="M22 18 C22 14, 26 10, 31 10 L48 10 C62 10, 72 20, 72 34 C72 45, 64 54, 53 57 L53 82 C53 86, 49 90, 44 90 C39 90, 35 86, 35 82 L35 27 C35 22, 31 18, 22 18 Z" fill="url(#finStemBlade)"/>
                            <path d="M38 34 L76 34 C82 34, 87 39, 87 45 C87 51, 82 56, 76 56 L46 56" stroke="url(#finCyanBlade)" stroke-width="12" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-heading text-xs font-bold text-slate-900 truncate">Control Comercial 360°</p>
                        <p class="text-[10px] text-slate-500 truncate">Ciclo activo • Trazabilidad de catálogo y cuotas</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-[#0062FF] font-mono text-[10px] font-bold shrink-0 border border-blue-200/60">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    BCB: 6.96 Bs
                </span>
            </div>

            <!-- Grilla de 3 KPIs clave de Finora -->
            <div class="grid grid-cols-3 gap-2">
                <!-- KPI 1: Ventas del Ciclo -->
                <div class="rounded-lg bg-white border border-slate-200/70 p-2.5 shadow-2xs">
                    <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-500">Ventas ciclo</p>
                    <p class="mt-1 font-heading text-xs sm:text-sm font-extrabold text-slate-900">Bs 8,450.00</p>
                    <p class="mt-0.5 font-mono text-[9px] font-semibold text-[#0062FF]">+18.4% mes</p>
                </div>
                <!-- KPI 2: Créditos por Cobrar -->
                <div class="rounded-lg bg-white border border-[#0062FF]/25 p-2.5 shadow-2xs">
                    <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-500">Por cobrar</p>
                    <p class="mt-1 font-heading text-xs sm:text-sm font-extrabold text-[#0062FF]">Bs 2,140.00</p>
                    <p class="mt-0.5 text-[9px] text-slate-500">4 créditos activos</p>
                </div>
                <!-- KPI 3: Cobranzas Realizadas -->
                <div class="rounded-lg bg-white border border-slate-200/70 p-2.5 shadow-2xs">
                    <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-500">Cobranzas</p>
                    <p class="mt-1 font-heading text-xs sm:text-sm font-extrabold text-slate-900">Bs 6,310.00</p>
                    <p class="mt-0.5 text-[9px] text-emerald-600 font-medium">92% puntual</p>
                </div>
            </div>

            <!-- Sección de Expedientes y Actividad Reciente -->
            <div class="rounded-lg bg-white border border-slate-200/70 p-3 space-y-2.5 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-900">Créditos y pedidos recientes</span>
                    <span class="font-mono text-[10px] font-semibold text-[#0062FF]">Kardex & Cuotas</span>
                </div>
                <div class="space-y-2">
                    <!-- Fila 1: Seguimiento de crédito a cuotas -->
                    <div class="p-2 rounded-lg bg-slate-50/80 border border-slate-200/60">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-slate-800 truncate pr-1">Carla Ríos • Kaiak Urbe</span>
                            <span class="font-mono text-[10px] font-bold text-[#0062FF] shrink-0">Cuota 2/3 (67%)</span>
                        </div>
                        <div class="mt-1.5 h-1.5 w-full rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full w-2/3 rounded-full bg-gradient-to-r from-[#0062FF] to-[#00D2DF]"></div>
                        </div>
                    </div>

                    <!-- Fila 2: Registro de cobranza / abono parcial -->
                    <div class="flex items-center justify-between gap-2 px-1 pt-0.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-slate-800 truncate">Recibo #PAG-184 • Abono en cuota</p>
                                <p class="text-[9px] text-slate-500 truncate">QR / Banco • Bruno Mendoza (Venta #042)</p>
                            </div>
                        </div>
                        <span class="font-mono text-[10px] font-bold text-emerald-600 shrink-0">+Bs 210.00</span>
                    </div>

                    <!-- Fila 3: Pedido con reserva de existencias confirmada -->
                    <div class="flex items-center justify-between gap-2 px-1 pt-0.5 border-t border-slate-100">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-md bg-blue-50 text-[#0062FF] flex items-center justify-center shrink-0 border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-slate-800 truncate">Pedido #PED-305 • Reserva confirmada</p>
                                <p class="text-[9px] text-slate-500 truncate">Ana López • 2 productos (Cuidado facial)</p>
                            </div>
                        </div>
                        <span class="font-mono text-[10px] font-bold text-slate-700 shrink-0">Bs 380.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

