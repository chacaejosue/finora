<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Panel del Colaborador</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased flex flex-col min-h-screen">
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
            <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.colaborador') }}">
                <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                <div class="flex flex-col">
                    <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                    <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Panel del Colaborador</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline text-xs font-bold text-finora-navy">
                    {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 border border-cyan-200 text-cyan-800 text-xs font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                    COLABORADOR
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-red-600 hover:bg-red-50 border border-slate-200 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-base">logout</span>
                        <span class="hidden sm:inline">Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">
        <section class="bg-gradient-to-r from-finora-navy via-finora-dark to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
            <div aria-hidden="true" class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-cyan-500/10 blur-3xl"></div>
            <div class="relative z-10 max-w-3xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-cyan-300 text-xs font-bold tracking-wide uppercase mb-4">
                    <span class="material-symbols-outlined text-sm">badge</span>
                    Espacio operativo
                </span>
                <h1 class="font-heading text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    ¡Bienvenido, {{ $usuario->persona->nombre }}!
                </h1>
                <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed">
                    Gestiona pedidos, clientes, inventario y ventas desde un solo lugar.
                </p>
            </div>
        </section>

        <section aria-label="Resumen operativo" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wide text-purple-700">Pedidos activos</span>
                <strong class="block mt-2 font-heading text-3xl text-finora-navy">{{ $pedidosPendientes }}</strong>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wide text-rose-700">Ventas en borrador</span>
                <strong class="block mt-2 font-heading text-3xl text-finora-navy">{{ $ventasPendientes }}</strong>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wide text-blue-700">Productos activos</span>
                <strong class="block mt-2 font-heading text-3xl text-finora-navy">{{ $productosActivos }}</strong>
            </div>
        </section>

        <section aria-labelledby="operations-title" class="space-y-4">
            <div>
                <h2 id="operations-title" class="font-heading text-xl sm:text-2xl font-extrabold text-finora-navy">Operaciones disponibles</h2>
                <p class="text-sm text-finora-subtle mt-1">Accede rápidamente a las tareas de tu rol.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ([
                    ['route' => 'panel.pedidos.index', 'icon' => 'shopping_cart', 'title' => 'Pedidos', 'description' => 'Registra pedidos y administra reservas de stock.', 'color' => 'purple'],
                    ['route' => 'panel.ventas.index', 'icon' => 'point_of_sale', 'title' => 'Ventas', 'description' => 'Consulta y gestiona el proceso de ventas.', 'color' => 'rose'],
                    ['route' => 'panel.clientes.index', 'icon' => 'group', 'title' => 'Clientes', 'description' => 'Consulta y actualiza la información de clientes.', 'color' => 'emerald'],
                    ['route' => 'panel.inventario.index', 'icon' => 'inventory_2', 'title' => 'Inventario', 'description' => 'Consulta existencias y registra ajustes autorizados.', 'color' => 'amber'],
                    ['route' => 'panel.compras.index', 'icon' => 'shopping_bag', 'title' => 'Compras', 'description' => 'Registra abastecimiento y recepción de productos.', 'color' => 'cyan'],
                    ['route' => 'panel.pagos.index', 'icon' => 'account_balance', 'title' => 'Pagos', 'description' => 'Registra abonos y consulta cuentas pendientes.', 'color' => 'indigo'],
                    ['route' => 'contabilidad.index', 'icon' => 'monitoring', 'title' => 'Contabilidad', 'description' => 'Libro Diario, Mayores, Balance de Apertura y Estados Financieros.', 'color' => 'blue'],
                ] as $operation)
                    <a href="{{ route($operation['route']) }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-lg hover:border-finora-blue transition-all group">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-finora-blue flex items-center justify-center shrink-0 group-hover:bg-finora-blue group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined">{{ $operation['icon'] }}</span>
                            </div>
                            <div>
                                <h3 class="font-heading text-base font-bold text-finora-navy group-hover:text-finora-blue">{{ $operation['title'] }}</h3>
                                <p class="text-xs text-finora-subtle mt-1 leading-relaxed">{{ $operation['description'] }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    </main>
</body>
</html>
