<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Movimientos de Inventario | Panel del Consultor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.index') }}">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Movimientos de Inventario</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.inventario.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-finora-blue hover:text-finora-deep-blue">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Volver a existencias
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if (session('exito'))
            <div role="alert" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center justify-between text-emerald-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span>{{ session('exito') }}</span>
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy">Kárdex de Movimientos</h1>
                <p class="text-xs sm:text-sm text-finora-subtle font-medium mt-1">
                    Auditoría detallada de entradas, salidas y ajustes manuales del almacén.
                </p>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-slate-200 text-finora-subtle uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4">Fecha</th>
                            <th class="py-3.5 px-4">Producto</th>
                            <th class="py-3.5 px-4">Tipo de Movimiento</th>
                            <th class="py-3.5 px-4 text-center">Cantidad</th>
                            <th class="py-3.5 px-4">Observación / Origen</th>
                            <th class="py-3.5 px-4">Registrado Por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-finora-navy">
                        @forelse ($movimientos as $mov)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-mono text-slate-600 font-bold">
                                    {{ $mov->fecha ? $mov->fecha->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-bold text-finora-navy">
                                    {{ $mov->producto->nombre }}
                                </td>
                                <td class="py-3 px-4 font-bold">
                                    <span class="px-2 py-0.5 rounded text-[10px] {{ $mov->cantidad > 0 ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700' }}">
                                        {{ $mov->tipo }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-extrabold text-sm {{ $mov->cantidad > 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                    {{ $mov->cantidad > 0 ? "+{$mov->cantidad}" : $mov->cantidad }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $mov->observacion ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $mov->registradoPor ? $mov->registradoPor->persona->nombre : 'Sistema' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-finora-subtle">
                                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">history</span>
                                    <span>No hay movimientos de inventario registrados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movimientos->hasPages())
                <div class="p-4 border-t border-slate-200/80 bg-[#F8FAFC]">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </main>

</body>
</html>
