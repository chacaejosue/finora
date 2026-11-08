<!-- Pie de página principal de Finora — Ultra-compacto y limpio en Móvil, Completo en Desktop -->
<footer class="bg-white border-t border-sky-100/70 pt-6 sm:pt-12 pb-20 sm:pb-8 text-sm mt-6 sm:mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Top Columns Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-5 sm:gap-8 lg:gap-10 pb-6 sm:pb-10 border-b border-slate-100">
            <!-- Col 1: Marca e Identidad (4 cols) -->
            <div class="lg:col-span-4 flex flex-col justify-between">
                <div>
                    <div class="flex flex-wrap items-center justify-between sm:justify-start gap-2 mb-2 sm:mb-3">
                        <a aria-label="Catálogo Digital Inicio" class="flex items-center gap-2" href="{{ route('landing') }}">
                            <img src="{{ asset('images/branding/finora-isotipo.png') }}" class="w-7 h-7 sm:w-8 sm:h-8 object-contain" alt="Catálogo Digital">
                            <div class="flex flex-col">
                                <span class="text-lg sm:text-xl font-bold tracking-tight text-[#0e1b2e] leading-tight">Catálogo Digital</span>
                                <span class="text-[9px] sm:text-[10px] font-semibold text-finora-cyan -mt-0.5">Consultora Independiente Natura</span>
                            </div>
                        </a>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-cyan-50 border border-cyan-100 text-[10px] sm:text-[11px] font-semibold text-cyan-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Autorizada</span>
                        </div>
                    </div>
                    <p class="hidden sm:block text-xs sm:text-sm text-slate-500 leading-relaxed max-w-sm mb-3">
                        Catálogo interactivo de productos 100% originales Natura. Asesoramiento de belleza, cuidado personal y entregas coordinadas directamente por tu consultora independiente.
                    </p>
                </div>
            </div>

            <!-- Col 2 y 3: Enlaces y Ayuda (En móvil se muestran en 2 columnas lado a lado super compactas) -->
            <div class="sm:col-span-2 lg:col-span-5 grid grid-cols-2 gap-4 sm:gap-8">
                <!-- Enlaces Rápidos -->
                <div>
                    <div class="h-6 sm:h-8 flex items-center mb-1.5 sm:mb-2">
                        <h3 class="font-bold text-[#0e1b2e] text-[11px] sm:text-xs uppercase tracking-wider">Enlaces</h3>
                    </div>
                    <ul class="space-y-1.5 text-xs text-slate-500">
                        <li><a class="hover:text-finora-cyan transition-colors" href="{{ request()->routeIs('landing') ? '#inicio' : route('landing') }}">Inicio</a></li>
                        <li><a class="hover:text-finora-cyan transition-colors" href="{{ route('categorias') }}">Categorías</a></li>
                        <li><a class="hover:text-finora-cyan transition-colors" href="{{ request()->routeIs('landing') ? '#ofertas' : route('ofertas') }}">Ofertas de ciclo</a></li>
                        <li><a class="hover:text-finora-cyan transition-colors" href="{{ request()->routeIs('landing') ? '#contacto' : route('landing') . '#contacto' }}">Contacto y Revista</a></li>
                    </ul>
                </div>

                <!-- Ayuda y Soporte -->
                <div>
                    <div class="h-6 sm:h-8 flex items-center mb-1.5 sm:mb-2">
                        <h3 class="font-bold text-[#0e1b2e] text-[11px] sm:text-xs uppercase tracking-wider">Ayuda</h3>
                    </div>
                    <ul class="space-y-1.5 text-xs text-slate-500">
                        <li><button type="button" onclick="window.finoraOpenPolicy?.('faq')" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Preguntas frecuentes</button></li>
                        <li><button type="button" onclick="window.finoraOpenPolicy?.('envios')" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Envíos y entregas</button></li>
                        <li><button type="button" onclick="window.finoraOpenPolicy?.('terminos')" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Términos</button></li>
                        <li><button type="button" onclick="window.finoraOpenPolicy?.('privacidad')" class="hover:text-finora-cyan transition-colors cursor-pointer text-left">Privacidad</button></li>
                    </ul>
                </div>
            </div>

            <!-- Col 4: Atención y Pedidos Directos (3 cols) -->
            <div class="lg:col-span-3">
                <div class="h-6 sm:h-8 flex items-center mb-1.5 sm:mb-2">
                    <h3 class="font-bold text-[#0e1b2e] text-[11px] sm:text-xs uppercase tracking-wider">Atención</h3>
                </div>
                <div class="space-y-2 text-xs text-slate-500">
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', config('services.whatsapp.phone')) }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/80 font-semibold transition-all">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Consultar por WhatsApp</span>
                    </a>
                    <div class="flex items-center gap-2 text-slate-500 text-[11px]">
                        <span>Coordinación en Bolivia • Lun - Sáb</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transparencia comercial y atribución de marca (Ultra sintético de 1 línea) -->
        <div class="mt-4 pt-3 border-t border-slate-100 text-[10px] text-slate-400 leading-normal flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <p>
                <strong class="text-slate-500">Aviso de transparencia comercial:</strong> Catálogo independiente gestionado por Consultora Natura en Bolivia. Marcas propiedad de Natura Cosméticos S.A.
            </p>
            <div class="flex items-center gap-2 shrink-0">
                <span>© {{ date('Y') }} Catálogo Digital</span>
                <span>•</span>
                <a href="{{ route('login') }}" class="font-bold text-[#0e1b2e] hover:text-finora-cyan transition-colors" title="Acceso al Sistema">
                    Finora • Gestión Comercial
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Modales de Políticas Reutilizables -->
<dialog id="policyModalFaq" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
    <div class="flex items-center justify-between border-b pb-3 mb-4">
        <h3 class="text-base font-bold text-slate-800">Preguntas Frecuentes</h3>
        <button type="button" onclick="document.getElementById('policyModalFaq').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
    </div>
    <div class="space-y-4 text-xs sm:text-sm text-slate-600 max-h-[60vh] overflow-y-auto pr-1">
        <div>
            <h4 class="font-bold text-slate-800 mb-1">¿Los productos son 100% originales?</h4>
            <p>Sí, todos los productos son despachados directamente del inventario oficial de Natura Cosméticos por tu consultora autorizada.</p>
        </div>
        <div>
            <h4 class="font-bold text-slate-800 mb-1">¿Cómo realizo el pago?</h4>
            <p>El pago se coordina directamente por WhatsApp mediante transferencia bancaria, QR o efectivo contra entrega.</p>
        </div>
        <div>
            <h4 class="font-bold text-slate-800 mb-1">¿Hacen envíos a todo el país?</h4>
            <p>Sí, coordinamos entregas locales y envíos a diferentes departamentos de Bolivia.</p>
        </div>
    </div>
