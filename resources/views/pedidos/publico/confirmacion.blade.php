<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido Recibido | Catálogo de Consultoría Natura</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}">

    <!-- Tipografía: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Forms & Container Queries -->
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
    @include('partials.header')

    <main class="mx-auto flex max-w-2xl px-4 pb-20 pt-10 sm:pt-14 sm:px-6">
        <section class="w-full rounded-3xl border border-slate-100/90 bg-white p-7 text-center shadow-soft sm:p-10 space-y-6">
            <div>
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-500 border border-emerald-100 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                    </svg>
                </div>
                <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-xs font-bold uppercase tracking-wider text-emerald-600">
                    Solicitud registrada con éxito
                </span>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#0e1b2e] tracking-tight">
                    Pedido #{{ $pedido->id }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-500 max-w-md mx-auto">
                    Hemos recibido tu solicitud. Tu consultora verificará las existencias y coordinará contigo la entrega y modalidad de pago por WhatsApp.
                </p>
            </div>

            {{-- Detalle de productos --}}
            <div class="rounded-2xl bg-slate-50/80 border border-slate-100 p-4 text-left">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Detalle de productos</h3>
                @foreach ($pedido->detalles as $detalle)
                    <div class="flex justify-between items-center gap-3 border-b border-slate-200/60 py-2.5 text-xs sm:text-sm last:border-0">
                        <span class="font-medium text-slate-700">{{ $detalle->cantidad }} × {{ $detalle->producto->nombre }}</span>
                        <span class="font-bold text-[#0e1b2e]">@money($detalle->subtotal)</span>
                    </div>
                @endforeach
                <div class="mt-3 flex justify-between items-center border-t border-slate-200 pt-3 font-extrabold text-[#0e1b2e]">
                    <span>Total del pedido</span>
                    <span class="text-base text-[#00c1de]">@money($pedido->total)</span>
                </div>
            </div>

            {{-- Opción por WhatsApp --}}
            @if ($whatsappUrl)
                <div class="pt-2">
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-500 hover:bg-emerald-600 px-6 py-3.5 text-xs sm:text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition-all cursor-pointer">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.995.545 1.761.791 2.796.791 3.182 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.778-5.768-5.778zm0-2c4.28 0 7.768 3.488 7.768 7.778 0 4.281-3.487 7.766-7.768 7.766-1.328 0-2.597-.336-3.716-.941l-4.315 1.131 1.152-4.212c-.7-1.189-1.089-2.56-1.089-3.744 0-4.29 3.488-7.778 7.768-7.778z"/>
                        </svg>
                        <span>Continuar y coordinar por WhatsApp</span>
                    </a>
                </div>
            @else
                <p class="rounded-2xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800">
                    El pedido fue registrado correctamente. Tu consultora se comunicará contigo a la brevedad.
                </p>
            @endif

            <div>
                <a href="{{ route('categorias') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-[#00c1de] transition-colors">
                    <span>←</span>
                    <span>Volver al catálogo comercial</span>
                </a>
            </div>
        </section>
    </main>

    <!-- Pie de página unificado -->
    @include('partials.footer')

    <script>
        // Limpiar carrito local
        window.localStorage.removeItem('finora-carrito');
        window.dispatchEvent(new Event('storage'));
    </script>
</body>
</html>
