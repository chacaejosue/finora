import React from 'react';
import { motion } from 'framer-motion';
import { TrendingUp, Check, PackageCheck } from 'lucide-react';

/**
 * Maqueta visual interactiva de Finora (Demo Mockup)
 * 
 * Representa la pantalla de Gestión Comercial, Ventas y Cobranzas
 * con datos contextualizados al sistema: catálogo de venta directa,
 * créditos en cuotas, reservas de stock y tasa oficial BCB.
 */
export default function FinoraMockupCard() {
  return (
    /* Columna derecha: Maqueta visual e interfaz destacada Finora */
    <motion.div
      className="lg:col-span-5 relative flex flex-col items-center lg:-translate-y-6 w-full max-w-lg mx-auto overflow-hidden"
      initial={{ opacity: 0, scale: 0.96 }}
      animate={{ opacity: 1, scale: 1 }}
      transition={{ duration: 0.5, delay: 0.1 }}
    >
      <div className="relative w-full rounded-2xl bg-surface-container-lowest border border-surface-container-high/70 p-3 sm:p-4 shadow-xl overflow-hidden">
        
        {/* Barra superior estilo ventana de navegador */}
        <div className="flex items-center justify-between gap-2 pb-3 px-1 border-b border-surface-container-low mb-3">
          <div className="flex items-center gap-1.5 min-w-0">
            <span className="w-2.5 h-2.5 rounded-full bg-rose-500/80 shrink-0" />
            <span className="w-2.5 h-2.5 rounded-full bg-amber-400/80 shrink-0" />
            <span className="w-2.5 h-2.5 rounded-full bg-emerald-500/80 shrink-0" />
            <span className="ml-1 sm:ml-2 font-mono text-[10px] sm:text-xs text-outline truncate max-w-[110px] xs:max-w-[140px] sm:max-w-none">
              finora.local/panel/ventas
            </span>
          </div>
          <span className="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold px-2 sm:px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60 shrink-0">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" /> Espacio: Ventas & Cobranzas
          </span>
        </div>

        {/* Contenedor del panel interactivo */}
        <div className="overflow-hidden rounded-xl bg-surface-container-low border border-surface-container-high/30 shadow-inner p-3.5 space-y-3">
          
          {/* Cabecera del espacio de trabajo */}
          <div className="flex items-center justify-between gap-2">
            <div className="flex items-center gap-2 min-w-0">
              {/* Isotipo vectorial oficial de Finora */}
              <div className="w-7 h-7 rounded-lg bg-white shadow-xs p-1 flex items-center justify-center shrink-0 border border-slate-100">
                <svg viewBox="0 0 100 100" className="w-full h-full" fill="none">
                  <defs>
                    <linearGradient id="finStem" x1="0%" y1="0%" x2="50%" y2="100%">
                      <stop offset="0%" stopColor="#0238B8" />
                      <stop offset="100%" stopColor="#008FFF" />
                    </linearGradient>
                    <linearGradient id="finCyan" x1="0%" y1="100%" x2="100%" y2="0%">
                      <stop offset="0%" stopColor="#00F8E7" />
                      <stop offset="100%" stopColor="#00A3FF" />
                    </linearGradient>
                  </defs>
                  <path
                    d="M22 18 C22 14, 26 10, 31 10 L48 10 C62 10, 72 20, 72 34 C72 45, 64 54, 53 57 L53 82 C53 86, 49 90, 44 90 C39 90, 35 86, 35 82 L35 27 C35 22, 31 18, 22 18 Z"
                    fill="url(#finStem)"
                  />
                  <path
                    d="M38 34 L76 34 C82 34, 87 39, 87 45 C87 51, 82 56, 76 56 L46 56"
                    stroke="url(#finCyan)"
                    strokeWidth="12"
                    strokeLinecap="round"
                  />
                </svg>
              </div>
              <div className="min-w-0">
                <p className="font-heading text-xs font-bold text-on-surface truncate">Control Comercial 360°</p>
                <p className="text-[10px] text-on-surface-variant truncate">Ciclo activo • Trazabilidad de catálogo y cuotas</p>
              </div>
            </div>
            {/* Integración oficial al BCB */}
            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#0062FF]/10 text-[#0062FF] font-mono text-[10px] font-bold shrink-0 border border-[#0062FF]/15">
              <TrendingUp className="w-3 h-3" /> BCB: 6.96 Bs
            </span>
          </div>

          {/* Grilla de 3 KPIs comerciales */}
          <div className="grid grid-cols-3 gap-2">
            <div className="rounded-lg bg-surface-container-lowest border border-surface-container-high/40 p-2.5">
              <p className="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant">Ventas ciclo</p>
              <p className="mt-1 font-heading text-xs sm:text-sm font-extrabold text-on-surface">Bs 8,450.00</p>
              <p className="mt-0.5 font-mono text-[9px] font-semibold text-[#0062FF]">+18.4% mes</p>
            </div>
            <div className="rounded-lg bg-surface-container-lowest border border-[#0062FF]/25 p-2.5">
              <p className="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant">Por cobrar</p>
              <p className="mt-1 font-heading text-xs sm:text-sm font-extrabold text-[#0062FF]">Bs 2,140.00</p>
              <p className="mt-0.5 text-[9px] text-on-surface-variant">4 créditos activos</p>
            </div>
            <div className="rounded-lg bg-surface-container-lowest border border-surface-container-high/40 p-2.5">
              <p className="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant">Cobranzas</p>
              <p className="mt-1 font-heading text-xs sm:text-sm font-extrabold text-on-surface">Bs 6,310.00</p>
              <p className="mt-0.5 text-[9px] text-emerald-600 dark:text-emerald-400 font-medium">92% puntual</p>
            </div>
          </div>

          {/* Expedientes y actividad operativa reciente */}
          <div className="rounded-lg bg-surface-container-lowest border border-surface-container-high/40 p-3 space-y-2.5">
            <div className="flex items-center justify-between">
              <span className="text-[11px] font-bold text-on-surface">Créditos y pedidos recientes</span>
              <span className="font-mono text-[10px] font-semibold text-[#0062FF]">Kardex & Cuotas</span>
            </div>
            <div className="space-y-2">
              
              {/* Item 1: Venta a crédito con barra de amortización */}
              <div className="p-2 rounded-lg bg-surface-container-low/70 border border-surface-container-high/30">
                <div className="flex items-center justify-between text-xs">
                  <span className="font-semibold text-on-surface truncate pr-1">Carla Ríos • Kaiak Urbe</span>
                  <span className="font-mono text-[10px] font-bold text-[#0062FF] shrink-0">Cuota 2/3 (67%)</span>
                </div>
                <div className="mt-1.5 h-1.5 w-full rounded-full bg-surface-container-high overflow-hidden">
                  <div className="h-full w-2/3 rounded-full bg-gradient-to-r from-[#0062FF] to-[#00D2DF]" />
                </div>
              </div>

              {/* Item 2: Cobranza / abono registrado */}
              <div className="flex items-center justify-between gap-2 px-1 pt-0.5">
                <div className="flex items-center gap-2 min-w-0">
                  <div className="w-6 h-6 rounded-md bg-emerald-500/15 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-500/20">
                    <Check className="w-3.5 h-3.5" />
                  </div>
                  <div className="min-w-0">
                    <p className="text-[11px] font-semibold text-on-surface truncate">Recibo #PAG-184 • Abono en cuota</p>
                    <p className="text-[9px] text-on-surface-variant truncate">QR / Banco • Bruno Mendoza (Venta #042)</p>
                  </div>
                </div>
                <span className="font-mono text-[10px] font-bold text-emerald-600 dark:text-emerald-400 shrink-0">+Bs 210.00</span>
              </div>

              {/* Item 3: Pedido reservado para entrega */}
              <div className="flex items-center justify-between gap-2 px-1 pt-0.5 border-t border-surface-container-high/20">
                <div className="flex items-center gap-2 min-w-0">
                  <div className="w-6 h-6 rounded-md bg-[#0062FF]/15 text-[#0062FF] flex items-center justify-center shrink-0 border border-[#0062FF]/20">
                    <PackageCheck className="w-3.5 h-3.5" />
                  </div>
                  <div className="min-w-0">
                    <p className="text-[11px] font-semibold text-on-surface truncate">Pedido #PED-305 • Reserva confirmada</p>
                    <p className="text-[9px] text-on-surface-variant truncate">Ana López • 2 productos (Cuidado facial)</p>
                  </div>
                </div>
                <span className="font-mono text-[10px] font-bold text-on-surface shrink-0">Bs 380.00</span>
              </div>

            </div>
          </div>

        </div>
      </div>
    </motion.div>
  );
}

