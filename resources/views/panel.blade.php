<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Panel Principal | Sistema de Gestión Comercial y Financiera</title>

    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel') }}" title="Finora - Panel Principal">
                <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                </div>
                <div class="flex flex-col">
                    <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                    <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Panel Administrativo</span>
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

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-finora-blue text-xs font-bold tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-finora-blue"></span>
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

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

        <section aria-labelledby="welcome-title" class="admin-welcome-card bg-gradient-to-r from-finora-navy via-finora-dark to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden mb-10 animate-fade-in-down">
            <div aria-hidden="true" class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
            <div aria-hidden="true" class="absolute right-32 -bottom-20 w-72 h-72 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-cyan-300 text-xs font-bold tracking-wide uppercase mb-4">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sesión Activa &bull; <span id="currentDateText">Panel Comercial</span>
                    </div>

                    <h1 id="welcome-title" class="font-heading text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        ¡{{ $usuario->persona->saludo }}, {{ $usuario->persona->nombre }}!
                    </h1>

                    <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed font-normal">
                        Bienvenido a Finora. Gestiona el flujo comercial completo de tu negocio: catálogo, clientes, pedidos, compras, inventario, ventas y cobranzas.
                    </p>
                </div>

                {{-- Accesos rápidos de alta frecuencia para el consultor --}}
                <div class="flex flex-wrap lg:flex-col gap-2.5 shrink-0">
                    <a href="{{ route('panel.ventas.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-secondary to-finora-sky text-white text-xs font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-sm">point_of_sale</span>
                        <span>Nueva Venta</span>
                    </a>
                    <a href="{{ route('panel.pedidos.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white text-xs font-bold backdrop-blur-md hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-sm">shopping_cart</span>
                        <span>Nuevo Pedido</span>
                    </a>
                    <a href="{{ route('panel.productos.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white text-xs font-bold backdrop-blur-md hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-sm">add_box</span>
                        <span>Nuevo Producto</span>
                    </a>
                </div>
            </div>
        </section>

        @if ($usuario->rol === 'CONSULTOR')
            <section class="mb-10 rounded-2xl border border-blue-100 bg-white p-5 shadow-sm" aria-labelledby="currency-calculator-title" data-currency-calculator>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-finora-blue">Herramienta de apoyo</p>
                        <h2 id="currency-calculator-title" class="mt-1 text-lg font-extrabold text-finora-navy">Calculadora de divisas</h2>
                        <p class="mt-1 text-xs text-finora-subtle">Consulta una referencia actualizada del tipo de cambio oficial del BCB para comunicar precios a tus clientes.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-[8rem_9rem_9rem] sm:items-end">
                        <label class="text-xs font-bold text-finora-subtle">Moneda
                            <select id="currencyDirection" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-finora-navy">
                                <option value="USD_BOB">USD → Bs</option>
                                <option value="BOB_USD">Bs → USD</option>
                            </select>
                        </label>
                        <label class="text-xs font-bold text-finora-subtle">Importe
                            <input id="currencyAmount" type="number" min="0" step="0.01" value="1" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-finora-navy">
                        </label>
                        <div class="rounded-xl bg-blue-50 px-3 py-2.5">
                            <span class="block text-[10px] font-bold uppercase tracking-wide text-blue-700">Resultado</span>
                            <strong id="currencyResult" class="block text-lg font-extrabold text-finora-blue">Cargando…</strong>
                        </div>
                    </div>
                </div>
                <div class="mt-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/50 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-extrabold text-finora-navy dark:text-slate-100">Fuente del tipo de cambio</p>
                        <p id="currencySourceLabel" class="mt-1 text-xs text-finora-subtle dark:text-slate-300">Consultando fuente activa…</p>
                    </div>
                    <div class="flex flex-wrap items-end gap-2">
                        <form action="{{ route('panel.tipo-cambio.refresh') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-finora-blue px-3 py-2 text-xs font-bold text-finora-blue hover:bg-blue-50 dark:hover:bg-blue-950/60">
                                <span class="material-symbols-outlined text-sm">refresh</span> Actualizar ahora
                            </button>
                        </form>
                        <form action="{{ route('panel.tipo-cambio.update') }}" method="POST" class="flex flex-wrap items-end gap-2">
                            @csrf
                            @method('PATCH')
                            <label class="text-xs font-bold text-finora-subtle dark:text-slate-300">Fuente
                                <select id="exchangeRateSource" name="fuente" class="mt-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-finora-navy dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                    <option value="OFICIAL">Oficial BCB</option>
                                    <option value="MANUAL">Manual</option>
                                </select>
                            </label>
                            <label id="manualRateField" class="hidden text-xs font-bold text-finora-subtle dark:text-slate-300">Bs por USD
                                <input name="tasa_manual" type="number" step="0.0001" min="0.0001" class="mt-1 w-28 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-finora-navy dark:border-slate-600 dark:bg-slate-900 dark:text-white" placeholder="6.96">
                            </label>
                            <button type="submit" class="rounded-xl bg-finora-navy px-3 py-2 text-xs font-bold text-white hover:bg-finora-dark">Guardar fuente</button>
                        </form>
                    </div>
                </div>
                <p id="currencyRateLabel" class="mt-3 text-[11px] text-finora-subtle">Consultando tipo de cambio oficial…</p>
            </section>
            <script>
                document.addEventListener('DOMContentLoaded', async () => {
                    const direction = document.getElementById('currencyDirection');
                    const amount = document.getElementById('currencyAmount');
                    const result = document.getElementById('currencyResult');
                     const rateLabel = document.getElementById('currencyRateLabel');
                     const source = document.getElementById('exchangeRateSource');
                     const manualField = document.getElementById('manualRateField');
                     const sourceLabel = document.getElementById('currencySourceLabel');
                     if (!direction || !amount || !result || !rateLabel) return;

                    let rate = 12;
                    try {
                        const response = await fetch('{{ route('tipo-cambio') }}', { headers: { Accept: 'application/json' } });
                         const data = await response.json();
                         rate = Number(data.bolivianos_por_dolar) || 12;
                         if (source) source.value = data.fuente || 'OFICIAL';
                         if (sourceLabel) {
                             const updatedAt = data.actualizado ? new Date(data.actualizado).toLocaleString('es-BO') : 'sin fecha';
                             sourceLabel.textContent = data.fuente === 'MANUAL'
                                 ? `Usando la tasa manual configurada. Actualizada: ${updatedAt}.`
                                 : `${data.es_respaldo ? 'Usando respaldo local' : 'Usando la tasa oficial del BCB'}. Última consulta: ${updatedAt}.`;
                         }
                     } catch {
                        rateLabel.textContent = 'No se pudo consultar el BCB; se utiliza la tasa de respaldo configurada.';
                    }

                    const format = (value, currency) => `${currency} ${Number(value).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    const update = () => {
                        const value = Number(amount.value) || 0;
                        const isDollarToBob = direction.value === 'USD_BOB';
                        result.textContent = format(isDollarToBob ? value * rate : value / rate, isDollarToBob ? 'Bs' : 'USD');
                         rateLabel.textContent = `Tasa de referencia: 1 USD = ${format(rate, 'Bs')}. La tasa oficial se consulta una vez al día.`;
                     };

                     const toggleManualRate = () => manualField?.classList.toggle('hidden', source?.value !== 'MANUAL');
                     source?.addEventListener('change', toggleManualRate);
                     toggleManualRate();

                    amount.addEventListener('input', update);
                    direction.addEventListener('change', update);
                    update();
                });
            </script>
        @endif

        @if ($pedidosPendientes > 0)
            <section class="mb-10 flex flex-col gap-3 rounded-2xl border border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-950/60 sm:flex-row sm:items-center sm:justify-between" role="status">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-purple-700 dark:text-purple-300">notifications_active</span>
                    <div>
                        <p class="text-sm font-extrabold text-purple-900 dark:text-purple-100">Tienes {{ $pedidosPendientes }} pedido(s) pendiente(s) de atención</p>
                        <p class="text-xs text-purple-700 dark:text-purple-200">Revisa disponibilidad y coordina con tus clientes.</p>
                    </div>
                </div>
                <a href="{{ route('panel.pedidos.index') }}" class="inline-flex items-center justify-center gap-1 rounded-xl bg-purple-700 px-3 py-2 text-xs font-bold text-white hover:bg-purple-800">Ver pedidos <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
            </section>
        @endif

        <section aria-labelledby="modules-title" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 id="modules-title" class="font-heading text-xl sm:text-2xl font-extrabold text-finora-navy tracking-tight">
                        Módulos del Sistema
                    </h2>
                    <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-0.5">
                        Selecciona un módulo operativo para gestionar la información.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- 1. Productos -->
                <a href="{{ route('panel.productos.index') }}" class="finora-card-interactive animate-fade-in-up delay-75 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-finora-blue transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-finora-blue group-hover:bg-finora-blue group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">package_2</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-finora-blue transition-colors flex items-center justify-between">
                            <span>Productos</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Catálogo de productos, precios y publicación.
                        </p>
                    </div>
                </a>

                <!-- 2. Clientes -->
                <a href="{{ route('panel.clientes.index') }}" class="finora-card-interactive animate-fade-in-up delay-100 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-600 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">group</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-emerald-600 transition-colors flex items-center justify-between">
                            <span>Clientes</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Directorio de clientes y personas de contacto.
                        </p>
                    </div>
                </a>

                <!-- 3. Pedidos -->
                <a href="{{ route('panel.pedidos.index') }}" class="finora-card-interactive animate-fade-in-up delay-150 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-purple-600 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">shopping_cart</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-purple-600 transition-colors flex items-center justify-between">
                            <span>Pedidos</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Solicitudes de clientes y reservas de stock.
                        </p>
                    </div>
                </a>

                <!-- 4. Compras -->
                <a href="{{ route('panel.compras.index') }}" class="finora-card-interactive animate-fade-in-up delay-200 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-cyan-700 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-700 group-hover:bg-cyan-700 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">shopping_bag</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-cyan-700 transition-colors flex items-center justify-between">
                            <span>Compras</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Reabastecimiento y recepción de mercadería.
                        </p>
                    </div>
                </a>

                <!-- 5. Inventario -->
                <a href="{{ route('panel.inventario.index') }}" class="finora-card-interactive animate-fade-in-up delay-250 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-amber-600 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">inventory_2</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-amber-600 transition-colors flex items-center justify-between">
                            <span>Inventario</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Existencias, reservaciones y kárdex de almacén.
                        </p>
                    </div>
                </a>

                <!-- 6. Ventas -->
                <a href="{{ route('panel.ventas.index') }}" class="finora-card-interactive animate-fade-in-up delay-300 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-rose-600 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">point_of_sale</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-rose-600 transition-colors flex items-center justify-between">
                            <span>Ventas</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Facturación, salidas de stock y cobro directo.
                        </p>
                    </div>
                </a>

                <!-- 7. Créditos y Pagos -->
                 <a href="{{ route('panel.pagos.index') }}" class="finora-card-interactive animate-fade-in-up delay-400 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-indigo-600 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">account_balance</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-indigo-600 transition-colors flex items-center justify-between">
                            <span>Créditos / Pagos</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Cuentas por cobrar y registro de abonos.
                        </p>
                    </div>
                 </a>

                <!-- 8. Contabilidad (Normativa Bolivia - Ley 843) -->
                <a href="{{ route('contabilidad.index') }}" class="finora-card-interactive animate-fade-in-up delay-400 bg-white p-6 rounded-2xl border-2 border-blue-200/80 shadow-sm hover:shadow-xl hover:border-finora-blue transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-blue-100 text-finora-blue tracking-wider">
                            Ley 843
                        </span>
                    </div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-finora-blue group-hover:bg-finora-blue group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">monitoring</span>
                        </div>
                        <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-finora-blue transition-colors flex items-center justify-between">
                            <span>Contabilidad</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </h3>
                        <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">
                            Libro Diario, Mayores, Balance de Apertura, Sumas y Saldos, y Estados Financieros.
                        </p>
                    </div>
                </a>

                 @if ($usuario->rol === 'CONSULTOR')
                     <a href="{{ route('panel.reportes.index') }}" class="finora-card-interactive animate-fade-in-up delay-400 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-sm hover:shadow-xl hover:border-teal-600 transition-all flex flex-col justify-between group">
                         <div>
                             <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-950 dark:text-teal-300 group-hover:bg-teal-600 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                                 <span class="material-symbols-outlined text-2xl">analytics</span>
                             </div>
                             <h3 class="font-heading text-lg font-bold text-finora-navy dark:text-slate-100 group-hover:text-teal-600 transition-colors flex items-center justify-between">
                                 <span>Reportes</span>
                                 <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                             </h3>
                             <p class="text-xs text-finora-subtle mt-1.5 leading-relaxed">Ventas, cobros, pedidos y existencias del periodo.</p>
                         </div>
                     </a>
                 @endif

                 <!-- 8. Usuarios -->
                @if ($usuario->rol === 'CONSULTOR')
                    <a href="{{ route('panel.usuarios.index') }}" class="finora-card-interactive animate-fade-in-up delay-400 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-slate-800 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 group-hover:bg-slate-800 group-hover:text-white transition-colors flex items-center justify-center mb-4 shadow-xs">
                                <span class="material-symbols-outlined text-2xl">manage_accounts</span>
                            </div>
                            <h3 class="font-heading text-lg font-bold text-finora-navy group-hover:text-slate-800 transition-colors flex items-center justify-between">
                                <span>Usuarios</span>
                                <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </h3>
                             <p class="text-xs text-finora-subtle dark:text-slate-300 mt-1.5 leading-relaxed">
                                Administración de cuentas de acceso y roles.
                            </p>
                        </div>
                    </a>
                @endif

            </div>
        </section>

    </main>

    <footer class="bg-white border-t border-slate-200/80 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 text-xs text-finora-subtle text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-1 sm:gap-2">
                <span class="font-bold text-finora-navy">Finora</span>
                <span class="max-w-[19rem] sm:max-w-none">&mdash; Sistema de gestión comercial y financiera para consultores independientes</span>
            </div>
            <div class="shrink-0">
                &copy; {{ date('Y') }} Finora. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dateEl = document.getElementById('currentDateText');
            if (dateEl) {
                const now = new Date();
                const options = { weekday: 'long', day: 'numeric', month: 'long' };
                const formatted = now.toLocaleDateString('es-ES', options);
                dateEl.textContent = formatted.charAt(0).toUpperCase() + formatted.slice(1);
            }
        });
    </script>
</body>
</html>
