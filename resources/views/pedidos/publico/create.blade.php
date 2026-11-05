<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitar Pedido - Finora | Belleza que Inspira</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}">

    <!-- Tipografía: Plus Jakarta Sans (Mismo diseño de la Landing) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Forms & Container Queries (Misma configuración visual de la Landing) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        finora: {
                            cyan: '#00c1de',
                            cyanDark: '#00a3bd',
                            cyanLight: '#e0f8fc',
                            blueDark: '#0e1b2e',
                            ice: '#f0f9ff',
                            surface: '#f8fcfe',
                            textPrimary: '#0f172a',
                            textSecondary: '#64748b'
                        }
                    },
                    boxShadow: {
                        'soft': '0 10px 30px -5px rgba(0, 193, 222, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.02)',
                        'card': '0 12px 32px 0 rgba(14, 27, 46, 0.06)',
                        'glow': '0 0 25px rgba(0, 193, 222, 0.25)',
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f7fafd;
            color: #0f172a;
        }
        .shadow-soft {
            box-shadow: 0 10px 30px -5px rgba(0, 193, 222, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        }
    </style>
</head>
<body class="min-h-screen bg-[#f7fafd] text-slate-800 antialiased selection:bg-[#00c1de] selection:text-white">
    <!-- Encabezado estilo luxury unificado -->
    @include('partials.header')

    <main class="mx-auto max-w-4xl px-4 pb-20 pt-8 sm:pt-10 sm:px-6 lg:px-8">
        <!-- Encabezado de la página de Carrito -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 border border-cyan-100 text-xs font-bold uppercase tracking-wider text-[#00c1de]">
                    Tu Carrito de Compra
                </span>
                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-[#0e1b2e] tracking-tight">
                    Arma tu pedido
                </h1>
                <p class="mt-1 text-sm text-slate-500 max-w-lg">
                    Revisa tus productos seleccionados y coordina la entrega directamente con tu consultor vía WhatsApp.
                </p>
            </div>
            <a href="{{ route('categorias') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-[#00c1de] transition-colors group shrink-0">
                <span class="group-hover:-translate-x-1 transition-transform">←</span>
                <span>Volver al catálogo</span>
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-xs sm:text-sm text-red-700 font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mx-auto max-w-2xl">
            <!-- Tarjeta unificada del pedido -->
            <section class="rounded-3xl border border-slate-100/90 bg-white p-6 sm:p-8 shadow-soft" aria-labelledby="pedido-title">
                <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100">
                    <h2 id="pedido-title" class="text-lg sm:text-xl font-bold text-[#0e1b2e]">Resumen de Selección</h2>
                    <span id="cartCount" class="rounded-full bg-cyan-50 border border-cyan-100 px-3 py-1 text-xs font-bold text-cyan-800">
                        0 productos
                    </span>
                </div>

                <!-- Lista dinámica de productos en el carrito -->
                <div id="cartItems" class="mt-4 space-y-3"></div>

                <!-- Estado vacío del carrito -->
                <div id="cartEmpty" class="mt-4 rounded-2xl bg-slate-50/70 border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">
                    <div class="w-12 h-12 rounded-full bg-cyan-50 text-[#00c1de] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-700">Todavía no agregaste productos a tu carrito.</p>
                    <p class="text-xs text-slate-400 mt-1">Explora nuestro catálogo y añade los artículos que desees consultar o pedir.</p>
                    <a href="{{ route('categorias') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#00c1de] text-white text-xs font-bold shadow-sm hover:bg-[#00a3bd] transition-all">
                        <span>Explorar productos</span>
                        <span>→</span>
                    </a>
                </div>

                <!-- Formulario de contacto y formalización -->
                <form id="publicOrderForm" action="{{ route('carrito.pedido') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="text-xs font-bold text-slate-700">
                            <span>Nombre *</span>
                            <input name="nombre" autocomplete="given-name" maxlength="100" value="{{ old('nombre') }}" required
                                placeholder="Tu nombre"
                                class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                        </label>
                        <label class="text-xs font-bold text-slate-700">
                            <span>Apellido <span class="font-normal text-slate-400">(opcional)</span></span>
                            <input name="apellido" autocomplete="family-name" maxlength="100" value="{{ old('apellido') }}"
                                placeholder="Tu apellido"
                                class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                        </label>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="text-xs font-bold text-slate-700">
                            <span>Teléfono WhatsApp *</span>
                            <input type="tel" name="telefono" autocomplete="tel" inputmode="tel" maxlength="30" value="{{ old('telefono') }}" required
                                placeholder="Ej. 70012345"
                                class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                        </label>
                        <label class="text-xs font-bold text-slate-700">
                            <span>Correo electrónico <span class="font-normal text-slate-400">(opcional)</span></span>
                            <input type="email" name="email" autocomplete="email" maxlength="255" value="{{ old('email') }}"
                                placeholder="tu@correo.com"
                                class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                        </label>
                    </div>

                    <label class="block text-xs font-bold text-slate-700">
                        <span>Dirección o referencia de entrega <span class="font-normal text-slate-400">(opcional)</span></span>
                        <input name="direccion" autocomplete="street-address" maxlength="255" value="{{ old('direccion') }}"
                            placeholder="Ciudad, zona, calle o lugar de referencia"
                            class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                    </label>

                    <label class="block text-xs font-bold text-slate-700">
                        <span>Preferencia de pago *</span>
                        <select name="preferencia_pago" required
                            class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">
                            <option value="" disabled {{ old('preferencia_pago') ? '' : 'selected' }}>Selecciona una forma de pago</option>
                            <option value="POR_CONFIRMAR" {{ old('preferencia_pago') === 'POR_CONFIRMAR' ? 'selected' : '' }}>Quiero coordinarlo con el consultor</option>
                            <option value="CONTADO" {{ old('preferencia_pago') === 'CONTADO' ? 'selected' : '' }}>Pago al contado (Efectivo o QR)</option>
                            <option value="CREDITO" {{ old('preferencia_pago') === 'CREDITO' ? 'selected' : '' }}>Solicitar compra a crédito</option>
                        </select>
                    </label>

                    <label class="block text-xs font-bold text-slate-700">
                        <span>Observaciones o nota especial <span class="font-normal text-slate-400">(opcional)</span></span>
                        <textarea name="observaciones" rows="2"
                            placeholder="Indicaciones adicionales sobre tu pedido..."
                            class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:ring-2 focus:ring-[#00c1de] focus:border-transparent transition-all">{{ old('observaciones') }}</textarea>
                    </label>

                    <div id="cartPayload"></div>

                    <button id="submitOrder" type="submit" disabled
                        class="w-full rounded-full bg-emerald-500 hover:bg-emerald-600 px-6 py-3.5 text-xs sm:text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                        </svg>
                        <span>Enviar solicitud y continuar por WhatsApp</span>
                    </button>
                    <p class="text-center text-[11px] text-slate-400">
                        El pedido queda registrado para coordinar entrega. No se realiza ningún cobro automático.
                    </p>
                </form>
            </section>
        </div>
    </main>

    <!-- Pie de página simétrico moderno -->
    @include('partials.footer')

    @php
        $productosParaCarrito = $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio_venta_actual,
            ];
        })->values();
    @endphp

    <script>
        window.finoraProducts = @json($productosParaCarrito);

        document.addEventListener('DOMContentLoaded', () => {
            const products = new Map(window.finoraProducts.map(product => [String(product.id), product]));
            const storageKey = 'finora-carrito';
            let cart = JSON.parse(window.localStorage.getItem(storageKey) || '[]').filter(item => products.has(String(item.id)));
            const items = document.getElementById('cartItems');
            const empty = document.getElementById('cartEmpty');
            const count = document.getElementById('cartCount');
            const payload = document.getElementById('cartPayload');
            const submit = document.getElementById('submitOrder');
            let displayCurrency = 'BOB';
            let exchangeRate = 12;

            function escapeHtml(value) {
                return String(value).replace(/[&<>"']/g, character => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[character]));
            }

            function save() {
                window.localStorage.setItem(storageKey, JSON.stringify(cart));
                window.dispatchEvent(new Event('storage'));
            }

            function render() {
                items.innerHTML = '';
                let totalItems = 0;
                let total = 0;
                cart.forEach((item, index) => {
                    const product = products.get(String(item.id));
                    totalItems += item.qty;
                    total += product.precio * item.qty;
                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-3 rounded-2xl bg-slate-50/80 border border-slate-100 p-3 sm:p-3.5';
                    row.innerHTML = `
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs sm:text-sm font-bold text-slate-800">${escapeHtml(product.nombre)}</p>
                            <p class="mt-0.5 text-xs font-semibold text-[#00c1de]">${formatMoney(product.precio * item.qty)}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" data-minus="${index}" class="h-7 w-7 rounded-full bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 shadow-xs flex items-center justify-center transition-all cursor-pointer" title="Restar">−</button>
                            <span class="w-5 text-center text-xs font-bold text-slate-800">${item.qty}</span>
                            <button type="button" data-plus="${index}" class="h-7 w-7 rounded-full bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 shadow-xs flex items-center justify-center transition-all cursor-pointer" title="Sumar">+</button>
                            <button type="button" data-remove="${index}" class="ml-1 text-slate-400 hover:text-rose-500 p-1 transition-colors cursor-pointer" title="Quitar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            </button>
                        </div>`;
                    items.appendChild(row);
                });

                if (cart.length) {
                    const totalRow = document.createElement('div');
                    totalRow.className = 'flex justify-between items-center border-t border-slate-100 pt-4 text-sm font-extrabold text-[#0e1b2e]';
                    totalRow.innerHTML = `<span>Total estimado</span><span class="text-lg font-extrabold text-[#00c1de]">${formatMoney(total)}</span>`;
                    items.appendChild(totalRow);
                }

                count.textContent = `${totalItems} producto${totalItems === 1 ? '' : 's'}`;
                empty.classList.toggle('hidden', cart.length > 0);
                submit.disabled = cart.length === 0;
                payload.innerHTML = cart.map((item, index) => `<input type="hidden" name="carrito[${index}][producto_id]" value="${Number(item.id)}"><input type="hidden" name="carrito[${index}][cantidad]" value="${item.qty}">`).join('');
                save();
            }

            function formatMoney(value) {
                if (displayCurrency === 'USD') {
                    return `USD ${(Number(value) / exchangeRate).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
                return `Bs ${Number(value).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }

            document.addEventListener('finora:currency-changed', event => {
                displayCurrency = event.detail.currency;
                exchangeRate = event.detail.rate;
                render();
            });

            items.addEventListener('click', event => {
                const button = event.target.closest('button');
                if (!button) return;
                const index = Number(button.dataset.plus ?? button.dataset.minus ?? button.dataset.remove);
                if (button.dataset.plus !== undefined) cart[index].qty = Math.min(cart[index].qty + 1, 99);
                if (button.dataset.minus !== undefined) cart[index].qty -= 1;
                if (button.dataset.remove !== undefined || cart[index]?.qty <= 0) cart.splice(index, 1);
                render();
            });

            document.getElementById('publicOrderForm').addEventListener('submit', event => {
                if (!cart.length) event.preventDefault();
            });

            render();
        });
    </script>
</body>
</html>
