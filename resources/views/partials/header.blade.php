<!-- BEGIN: MainHeader Finora (Componente Reutilizable Unificado) -->
<style>
    /* Estilos de navegación y ScrollSpy activo */
    .finora-nav-link {
        position: relative;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        transition: color 0.2s ease-in-out, font-weight 0.2s ease-in-out;
    }
    .finora-nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0%;
        height: 2px;
        background-color: #00c1de;
        border-radius: 9999px;
        transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .finora-nav-link.finora-nav-active {
        color: #00c1de !important;
        font-weight: 600 !important;
    }
    .finora-nav-link.finora-nav-active::after {
        width: 100%;
    }
    .finora-nav-link.finora-nav-active svg {
        color: #00c1de !important;
    }

    /* Enlaces del drawer móvil */
    .finora-mobile-nav-link {
        transition: all 0.2s ease-in-out;
    }
    .finora-mobile-nav-link.finora-nav-active {
        color: #00c1de !important;
        background-color: rgba(0, 193, 222, 0.08) !important;
        font-weight: 700 !important;
    }
</style>

<header class="w-full bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-sky-100/60 transition-all" id="mainHeader">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
        <!-- Brand Logo (Portal de Catálogo de Consultoría) -->
        <a aria-label="Catálogo Digital Inicio" class="flex items-center gap-2.5 shrink-0 group cursor-pointer" href="{{ request()->routeIs('landing') ? '#inicio' : route('landing') }}" id="nav-inicio" data-nav-target="inicio">
            <img src="{{ asset('images/branding/finora-isotipo.png') }}" class="w-9 h-9 object-contain group-hover:scale-105 transition-transform" alt="Catálogo Digital">
            <div class="flex flex-col">
                <span class="text-xl sm:text-2xl font-bold tracking-tight text-[#0e1b2e] leading-tight">Catálogo Digital</span>
                <span class="text-[10px] font-semibold text-finora-cyan tracking-normal -mt-0.5">Consultora Independiente Natura</span>
            </div>
        </a>

        <!-- Primary Navigation Links (Desktop) -->
        <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-slate-600">
            @if(request()->routeIs('landing'))
                <a href="#inicio" class="finora-nav-link finora-nav-active text-slate-600 hover:text-finora-cyan transition-colors" data-nav-target="inicio" id="nav-inicio-link">Inicio</a>
            @else
                <a class="finora-nav-link hover:text-finora-cyan transition-colors" href="{{ route('landing') }}" id="nav-inicio-link">Inicio</a>
            @endif

            <!-- Dropdown permanente de Categorías (Mismo ancho y estructura en todas las páginas para evitar saltos del navbar) -->
            <div class="relative group cursor-pointer flex items-center gap-1 hover:text-finora-cyan transition-colors" id="nav-categorias-dropdown">
                <a href="{{ route('categorias') }}"
                   class="finora-nav-link flex items-center gap-1 {{ request()->routeIs('categorias*') ? 'finora-nav-active font-semibold text-finora-cyan' : 'text-slate-600 hover:text-finora-cyan' }} transition-colors"
                   data-nav-target="categorias">
                    <span>Categorías</span>
                    <svg class="w-4 h-4 {{ request()->routeIs('categorias*') ? 'text-finora-cyan' : 'text-slate-400' }} group-hover:text-finora-cyan transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </a>
                <div class="absolute left-0 top-full pt-2 w-56 hidden group-hover:block transition-all z-50">
                    <div class="rounded-2xl bg-white shadow-card border border-slate-100 p-2.5 flex flex-col gap-1">
                        <a href="{{ route('categorias') }}"
                           data-category-slug="all"
                           class="nav-category-link px-3 py-2 rounded-xl text-xs font-bold text-finora-cyan hover:bg-cyan-50 flex items-center justify-between">
                            <span>Ver todo el catálogo</span>
                            <span>→</span>
                        </a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        @php
                            $headerCategorias = $categorias ?? \App\Models\Categoria::where('activo', true)->orderBy('nombre')->get();
                        @endphp
                        @foreach($headerCategorias as $cat)
                            @php $catSlug = \Illuminate\Support\Str::slug($cat->nombre); @endphp
                            <a href="{{ route('categorias', ['categoria' => $catSlug]) }}"
                               data-category-slug="{{ $catSlug }}"
                               class="nav-category-link px-3 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-finora-cyan hover:bg-slate-50 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                <span class="truncate">{{ $cat->nombre }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <a class="finora-nav-link text-slate-600 hover:text-finora-cyan transition-colors" href="{{ request()->routeIs('landing') ? '#ofertas' : route('landing') . '#ofertas' }}" data-nav-target="ofertas">Ofertas</a>
            <a class="finora-nav-link text-slate-600 hover:text-finora-cyan transition-colors" href="{{ request()->routeIs('landing') ? '#contacto' : route('landing') . '#contacto' }}" data-nav-target="contacto">Contacto</a>
        </nav>

        <!-- Search and Header User Actions -->
        <div class="flex items-center gap-3 sm:gap-5">
            <!-- Search Bar with centered icon -->
            <form action="{{ route('categorias') }}" method="GET" class="relative hidden sm:flex items-center w-48 lg:w-64 m-0 p-0" id="headerSearchForm">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </span>
                <input name="buscar" id="headerSearchInput" value="{{ request('buscar') }}" class="w-full pl-10 pr-4 py-2 text-xs lg:text-sm leading-normal bg-slate-50 border border-slate-200/80 rounded-full focus:outline-none focus:ring-2 focus:ring-finora-cyan focus:border-transparent text-slate-700 placeholder-slate-400" placeholder="Buscar productos..." type="search">
            </form>

            <span class="theme-toggle-slot hidden" aria-hidden="true"></span>

            <!-- Account Profile Link -->
            @auth
                <a href="{{ auth()->user()->rol === 'CLIENTE' ? route('mi-cuenta') : route('panel') }}" aria-label="Perfil de usuario" class="text-slate-700 hover:text-finora-cyan transition-colors p-1.5 rounded-full hover:bg-slate-100 cursor-pointer" title="{{ auth()->user()->persona?->nombre ?? 'Mi cuenta' }}">
                    <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}" aria-label="Iniciar sesión" class="text-slate-700 hover:text-finora-cyan transition-colors p-1.5 rounded-full hover:bg-slate-100 cursor-pointer" title="Iniciar sesión">
                    <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
            @endauth

            <!-- Cart Link with Badge (Visible en desktop md+, oculto en móvil ya que el bottom bar incluye el acceso permanente a la cesta) -->
            <a id="cartLink" href="{{ route('carrito') }}" aria-label="Ver carrito" class="hidden md:inline-flex relative text-slate-700 hover:text-finora-cyan transition-colors p-1.5 rounded-full hover:bg-slate-100 cursor-pointer" title="Ver carrito">
                <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <span id="publicCartBadge" class="absolute -top-1 -right-1 min-w-5 h-5 px-1 bg-finora-cyan text-white text-[11px] font-bold rounded-full flex items-center justify-center shadow-xs">
                    0
                </span>
            </a>

            <!-- Mobile menu toggle button -->
            <button type="button" id="mobileMenuToggle" class="md:hidden p-1.5 text-slate-700 hover:text-finora-cyan focus:outline-none cursor-pointer" aria-label="Abrir menú de navegación">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="mobileMenuIcon" d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Drawer (Responsive) -->
    <div id="mobileMenuDrawer" class="hidden md:hidden border-t border-slate-100 bg-white/95 backdrop-blur-md px-4 py-4 space-y-3 shadow-lg">
        <form action="{{ route('categorias') }}" method="GET" class="relative w-full flex items-center" id="mobileHeaderSearchForm">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </span>
            <input name="buscar" id="mobileHeaderSearchInput" value="{{ request('buscar') }}" class="w-full pl-10 pr-4 py-2 text-xs leading-normal bg-slate-50 border border-slate-200 rounded-full text-slate-700 placeholder-slate-400" placeholder="Buscar productos..." type="search">
        </form>
        <nav class="flex flex-col gap-2 text-sm font-medium text-slate-700">
            @if(request()->routeIs('landing'))
                <a class="finora-mobile-nav-link py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors finora-nav-active" href="#inicio" data-nav-target="inicio">Inicio</a>
            @else
                <a class="finora-mobile-nav-link py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ route('landing') }}">Inicio</a>
            @endif

            @if(request()->routeIs('categorias'))
                <a class="finora-mobile-nav-link py-2 px-3 rounded-lg font-semibold text-finora-cyan bg-cyan-50/50" href="{{ route('categorias') }}" aria-current="page">Categorías</a>
            @else
                <a class="finora-mobile-nav-link py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ request()->routeIs('landing') ? '#categorias' : route('categorias') }}" data-nav-target="categorias">Categorías</a>
            @endif

            <a class="finora-mobile-nav-link py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ request()->routeIs('landing') ? '#ofertas' : route('landing') . '#ofertas' }}" data-nav-target="ofertas">Ofertas</a>
            <a class="finora-mobile-nav-link py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ request()->routeIs('landing') ? '#contacto' : route('landing') . '#contacto' }}" data-nav-target="contacto">Contacto</a>
            <div class="border-t border-slate-100 pt-2 mt-1">
                @auth
                    <a href="{{ auth()->user()->rol === 'CLIENTE' ? route('mi-cuenta') : route('panel') }}" class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-full bg-finora-cyan text-white text-xs font-bold shadow-sm">
                        Mi Cuenta / Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-full bg-[#0e1b2e] text-white text-xs font-bold shadow-sm">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </nav>
    </div>
</header>

<!-- Mobile Floating Bottom Bar (Acceso ergonómico táctil para teléfonos) -->
<nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2 flex items-center justify-around shadow-lg" aria-label="Navegación móvil rápida">
    <a href="{{ request()->routeIs('landing') ? '#inicio' : route('landing') }}" class="finora-bottom-nav-link flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('landing') ? 'text-finora-cyan font-bold' : 'text-slate-500 hover:text-slate-800' }} transition-colors" data-nav-target="inicio">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        <span>Inicio</span>
    </a>
    <a href="{{ route('categorias') }}" class="finora-bottom-nav-link flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('categorias*') ? 'text-finora-cyan font-bold' : 'text-slate-500 hover:text-slate-800' }} transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
        <span>Catálogo</span>
    </a>
    <a href="{{ route('carrito') }}" class="relative flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('carrito*') ? 'text-finora-cyan font-bold' : 'text-slate-500 hover:text-slate-800' }} transition-colors">
        <div class="relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
            <span id="publicCartBadge" class="absolute -top-1.5 -right-2.5 min-w-4 h-4 px-1 bg-finora-cyan text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>
        </div>
        <span>Mi Pedido</span>
    </a>
    <a href="https://wa.me/{{ preg_replace('/\D+/', '', config('services.whatsapp.phone')) }}" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center gap-1 text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/></svg>
        <span>WhatsApp</span>
    </a>
</nav>

<script>
    (function() {
        function initFinoraHeader() {
            // Toggle menú móvil
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMenuDrawer = document.getElementById('mobileMenuDrawer');

            if (mobileMenuToggle && mobileMenuDrawer) {
                mobileMenuToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    mobileMenuDrawer.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!mobileMenuDrawer.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                        mobileMenuDrawer.classList.add('hidden');
                    }
                });
            }

            // Gestión de enlaces activos (Desktop, Drawer y Bottom bar)
            const desktopNavLinks = document.querySelectorAll('.finora-nav-link[data-nav-target]');
            const mobileNavLinks = document.querySelectorAll('.finora-mobile-nav-link[data-nav-target]');
            const bottomNavInicio = document.querySelector('.finora-bottom-nav-link[data-nav-target="inicio"]');

            function setActiveNav(targetId) {
                desktopNavLinks.forEach(link => {
                    if (link.getAttribute('data-nav-target') === targetId) {
                        link.classList.add('finora-nav-active');
                    } else {
                        link.classList.remove('finora-nav-active');
                    }
                });

                mobileNavLinks.forEach(link => {
                    if (link.getAttribute('data-nav-target') === targetId) {
                        link.classList.add('finora-nav-active');
                    } else {
                        link.classList.remove('finora-nav-active');
                    }
                });

                if (bottomNavInicio) {
                    if (targetId === 'inicio') {
                        bottomNavInicio.classList.add('text-finora-cyan', 'font-bold');
                        bottomNavInicio.classList.remove('text-slate-500');
                    } else {
                        bottomNavInicio.classList.remove('text-finora-cyan', 'font-bold');
                        bottomNavInicio.classList.add('text-slate-500');
                    }
                }
            }

            // Click directo en cualquier enlace con data-nav-target
            document.querySelectorAll('[data-nav-target]').forEach(link => {
                link.addEventListener('click', () => {
                    const target = link.getAttribute('data-nav-target');
                    if (target) {
                        setActiveNav(target);
                    }
                    if (mobileMenuDrawer && !mobileMenuDrawer.classList.contains('hidden')) {
                        mobileMenuDrawer.classList.add('hidden');
                    }
                });
            });

            // ScrollSpy solo en la página principal (landing)
            @if(request()->routeIs('landing'))
            const sectionIds = ['inicio', 'categorias', 'ofertas', 'contacto'];
            let ticking = false;

            function onScroll() {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        const scrollPos = window.scrollY + 160;
                        let current = 'inicio';

                        for (let i = 0; i < sectionIds.length; i++) {
                            const sec = document.getElementById(sectionIds[i]);
                            if (sec) {
                                const top = sec.offsetTop;
                                if (scrollPos >= top) {
                                    current = sectionIds[i];
                                }
                            }
                        }

                        setActiveNav(current);
                        ticking = false;
                    });
                    ticking = true;
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
            @endif
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFinoraHeader);
        } else {
            initFinoraHeader();
        }
    })();
</script>
<!-- END: MainHeader Finora -->