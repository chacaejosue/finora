<!-- Pie de página principal de Finora — Rediseño Moderno Luxury -->
<footer class="relative z-10 w-full bg-slate-50 dark:bg-slate-950/80 border-t border-slate-200/70 dark:border-slate-800 pt-16 pb-12 mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-12 pb-12 border-b border-slate-200 dark:border-slate-800">

            <!-- Columna 1: Marca Finora -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-8 w-auto object-contain"/>
                    <span class="font-heading font-extrabold text-2xl text-slate-900 dark:text-white tracking-tight">Finora</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm leading-relaxed">
                    Plataforma comercial y catálogo exclusivo para consultores independientes. Fragancias, cuidado de la piel y bienestar con atención y asesoramiento directo.
                </p>
                <div class="flex items-center gap-2 pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Consultor en línea
                    </span>
                </div>
            </div>

            <!-- Columna 2: Enlaces rápidos -->
            <div class="flex flex-col gap-3 md:justify-self-center">
                <h3 class="font-heading font-bold text-sm text-slate-900 dark:text-white tracking-wide">Enlaces</h3>
                <ul class="flex flex-col gap-2.5 text-sm text-slate-600 dark:text-slate-400">
                    <li><a href="{{ route('landing') }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Inicio</a></li>
                    <li><a href="{{ route('categorias') }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Categorías</a></li>
                    <li><a href="{{ request()->routeIs('landing') ? '#ofertas' : route('landing') . '#ofertas' }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Ofertas</a></li>
                    <li><a href="{{ request()->routeIs('landing') ? '#contacto' : route('landing') . '#contacto' }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors">Contacto</a></li>
                </ul>
            </div>

            <!-- Columna 3: Ayuda y Políticas -->
            <div class="flex flex-col gap-3 md:justify-self-end">
                <h3 class="font-heading font-bold text-sm text-slate-900 dark:text-white tracking-wide">Ayuda</h3>
                <ul class="flex flex-col gap-2.5 text-sm text-slate-600 dark:text-slate-400">
                    <li>
                        <button type="button" onclick="window.finoraOpenPolicy?.('faq')" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors text-left cursor-pointer">
                            Preguntas frecuentes
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="window.finoraOpenPolicy?.('envios')" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors text-left cursor-pointer">
                            Envíos y devoluciones
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="window.finoraOpenPolicy?.('terminos')" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors text-left cursor-pointer">
                            Términos y condiciones
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="window.finoraOpenPolicy?.('privacidad')" class="hover:text-sky-600 dark:hover:text-sky-400 transition-colors text-left cursor-pointer">
                            Política de privacidad
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Barra inferior de copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
            <p>© {{ date('Y') }} Finora. Todos los derechos reservados.</p>
            <div class="flex items-center gap-1.5 text-sky-600 dark:text-sky-400 font-medium">
                <span>&rarr;</span>
                <span>Belleza y bienestar que inspiran</span>
            </div>
        </div>
    </div>
</footer>
