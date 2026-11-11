<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Editar Venta #{{ $venta->id }} (Borrador) | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Editar Venta en Borrador</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.ventas.show', $venta) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Volver a la venta</span>
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="font-heading text-xl font-extrabold text-finora-navy">
                        Editar Venta #{{ $venta->id }} (Borrador)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">Puedes ajustar el cliente, productos, cantidades, precios y condiciones antes de confirmar.</p>
                </div>
                <span class="rounded-full border border-purple-200 bg-purple-50 text-purple-800 px-3 py-1 text-xs font-bold">
                    Estado: BORRADOR
                </span>
            </div>

            @if ($errors->any())
                <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 mb-6 text-red-800 text-xs">
                    <p class="font-bold mb-1">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('panel.ventas.update', $venta) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                @if ($venta->pedido_id)
                    <input type="hidden" name="pedido_id" value="{{ $venta->pedido_id }}">
                @endif

                <div class="space-y-3">
                    <label class="block text-xs font-bold text-finora-navy">Cliente *</label>
                    <div id="contenedor-cliente-existente">
                        <select id="cliente_id" name="cliente_id" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach ($clientes as $cli)
                                <option value="{{ $cli->id }}"
                                    data-estado="{{ $cli->estado_crediticio }}"
                                    data-deuda="{{ number_format($cli->saldo_deudor, 2) }}"
                                    data-vencidas="{{ $cli->cuotas_vencidas_count }}"
                                    {{ old('cliente_id', $venta->cliente_id) == $cli->id ? 'selected' : '' }}>
                                    {{ $cli->persona->nombre }} {{ $cli->persona->apellido }} ({{ $cli->telefono ?: 'Sin teléfono' }})
                                    @if ($cli->saldo_deudor > 0)
                                        - Deuda: Bs {{ number_format($cli->saldo_deudor, 2) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Modalidad de Pago -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="forma_pago" class="block text-xs font-bold text-finora-navy mb-1">Forma de Pago *</label>
                        <select id="forma_pago" name="forma_pago" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="CONTADO" {{ old('forma_pago', $venta->forma_pago) === 'CONTADO' ? 'selected' : '' }}>Al Contado (Pago Inmediato)</option>
                            <option value="CREDITO" {{ old('forma_pago', $venta->forma_pago) === 'CREDITO' ? 'selected' : '' }}>Al Crédito (En Cuotas)</option>
                        </select>
                    </div>

                    <div id="metodo-pago-container" class="{{ old('forma_pago', $venta->forma_pago) === 'CREDITO' ? 'hidden' : '' }}">
                        <label for="metodo_pago" class="block text-xs font-bold text-finora-navy mb-1">Método de Cobro Inmediato *</label>
                        <select id="metodo_pago" name="metodo_pago" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="EFECTIVO" {{ old('metodo_pago', $venta->metodo_pago ?? 'EFECTIVO') === 'EFECTIVO' ? 'selected' : '' }}>Efectivo (Caja)</option>
                            <option value="TRANSFERENCIA" {{ old('metodo_pago', $venta->metodo_pago) === 'TRANSFERENCIA' ? 'selected' : '' }}>Transferencia Bancaria</option>
                            <option value="QR" {{ old('metodo_pago', $venta->metodo_pago) === 'QR' ? 'selected' : '' }}>Pago QR Simple</option>
                        </select>
                    </div>

                    <div id="cuotas-container" class="{{ old('forma_pago', $venta->forma_pago) === 'CREDITO' ? '' : 'hidden' }}">
                        <label for="numero_cuotas" class="block text-xs font-bold text-finora-navy mb-1">Número de Cuotas</label>
                        <input type="number" id="numero_cuotas" name="numero_cuotas" min="1" max="24"
                               value="{{ old('numero_cuotas', $venta->numero_cuotas ?: 2) }}"
                               class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <div class="border-t border-b border-slate-100 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-sm text-finora-navy">Productos de la Venta</h3>
                        <button type="button" id="btn-add-item" class="px-3 py-1.5 bg-cyan-50 text-[#00a3bd] hover:bg-cyan-100 rounded-xl text-xs font-bold inline-flex items-center gap-1 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Agregar Producto
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        @foreach ($venta->detalles as $idx => $det)
                            <div class="item-row bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2 relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-finora-subtle uppercase">Ítem #{{ $idx + 1 }}</span>
                                    @if ($loop->count > 1)
                                        <button type="button" class="btn-remove-row text-red-500 hover:text-red-700 p-0.5" title="Eliminar fila">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                        </button>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                    <div class="sm:col-span-6">
                                        <label class="block text-[10px] font-bold text-finora-subtle mb-1">Producto</label>
                                        <select name="detalles[{{ $idx }}][producto_id]" required class="select-prod w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                            <option value="">-- Seleccionar Producto --</option>
                                            @foreach ($productos as $prod)
                                                <option value="{{ $prod->id }}"
                                                    data-precio="{{ $prod->precio_venta_actual }}"
                                                    {{ $det->producto_id == $prod->id ? 'selected' : '' }}>
                                                    {{ $prod->codigo }} - {{ $prod->nombre }} (Bs {{ number_format($prod->precio_venta_actual, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-bold text-finora-subtle mb-1">Cantidad</label>
                                        <input type="number" min="1" name="detalles[{{ $idx }}][cantidad]" value="{{ $det->cantidad }}" required class="input-cantidad w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-bold text-finora-subtle mb-1">Precio Unitario (Bs)</label>
                                        <input type="number" step="0.01" min="0" name="detalles[{{ $idx }}][precio_unitario]" value="{{ $det->precio_unitario }}" required class="input-precio w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Descuento y Totales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                    <div>
                        <label for="descuento" class="block text-xs font-bold text-finora-navy mb-1">Descuento Global (Bs)</label>
                        <input type="number" step="0.01" min="0" id="descuento" name="descuento" value="{{ old('descuento', $venta->descuento) }}" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex justify-between items-center text-xs">
                        <span class="font-bold text-finora-subtle">Total Calculado:</span>
                        <span id="label-total" class="font-heading font-extrabold text-base text-finora-navy">Bs {{ number_format($venta->total, 2) }}</span>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('panel.ventas.show', $venta) }}" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-[#00c1de] hover:bg-[#00a3bd] px-6 py-2.5 rounded-xl text-white font-heading font-bold text-xs shadow-md transition-all cursor-pointer">
                        Guardar Cambios en Borrador
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formaPago = document.getElementById('forma_pago');
            const cuotasContainer = document.getElementById('cuotas-container');
            const metodoPagoContainer = document.getElementById('metodo-pago-container');
            const numeroCuotasInput = document.getElementById('numero_cuotas');
            const container = document.getElementById('items-container');
            const btnAdd = document.getElementById('btn-add-item');
            const descuentoInput = document.getElementById('descuento');
            const labelTotal = document.getElementById('label-total');

            function toggleCuotas() {
                if (formaPago.value === 'CREDITO') {
                    cuotasContainer.classList.remove('hidden');
                    metodoPagoContainer.classList.add('hidden');
                    if (numeroCuotasInput) numeroCuotasInput.setAttribute('required', 'required');
                } else {
                    cuotasContainer.classList.add('hidden');
                    metodoPagoContainer.classList.remove('hidden');
                    if (numeroCuotasInput) numeroCuotasInput.removeAttribute('required');
                }
            }
            formaPago.addEventListener('change', toggleCuotas);

            function calcularTotal() {
                let total = 0;
                container.querySelectorAll('.item-row').forEach(row => {
                    const cant = parseFloat(row.querySelector('.input-cantidad')?.value) || 0;
                    const precio = parseFloat(row.querySelector('.input-precio')?.value) || 0;
                    total += cant * precio;
                });
                const desc = parseFloat(descuentoInput?.value) || 0;
                total = Math.max(0, total - desc);
                labelTotal.textContent = 'Bs ' + total.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            container.addEventListener('input', calcularTotal);
            descuentoInput.addEventListener('input', calcularTotal);

            let itemIdx = {{ $venta->detalles->count() }};
            const productsOptions = `@foreach ($productos as $prod)<option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta_actual }}">{{ $prod->codigo }} - {{ $prod->nombre }} (Bs {{ number_format($prod->precio_venta_actual, 2) }})</option>@endforeach`;

            btnAdd.addEventListener('click', () => {
                const div = document.createElement('div');
                div.className = 'item-row bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2 relative';
                div.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-finora-subtle uppercase">Ítem de Venta</span>
                        <button type="button" class="btn-remove-row text-red-500 hover:text-red-700 p-0.5" title="Eliminar fila">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                        <div class="sm:col-span-6">
                            <label class="block text-[10px] font-bold text-finora-subtle mb-1">Producto</label>
                            <select name="detalles[${itemIdx}][producto_id]" required class="select-prod w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                <option value="">-- Seleccionar Producto --</option>
                                ${productsOptions}
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[10px] font-bold text-finora-subtle mb-1">Cantidad</label>
                            <input type="number" min="1" name="detalles[${itemIdx}][cantidad]" value="1" required class="input-cantidad w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[10px] font-bold text-finora-subtle mb-1">Precio Unitario (Bs)</label>
                            <input type="number" step="0.01" min="0" name="detalles[${itemIdx}][precio_unitario]" value="0.00" required class="input-precio w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                        </div>
                    </div>
                `;
                container.appendChild(div);
                itemIdx++;

                div.querySelector('.select-prod').addEventListener('change', (e) => {
                    const opt = e.target.selectedOptions[0];
                    if (opt && opt.dataset.precio) {
                        div.querySelector('.input-precio').value = parseFloat(opt.dataset.precio).toFixed(2);
                        calcularTotal();
                    }
                });

                div.querySelector('.btn-remove-row').addEventListener('click', () => {
                    div.remove();
                    calcularTotal();
                });
            });

            container.addEventListener('change', (e) => {
                if (e.target.classList.contains('select-prod')) {
                    const opt = e.target.selectedOptions[0];
                    if (opt && opt.dataset.precio) {
                        const row = e.target.closest('.item-row');
                        row.querySelector('.input-precio').value = parseFloat(opt.dataset.precio).toFixed(2);
                        calcularTotal();
                    }
                }
            });

            container.querySelectorAll('.btn-remove-row').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.target.closest('.item-row').remove();
                    calcularTotal();
                });
            });
        });
    </script>
</body>
</html>
