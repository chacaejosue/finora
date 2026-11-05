<!-- BEGIN: MainHeader Finora (Componente Reutilizable Unificado) -->
<header class="w-full bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-sky-100/60 transition-all" id="mainHeader">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
        <!-- Brand Logo Finora (Uso del isotipo oficial de Finora) -->
        <a aria-label="Finora Inicio" class="flex items-center gap-2.5 shrink-0 group cursor-pointer" href="{{ route('landing') }}" id="nav-inicio">
            <img src="{{ asset('images/branding/finora-isotipo.png') }}" class="w-9 h-9 object-contain group-hover:scale-105 transition-transform" alt="Finora">
            <span class="text-2xl font-bold tracking-tight text-[#0e1b2e]">Finora</span>
        </a>

        <!-- Primary Navigation Links (Desktop) -->
        <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-slate-600">
            @if(request()->routeIs('landing'))
                <span class="text-finora-cyan font-semibold relative py-1 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-finora-cyan after:rounded-full cursor-default select-none pointer-events-none" aria-current="page">Inicio</span>
            @else
                <a class="hover:text-finora-cyan transition-colors" href="{{ route('landing') }}" id="nav-inicio-link">Inicio</a>
            @endif

            @if(request()->routeIs('categorias'))
                <!-- Pestaña categorías desactivada / fija porque ya estamos aquí -->
                <span class="text-finora-cyan font-semibold relative py-1 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-finora-cyan after:rounded-full cursor-default select-none pointer-events-none" aria-current="page">Categorías</span>
            @else
                <div class="relative group cursor-pointer flex items-center gap-1 hover:text-finora-cyan transition-colors" id="nav-categorias-dropdown">
                    <a href="{{ route('categorias') }}" class="flex items-center gap-1">
                        <span>Categorías</span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-finora-cyan transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </a>
                    <div class="absolute left-0 top-full pt-2 w-56 hidden group-hover:block transition-all z-50">
                        <div class="rounded-2xl bg-white shadow-card border border-slate-100 p-2.5 flex flex-col gap-1">
                            <a href="{{ route('categorias') }}" class="px-3 py-2 rounded-xl text-xs font-bold text-finora-cyan hover:bg-cyan-50 flex items-center justify-between">
                                <span>Ver todo el catálogo</span>
                                <span>→</span>
                            </a>
                            <div class="h-px bg-slate-100 my-1"></div>
                            @php
                                $headerCategorias = $categorias ?? \App\Models\Categoria::where('activo', true)->orderBy('nombre')->get();
                            @endphp
                            @foreach($headerCategorias as $cat)
                                <a href="{{ route('categorias', ['categoria' => \Illuminate\Support\Str::slug($cat->nombre)]) }}" class="px-3 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-finora-cyan hover:bg-slate-50 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                    <span class="truncate">{{ $cat->nombre }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <a class="hover:text-finora-cyan transition-colors" href="{{ request()->routeIs('landing') ? '#ofertas' : route('landing') . '#ofertas' }}">Ofertas</a>
            <a class="hover:text-finora-cyan transition-colors" href="{{ request()->routeIs('landing') ? '#nosotros' : route('landing') . '#nosotros' }}">Nosotros</a>
            <a class="hover:text-finora-cyan transition-colors" href="https://wa.me/{{ preg_replace('/\D+/', '', config('services.whatsapp.phone')) }}" target="_blank" rel="noopener noreferrer">Contacto</a>
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

            <!-- Cart Link with Badge -->
            <a id="cartLink" href="{{ route('carrito') }}" aria-label="Ver carrito" class="relative text-slate-700 hover:text-finora-cyan transition-colors p-1.5 rounded-full hover:bg-slate-100 cursor-pointer" title="Ver carrito">
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
        <form action="{{ route('categorias') }}" method="GET" class="relative w-full flex items-center">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </span>
            <input name="buscar" value="{{ request('buscar') }}" class="w-full pl-10 pr-4 py-2 text-xs leading-normal bg-slate-50 border border-slate-200 rounded-full text-slate-700 placeholder-slate-400" placeholder="Buscar productos..." type="search">
        </form>
        <nav class="flex flex-col gap-2 text-sm font-medium text-slate-700">
            @if(request()->routeIs('landing'))
                <span class="py-2 px-3 rounded-lg text-finora-cyan font-semibold bg-cyan-50/50 cursor-default select-none pointer-events-none">Inicio</span>
            @else
                <a class="py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ route('landing') }}">Inicio</a>
            @endif

            @if(request()->routeIs('categorias'))
                <span class="py-2 px-3 rounded-lg text-finora-cyan font-semibold bg-cyan-50/50 cursor-default select-none pointer-events-none">Categorías</span>
            @else
                <a class="py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ route('categorias') }}">Categorías</a>
            @endif

            <a class="py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ request()->routeIs('landing') ? '#ofertas' : route('landing') . '#ofertas' }}">Ofertas</a>
            <a class="py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="{{ request()->routeIs('landing') ? '#nosotros' : route('landing') . '#nosotros' }}">Nosotros</a>
            <a class="py-2 px-3 rounded-lg hover:bg-slate-50 transition-colors" href="https://wa.me/{{ preg_replace('/\D+/', '', config('services.whatsapp.phone')) }}" target="_blank" rel="noopener noreferrer">Contacto</a>
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
<!-- END: MainHeader Finora -->
