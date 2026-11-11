<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Detalle de Compra #{{ $compra->id }} | Panel del Consultor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Ficha de Compra</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.compras.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-finora-blue hover:text-finora-deep-blue">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Volver a compras
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

        @if ($errors->has('error'))
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 flex items-center justify-between text-red-800 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span>{{ $errors->first('error') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold text-finora-navy">
                        Orden de Compra #{{ $compra->id }}
                    </h1>
                    @php
                        $badgeStyle = match($compra->estado) {
                            'RECIBIDA' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                            'EN_CAMINO' => 'bg-cyan-50 border-cyan-200 text-cyan-800',
                            'SOLICITADA' => 'bg-blue-50 border-blue-200 text-blue-800',
                            'CANCELADA' => 'bg-red-50 border-red-200 text-red-700',
                            default => 'bg-slate-100 border-slate-200 text-slate-600',
                        };
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                        {{ $compra->estado }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $compra->forma_pago === 'CREDITO' ? 'bg-purple-50 border-purple-200 text-purple-800' : 'bg-blue-50 border-blue-200 text-blue-800' }}">
                        {{ $compra->forma_pago === 'CREDITO' ? 'Crédito Proveedor' : 'Al Contado (' . ($compra->metodo_pago ?: 'Efectivo') . ')' }}
                    </span>
                </div>
                <p class="text-xs text-finora-subtle mt-1">
                    Proveedor: <strong class="text-finora-navy">{{ $compra->proveedor->nombre }}</strong> &bull; Solicitada el {{ $compra->fecha_solicitud ? $compra->fecha_solicitud->format('d/m/Y H:i') : '-' }}
                </p>
            </div>

            @if ($compra->estado !== 'RECIBIDA' && $compra->estado !== 'CANCELADA')
                <div class="flex flex-wrap items-center gap-2">
                    @if ($compra->estado === 'BORRADOR')
                        <form action="{{ route('panel.compras.estado', $compra) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="SOLICITADA">
                            <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                Marcar como Solicitada
                            </button>
                        </form>
                    @endif

                    @if ($compra->estado === 'SOLICITADA')
                        <form action="{{ route('panel.compras.estado', $compra) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="EN_CAMINO">
                            <button type="submit" class="px-3.5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                Marcar como En Camino
                            </button>
                        </form>
                    @endif

                    @if (in_array($compra->estado, ['BORRADOR', 'SOLICITADA', 'EN_CAMINO']))
                        <button type="button" id="btnOpenRecepcionModal" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1 shadow-sm cursor-pointer">
                            <span class="material-symbols-outlined text-sm">inventory</span>
                            Confirmar Recepción e Ingresar a Inventario
                        </button>

                        <button type="button" id="btnOpenCancelarModal" class="px-3 py-2 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                            Cancelar Compra
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-finora-navy">Productos Solicitados</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <thead class="bg-[#F8FAFC] text-finora-subtle font-bold uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Código</th>
                            <th class="py-2.5 px-3">Producto</th>
                            <th class="py-2.5 px-3 text-center">Cantidad</th>
                            <th class="py-2.5 px-3 text-right">Costo Unitario</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($compra->detalles as $det)
                            <tr>
                                <td class="py-2.5 px-3 font-mono text-slate-600 font-bold">{{ $det->producto->codigo }}</td>
                                <td class="py-2.5 px-3 font-bold text-finora-navy">{{ $det->producto->nombre }}</td>
                                <td class="py-2.5 px-3 text-center font-bold">{{ $det->cantidad }}</td>
                                <td class="py-2.5 px-3 text-right text-slate-600">@money($det->costo_unitario)</td>
                                <td class="py-2.5 px-3 text-right font-bold text-finora-navy">@money($det->subtotal)</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-slate-200 font-bold text-sm text-finora-navy">
                        <tr>
                            <td colspan="4" class="py-3 px-3 text-right">Total Orden:</td>
                            <td class="py-3 px-3 text-right text-finora-blue">@money($compra->total)</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($compra->observaciones)
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-xs font-bold text-finora-subtle block">Observaciones:</span>
                    <p class="text-xs text-slate-700 mt-0.5">{{ $compra->observaciones }}</p>
                </div>
            @endif
        </div>
    </main>

    @if (in_array($compra->estado, ['BORRADOR', 'SOLICITADA', 'EN_CAMINO']))
        <!-- Modal Recepción con Resumen de Ingreso a Inventario -->
        <div id="modalConfirmarRecepcion" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">inventory</span>
                        </div>
                        <div>
                            <h3 class="font-heading font-extrabold text-base text-slate-900">Confirmar Ingreso a Inventario</h3>
                            <p class="text-xs text-slate-400">Recepción de Orden #{{ $compra->id }} - {{ $compra->proveedor->nombre }}</p>
                        </div>
                    </div>
                    <button type="button" class="btnCloseModal text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Proveedor:</span>
                        <strong class="text-slate-800">{{ $compra->proveedor->nombre }}</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Forma de Pago / Financiamiento:</span>
                        <span class="font-bold text-slate-700">
                            {{ $compra->forma_pago === 'CREDITO' ? 'Crédito Proveedor (Factura a Plazo)' : 'Al Contado (' . ($compra->metodo_pago ?: 'Efectivo') . ')' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-200/80">
                        <span class="font-bold text-slate-700">Total Factura:</span>
                        <span class="font-heading font-black text-slate-900 text-sm">Bs {{ number_format($compra->total, 2) }}</span>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block">Productos que ingresarán al almacén</span>
                    <div class="max-h-48 overflow-y-auto space-y-1.5 pr-1">
                        @foreach ($compra->detalles as $det)
                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50">
                                <div class="truncate mr-2">
                                    <span class="font-bold text-slate-800 block truncate">{{ $det->producto->nombre }}</span>
                                    <span class="text-[10px] text-slate-400">Costo Unit.: Bs {{ number_format($det->costo_unitario, 2) }}</span>
                                </div>
                                <span class="font-heading font-extrabold text-emerald-600 shrink-0">+{{ $det->cantidad }} un.</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" class="btnCloseModal px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <form action="{{ route('panel.compras.estado', $compra) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estado" value="RECIBIDA">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors cursor-pointer inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            <span>Sí, Confirmar Recepción</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Cancelar Compra -->
        <div id="modalCancelarCompra" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center gap-3 text-red-600">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">cancel</span>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-slate-900">¿Cancelar Orden de Compra?</h3>
                        <p class="text-xs text-slate-400">Esta orden pasará a estado CANCELADA y no ingresará al inventario.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" class="btnCloseModal px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        Volver
                    </button>
                    <form action="{{ route('panel.compras.estado', $compra) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estado" value="CANCELADA">
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                            Sí, Cancelar Orden
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const btnOpenRecepcion = document.getElementById('btnOpenRecepcionModal');
                const modalRecepcion = document.getElementById('modalConfirmarRecepcion');
                const btnOpenCancelar = document.getElementById('btnOpenCancelarModal');
                const modalCancelar = document.getElementById('modalCancelarCompra');

                if (btnOpenRecepcion && modalRecepcion) {
                    btnOpenRecepcion.addEventListener('click', () => modalRecepcion.classList.remove('hidden'));
                }
                if (btnOpenCancelar && modalCancelar) {
                    btnOpenCancelar.addEventListener('click', () => modalCancelar.classList.remove('hidden'));
                }

                document.querySelectorAll('.btnCloseModal').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (modalRecepcion) modalRecepcion.classList.add('hidden');
                        if (modalCancelar) modalCancelar.classList.add('hidden');
                    });
                });
            });
        </script>
    @endif
</body>
</html>
