<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Detalle de Venta #{{ $venta->id }} | Panel del Consultor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Ficha de Venta</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.ventas.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Ventas</span>
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

        @if ($errors->has('stock'))
            <div role="alert" class="rounded-2xl border-2 border-amber-300 bg-amber-50/90 p-5 text-amber-950 shadow-sm space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-heading font-extrabold text-sm text-amber-950">Stock Insuficiente para Confirmar Salida</h4>
                        <p class="text-xs text-amber-900 mt-1 leading-relaxed">{{ $errors->first('stock') }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <a href="{{ route('panel.compras.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span>
                                <span>Registrar Compra / Reponer Stock</span>
                            </a>
                            <span class="text-[11px] text-amber-800 font-medium">
                                💡 Tip: También puedes adquirir este producto en tu cuenta de consultora Natura e ingresarlo en Compras cuando llegue el pedido.
                            </span>
                        </div>
                    </div>
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

        @php
            // Preparar número de WhatsApp y mensaje de recibo
            $clienteTel = preg_replace('/[^0-9]/', '', $venta->cliente->telefono ?? '');
            if (strlen($clienteTel) === 8) {
                $clienteTel = '591' . $clienteTel;
            }

            $lineasRecibo = [
                "🌸 *FINORA - RECIBO DE VENTA #{$venta->id}* 🌸",
                "¡Hola {$venta->cliente->nombre}! Muchas gracias por tu compra de productos Natura.",
                "",
                "📅 *Fecha:* " . ($venta->fecha ? $venta->fecha->format('d/m/Y H:i') : now()->format('d/m/Y')),
                "🛍️ *Detalle:*",
            ];
            foreach ($venta->detalles as $det) {
                $lineasRecibo[] = " • {$det->cantidad}x {$det->producto->nombre} - Bs " . number_format($det->subtotal, 2);
            }
            if ((float) $venta->descuento > 0) {
                $lineasRecibo[] = "🎁 *Descuento:* -Bs " . number_format($venta->descuento, 2);
            }
            $lineasRecibo[] = "💰 *TOTAL:* Bs " . number_format($venta->total, 2);
            $lineasRecibo[] = "💳 *Modalidad:* " . ($venta->forma_pago === 'CREDITO' ? "Crédito ({$venta->numero_cuotas} cuotas)" : "Contado");

            if ($venta->forma_pago === 'CREDITO') {
                $lineasRecibo[] = "💵 *Total abonado:* Bs " . number_format($venta->total_pagado, 2);
                $lineasRecibo[] = "⚠️ *Saldo pendiente:* Bs " . number_format($venta->saldo_pendiente, 2);
            }
            $lineasRecibo[] = "";
            $lineasRecibo[] = "✨ *Consultora Natura:* Finora Gestión Inteligente";
            $lineasRecibo[] = "¡Que disfrutes mucho tus productos!";

            $mensajeReciboRaw = implode("\n", $lineasRecibo);
            $urlWaRecibo = $clienteTel
                ? "https://wa.me/{$clienteTel}?text=" . rawurlencode($mensajeReciboRaw)
                : "https://wa.me/?text=" . rawurlencode($mensajeReciboRaw);
        @endphp

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold text-finora-navy">
                        Venta #{{ $venta->id }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border bg-purple-50 border-purple-200 text-purple-800 dark:bg-purple-950/70 dark:border-purple-700 dark:text-purple-200">
                        {{ $venta->forma_pago }}
                    </span>
                    @php
                        $badgeStyle = match($venta->estado) {
                            'CONFIRMADA' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                            'ANULADA' => 'bg-red-50 border-red-200 text-red-700',
                            default => 'bg-slate-100 border-slate-200 text-slate-600',
                        };
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeStyle }} dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100">
                        {{ $venta->estado }}
                    </span>
                </div>
                <p class="text-xs text-finora-subtle mt-1">
                    Cliente: <strong class="text-finora-navy">{{ $venta->cliente->persona->nombre }} {{ $venta->cliente->persona->apellido }}</strong>
                    @if ($venta->cliente->telefono)
                        &bull; Tel: <span class="font-mono text-slate-600">{{ $venta->cliente->telefono }}</span>
                    @endif
                    &bull; Registrada el {{ $venta->fecha ? $venta->fecha->format('d/m/Y H:i') : '-' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Botón Enviar Recibo WhatsApp --}}
                <a href="{{ $urlWaRecibo }}" target="_blank" rel="noopener noreferrer" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1.5 shadow-sm" title="Enviar comprobante detallado al cliente por WhatsApp">
                    <span class="material-symbols-outlined text-sm">chat</span>
                    <span>Recibo WhatsApp</span>
                </a>

                @if ($venta->estado === 'BORRADOR')
                    <a href="{{ route('panel.ventas.edit', $venta) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1.5" title="Editar datos o productos de este borrador">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        <span>Editar Borrador</span>
                    </a>

                    <button type="button" id="btnOpenConfirmModal" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1 shadow-sm cursor-pointer">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Confirmar Venta y Descontar Inventario</span>
                    </button>

                    <button type="button" id="btnOpenDeleteModal" class="px-3 py-2 bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-700 text-xs font-bold rounded-xl transition-colors inline-flex items-center gap-1 cursor-pointer" title="Descartar y eliminar este borrador">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                @endif

                @if ($venta->estado === 'CONFIRMADA' && auth()->user()?->rol === 'CONSULTOR')
                    <button type="button" id="btnOpenAnularModal" class="px-3.5 py-2 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                        Anular Venta
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                {{-- Detalle de Productos --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="font-heading font-bold text-sm text-finora-navy">Detalle de Productos</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs divide-y divide-slate-100">
                            <thead class="bg-[#F8FAFC] text-finora-subtle font-bold uppercase">
                                <tr>
                                    <th class="py-2.5 px-3">Código</th>
                                    <th class="py-2.5 px-3">Producto</th>
                                    <th class="py-2.5 px-3 text-center">Cantidad</th>
                                    <th class="py-2.5 px-3 text-right">Precio Unit.</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($venta->detalles as $det)
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono text-slate-600 font-bold">{{ $det->producto->codigo }}</td>
                                        <td class="py-2.5 px-3 font-bold text-finora-navy">{{ $det->producto->nombre }}</td>
                                        <td class="py-2.5 px-3 text-center font-bold">{{ $det->cantidad }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-600">@money($det->precio_unitario)</td>
                                        <td class="py-2.5 px-3 text-right font-bold text-finora-navy">@money($det->subtotal)</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-slate-200 font-bold text-xs text-finora-navy">
                                <tr>
                                    <td colspan="4" class="py-2 px-3 text-right text-finora-subtle">Subtotal General:</td>
                                    <td class="py-2 px-3 text-right">@money($venta->subtotal)</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="py-2 px-3 text-right text-finora-subtle">Descuento:</td>
                                    <td class="py-2 px-3 text-right text-red-600">-@money($venta->descuento)</td>
                                </tr>
                                <tr class="text-sm font-extrabold border-t border-slate-200">
                                    <td colspan="4" class="py-3 px-3 text-right">Total Venta:</td>
                                    <td class="py-3 px-3 text-right text-finora-blue">@money($venta->total)</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Plan de Cuotas y Cobranzas (Solo si es a crédito) --}}
                @if ($venta->forma_pago === 'CREDITO')
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-heading font-bold text-sm text-finora-navy">Plan de Cuotas y Cobranzas</h3>
                                <p class="text-xs text-finora-subtle mt-0.5">Control de vencimientos y recordatorios rápidos de cobranza.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $venta->cuotas->count() }} Cuotas pactadas
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs divide-y divide-slate-100">
                                <thead class="bg-[#F8FAFC] text-finora-subtle font-bold uppercase">
                                    <tr>
                                        <th class="py-2.5 px-3">Cuota</th>
                                        <th class="py-2.5 px-3">Vencimiento</th>
                                        <th class="py-2.5 px-3 text-right">Monto</th>
                                        <th class="py-2.5 px-3 text-right">Pagado</th>
                                        <th class="py-2.5 px-3 text-right">Saldo</th>
                                        <th class="py-2.5 px-3 text-center">Estado</th>
                                        <th class="py-2.5 px-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($venta->cuotas as $cuota)
                                        @php
                                            $isVencida = $cuota->estado !== 'PAGADA' && $cuota->fecha_vencimiento && $cuota->fecha_vencimiento->isPast();
                                            $cuotaBadge = match(true) {
                                                $cuota->estado === 'PAGADA' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                                                $isVencida => 'bg-red-50 border-red-200 text-red-700',
                                                $cuota->estado === 'PARCIAL' => 'bg-blue-50 border-blue-200 text-blue-700',
                                                default => 'bg-amber-50 border-amber-200 text-amber-800',
                                            };
                                            $cuotaLabel = $cuota->estado === 'PAGADA' ? 'PAGADA' : ($isVencida ? 'VENCIDA' : $cuota->estado);

                                            // Mensaje de recordatorio WhatsApp
                                            $msgRecordatorio = "🌸 *RECORDATORIO DE PAGO - NATURA* 🌸\n"
                                                . "Hola *{$venta->cliente->nombre}*, te escribo para recordarte sobre tu cuota pendiente:\n\n"
                                                . "📌 *Venta #{$venta->id} - Cuota #{$cuota->numero}*\n"
                                                . "💰 *Monto pendiente:* Bs " . number_format($cuota->saldo_pendiente, 2) . "\n"
                                                . "🗓️ *Vencimiento:* " . ($cuota->fecha_vencimiento ? $cuota->fecha_vencimiento->format('d/m/Y') : 'Inmediato') . "\n\n"
                                                . "📲 Puedes pagar fácilmente mediante *QR Simple* al instante.\n"
                                                . "¡Agradezco mucho tu puntualidad! ✨";
                                            $urlWaCuota = $clienteTel
                                                ? "https://wa.me/{$clienteTel}?text=" . rawurlencode($msgRecordatorio)
                                                : "https://wa.me/?text=" . rawurlencode($msgRecordatorio);
                                        @endphp
                                        <tr class="{{ $isVencida ? 'bg-red-50/30' : '' }}">
                                            <td class="py-3 px-3 font-bold text-finora-navy">#{{ $cuota->numero }}</td>
                                            <td class="py-3 px-3 text-slate-600">
                                                {{ $cuota->fecha_vencimiento ? $cuota->fecha_vencimiento->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="py-3 px-3 text-right font-medium text-slate-600">@money($cuota->monto)</td>
                                            <td class="py-3 px-3 text-right font-medium text-emerald-700">@money($cuota->monto_pagado)</td>
                                            <td class="py-3 px-3 text-right font-bold {{ $cuota->saldo_pendiente > 0 ? 'text-red-600' : 'text-slate-400' }}">
                                                @money($cuota->saldo_pendiente)
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $cuotaBadge }}">
                                                    {{ $cuotaLabel }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    @if ($cuota->saldo_pendiente > 0)
                                                        {{-- Botón Recordatorio WhatsApp --}}
                                                        <a href="{{ $urlWaCuota }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg text-[11px] font-bold transition-colors" title="Recordatorio WhatsApp">
                                                            <span class="material-symbols-outlined text-[15px]">chat</span>
                                                            <span>Recordar</span>
                                                        </a>
                                                    @else
                                                        <span class="text-emerald-600 font-bold text-[11px] inline-flex items-center gap-0.5">
                                                            <span class="material-symbols-outlined text-sm">check</span> Al día
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-finora-subtle">No se han registrado cuotas para esta venta.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                {{-- Resumen de Pagos --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                    <h3 class="font-heading font-bold text-sm text-finora-navy border-b border-slate-100 pb-2">Resumen de Pagos</h3>
                    <div class="flex justify-between text-xs">
                        <span class="text-finora-subtle">Total Venta:</span>
                        <span class="font-bold text-finora-navy">@money($venta->total)</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-finora-subtle">Monto Pagado:</span>
                        <span class="font-bold text-emerald-700">@money($venta->total_pagado)</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-slate-100 pt-2">
                        <span class="font-bold text-finora-navy">Saldo Pendiente:</span>
                        <span class="font-extrabold text-red-600">@money($venta->saldo_pendiente)</span>
                    </div>

                    @if ($venta->estado === 'CONFIRMADA' && $venta->saldo_pendiente > 0)
                        <div class="pt-2">
                            <a href="{{ route('panel.pagos.create', ['venta_id' => $venta->id]) }}" class="w-full py-2.5 bg-finora-navy text-white text-xs font-bold rounded-xl hover:bg-finora-dark transition-colors inline-flex items-center justify-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-sm">payments</span>
                                Registrar Cobro / Pago
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Historial de Pagos --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-3">
                    <h3 class="font-heading font-bold text-sm text-finora-navy border-b border-slate-100 pb-2">Historial de Pagos</h3>
                    @forelse ($venta->pagos as $p)
                        <div class="text-xs p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-finora-navy block">@money($p->monto)</span>
                                <span class="text-[10px] text-finora-subtle">
                                    {{ $p->metodo }} &bull; {{ $p->fecha ? $p->fecha->format('d/m/Y H:i') : '' }}
                                    @if ($p->observacion)
                                        <br/><span class="text-slate-500 italic">{{ $p->observacion }}</span>
                                    @endif
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->estado === 'REGISTRADO' ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700' }}">
                                {{ $p->estado }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-finora-subtle py-2 text-center">No hay pagos registrados para esta venta.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    @if ($venta->estado === 'BORRADOR')
        <!-- Modal In-App de Confirmación con Resumen Previo (Doble Aprobación) -->
        <div id="modalConfirmarVenta" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">verified</span>
                        </div>
                        <div>
                            <h3 class="font-heading font-extrabold text-base text-slate-900">Confirmación de Venta #{{ $venta->id }}</h3>
                            <p class="text-xs text-slate-400">Verifica los detalles antes de aplicar la salida de inventario.</p>
                        </div>
                    </div>
                    <button type="button" class="btnCloseModal text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Resumen Financiero y Cliente -->
                <div class="bg-slate-50 rounded-2xl p-4 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Cliente:</span>
                        <strong class="text-slate-800">{{ $venta->cliente->persona->nombre }} {{ $venta->cliente->persona->apellido }}</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Modalidad:</span>
                        <span class="font-bold text-slate-700">
                            {{ $venta->forma_pago === 'CREDITO' ? "Crédito ({$venta->numero_cuotas} cuotas)" : "Al Contado ({$venta->metodo_pago})" }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-200/80">
                        <span class="font-bold text-slate-700">Total a Facturar:</span>
                        <span class="font-heading font-black text-emerald-600 text-sm">Bs {{ number_format($venta->total, 2) }}</span>
                    </div>
                </div>

                <!-- Impacto en Inventario -->
                <div class="space-y-2 text-xs">
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block">Impacto en Inventario (Salida de Almacén)</span>
                    <div class="max-h-48 overflow-y-auto space-y-1.5 pr-1">
                        @foreach ($venta->detalles as $det)
                            @php
                                $stockFisico = (int) \App\Models\MovimientoInventario::where('producto_id', $det->producto_id)->sum('cantidad');
                                $hayStock = $stockFisico >= $det->cantidad;
                            @endphp
                            <div class="flex items-center justify-between p-2 rounded-xl {{ $hayStock ? 'bg-slate-50' : 'bg-red-50 border border-red-200' }}">
                                <div class="truncate mr-2">
                                    <span class="font-bold text-slate-800 block truncate">{{ $det->producto->nombre }}</span>
                                    <span class="text-[10px] text-slate-400">Stock actual: {{ $stockFisico }} un.</span>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-900 block">-{{ $det->cantidad }} un.</span>
                                    @if ($hayStock)
                                        <span class="text-[10px] font-bold text-emerald-600">✓ Disponible</span>
                                    @else
                                        <span class="text-[10px] font-bold text-red-600">⚠️ Faltan {{ $det->cantidad - $stockFisico }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" class="btnCloseModal px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <form action="{{ route('panel.ventas.confirmar', $venta) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors cursor-pointer inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            <span>Sí, Confirmar y Aplicar Salida</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Descartar Borrador -->
        <div id="modalDescartarBorrador" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center gap-3 text-red-600">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">delete</span>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-slate-900">¿Descartar Venta en Borrador?</h3>
                        <p class="text-xs text-slate-400">Esta acción eliminará el registro de la venta #{{ $venta->id }}.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" class="btnCloseModal px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <form action="{{ route('panel.ventas.destroy', $venta) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                            Sí, Eliminar Borrador
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($venta->estado === 'CONFIRMADA' && auth()->user()?->rol === 'CONSULTOR')
        <!-- Modal Anular Venta -->
        <div id="modalAnularVenta" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center gap-3 text-amber-600">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">undo</span>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-slate-900">¿Anular Venta #{{ $venta->id }}?</h3>
                        <p class="text-xs text-slate-400">Se revertirá la salida de inventario y los asientos contables asociados.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" class="btnCloseModal px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <form action="{{ route('panel.ventas.anular', $venta) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                            Sí, Anular Venta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnOpenConfirm = document.getElementById('btnOpenConfirmModal');
            const modalConfirm = document.getElementById('modalConfirmarVenta');
            const btnOpenDelete = document.getElementById('btnOpenDeleteModal');
            const modalDelete = document.getElementById('modalDescartarBorrador');
            const btnOpenAnular = document.getElementById('btnOpenAnularModal');
            const modalAnular = document.getElementById('modalAnularVenta');

            if (btnOpenConfirm && modalConfirm) {
                btnOpenConfirm.addEventListener('click', () => modalConfirm.classList.remove('hidden'));
            }
            if (btnOpenDelete && modalDelete) {
                btnOpenDelete.addEventListener('click', () => modalDelete.classList.remove('hidden'));
            }
            if (btnOpenAnular && modalAnular) {
                btnOpenAnular.addEventListener('click', () => modalAnular.classList.remove('hidden'));
            }

            document.querySelectorAll('.btnCloseModal').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (modalConfirm) modalConfirm.classList.add('hidden');
                    if (modalDelete) modalDelete.classList.add('hidden');
                    if (modalAnular) modalAnular.classList.add('hidden');
                });
            });
        });
    </script>
    </main>

    @include('partials.panel-footer')
</body>
</html>