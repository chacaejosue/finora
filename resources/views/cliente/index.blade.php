<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Mi Cuenta | Portal de Cliente - Finora</title>

    <!-- Favicon de Finora -->
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>

    <!-- Tipografías de Google: Outfit & Manrope -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>

    <!-- Iconos Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <a class="inline-flex items-center gap-3.5 group" href="{{ route('landing') }}" title="Finora - Inicio">
                <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                </div>
                <div class="flex flex-col">
                    <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Portal de Cliente</span>
                    <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Consultoría Natura &bull; Sistema Finora</span>
                </div>
            </a>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-bold text-finora-navy">
                        {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
                    </span>
                    <span class="text-[11px] font-medium text-finora-subtle">
                        {{ $usuario->persona->email }}
                    </span>
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 border border-cyan-200 text-cyan-800 text-xs font-bold tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                    {{ $usuario->rol }}
                </span>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-red-600 hover:bg-red-50 border border-slate-200 hover:border-red-200 transition-colors cursor-pointer" title="Cerrar sesión">
                        <span class="material-symbols-outlined text-base">logout</span>
                        <span class="hidden sm:inline">Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">

        <!-- Banner de Bienvenida -->
        <section aria-labelledby="welcome-title" class="bg-gradient-to-r from-finora-navy via-finora-dark to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
            <div aria-hidden="true" class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
            <div aria-hidden="true" class="absolute right-32 -bottom-20 w-72 h-72 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-cyan-300 text-xs font-bold tracking-wide uppercase mb-4">
                    <span class="material-symbols-outlined text-sm">person</span>
                    Portal de Cliente en Desarrollo
                </div>

                <h1 id="welcome-title" class="font-heading text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    ¡Bienvenido, {{ $usuario->persona->nombre }}!
                </h1>

                <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed font-normal">
                    Gracias por confiar en nuestra consultoría de belleza. Este es tu panel de cliente donde podrás consultar tus pedidos registrados y estado de cuenta.
                </p>
            </div>
        </section>

        <section aria-label="Resumen de cuenta" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wide text-purple-700">Pedidos recientes</span>
                <strong class="block mt-2 font-heading text-3xl text-finora-navy">{{ $pedidos->count() }}</strong>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wide text-emerald-700">Compras registradas</span>
                <strong class="block mt-2 font-heading text-3xl text-finora-navy">{{ $ventas->count() }}</strong>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <span class="text-xs font-bold uppercase tracking-wide text-amber-700">Saldo pendiente</span>
                <strong class="block mt-2 font-heading text-3xl text-finora-navy">@money($ventas->sum(fn ($venta) => $venta->saldo_pendiente))</strong>
            </div>
        </section>

        <section aria-labelledby="orders-title" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h2 id="orders-title" class="font-heading text-xl font-extrabold text-finora-navy">Mis pedidos</h2>
                    <p class="text-xs text-finora-subtle mt-1">Consulta el estado de tus solicitudes recientes.</p>
                </div>
                <a href="{{ route('categorias') }}" class="text-xs font-bold text-finora-blue hover:underline">Ver catálogo</a>
            </div>

            @if ($pedidos->isEmpty())
                <p class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-5 text-sm text-finora-subtle">Aún no tienes pedidos registrados.</p>
            @else
                <div class="space-y-3">
                    @foreach ($pedidos as $pedido)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-100 px-4 py-4">
                            <div>
                                <p class="text-sm font-bold text-finora-navy">Pedido #{{ $pedido->id }}</p>
                                <p class="text-xs text-finora-subtle mt-1">{{ $pedido->fecha->format('d/m/Y H:i') }} · {{ $pedido->detalles->count() }} producto(s)</p>
                                @if ($pedido->fecha_entrega)
                                    <p class="text-xs text-emerald-700 mt-1 font-semibold">Entregado el {{ $pedido->fecha_entrega->format('d/m/Y H:i') }} a {{ $pedido->recibido_por }}</p>
                                @endif
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="inline-flex rounded-full bg-purple-50 border border-purple-100 px-2.5 py-1 text-[11px] font-bold text-purple-700">{{ str_replace('_', ' ', $pedido->estado) }}</span>
                                <p class="text-sm font-bold text-finora-navy mt-1">@money($pedido->total)</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section aria-labelledby="account-title" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <div class="mb-5">
                <h2 id="account-title" class="font-heading text-xl font-extrabold text-finora-navy">Estado de cuenta</h2>
                <p class="text-xs text-finora-subtle mt-1">Historial de tus compras y saldo pendiente.</p>
            </div>

            @if ($ventas->isEmpty())
                <p class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-5 text-sm text-finora-subtle">Aún no tienes compras registradas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-slate-100 text-[11px] uppercase tracking-wide text-finora-subtle">
                            <tr>
                                <th class="px-3 py-3 font-bold">Venta</th>
                                <th class="px-3 py-3 font-bold">Fecha</th>
                                <th class="px-3 py-3 font-bold">Estado</th>
                                <th class="px-3 py-3 font-bold text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($ventas as $venta)
                                <tr>
                                    <td class="px-3 py-3 font-bold text-finora-navy">#{{ $venta->id }}</td>
                                    <td class="px-3 py-3 text-finora-subtle">{{ $venta->fecha->format('d/m/Y') }}</td>
                                    <td class="px-3 py-3 text-finora-subtle">{{ str_replace('_', ' ', $venta->estado) }}</td>
                                    <td class="px-3 py-3 text-right font-bold text-finora-navy">@money($venta->saldo_pendiente)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-finora-subtle">
            <div class="flex items-center gap-2">
                <span class="font-bold text-finora-navy">Finora</span>
                <span>&mdash; Plataforma comercial y financiera</span>
            </div>
            <div>
                &copy; {{ date('Y') }} Finora. Todos los derechos reservados.
            </div>
        </div>
    </footer>

</body>
</html>