</dialog>

<dialog id="policyModalEnvios" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
    <div class="flex items-center justify-between border-b pb-3 mb-4">
        <h3 class="text-base font-bold text-slate-800">Envíos y Entregas</h3>
        <button type="button" onclick="document.getElementById('policyModalEnvios').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
    </div>
    <div class="space-y-3 text-xs sm:text-sm text-slate-600 max-h-[60vh] overflow-y-auto pr-1">
        <p>• <strong>Entregas locales:</strong> Se coordinan en puntos de encuentro céntricos o a domicilio en un plazo de 24 a 48 horas tras confirmar el pedido.</p>
        <p>• <strong>Envíos nacionales:</strong> Se realizan por empresas de encomienda o transporte terrestre debidamente embalados.</p>
    </div>
</dialog>

<dialog id="policyModalTerminos" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
    <div class="flex items-center justify-between border-b pb-3 mb-4">
        <h3 class="text-base font-bold text-slate-800">Términos y Condiciones</h3>
        <button type="button" onclick="document.getElementById('policyModalTerminos').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
    </div>
    <div class="space-y-3 text-xs sm:text-sm text-slate-600 max-h-[60vh] overflow-y-auto pr-1">
        <p>Este catálogo web actúa como canal informativo y facilitador de pedidos para consultoras independientes. Los precios y ofertas mostrados corresponden a los ciclos comerciales vigentes.</p>
    </div>
</dialog>

<dialog id="policyModalPrivacidad" class="fixed inset-0 m-auto w-[min(92vw,36rem)] rounded-3xl bg-white shadow-2xl p-6 border border-slate-100 backdrop:bg-black/60">
    <div class="flex items-center justify-between border-b pb-3 mb-4">
        <h3 class="text-base font-bold text-slate-800">Política de Privacidad</h3>
        <button type="button" onclick="document.getElementById('policyModalPrivacidad').close()" class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
    </div>
    <div class="space-y-3 text-xs sm:text-sm text-slate-600 max-h-[60vh] overflow-y-auto pr-1">
        <p>Los datos ingresados en el formulario de pedido (nombre y teléfono) son utilizados exclusivamente para la coordinación de la entrega por WhatsApp. No compartimos tu información con terceros.</p>
    </div>
</dialog>
