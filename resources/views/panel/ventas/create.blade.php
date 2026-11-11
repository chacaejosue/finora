<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Registrar Venta | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Nueva Venta</span>
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

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
             <div class="mb-6 flex flex-wrap items-center gap-3">
                 <h1 class="font-heading text-xl font-extrabold text-finora-navy">
                 @if ($pedido)
                     Registrar venta vinculada al Pedido #{{ $pedido->id }}
                 @else
                     Registrar nueva venta directa
                 @endif
                 </h1>
                 <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $pedido ? 'border-purple-200 bg-purple-50 text-purple-800 dark:border-purple-700 dark:bg-purple-950/60 dark:text-purple-200' : 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-700 dark:bg-blue-950/60 dark:text-blue-200' }}">
                     {{ $pedido ? 'Origen: pedido' : 'Origen: venta directa' }}
                 </span>
             </div>

            <form action="{{ route('panel.ventas.store') }}" method="POST" class="space-y-6">
                @csrf

                @if ($pedido)
                    @php
                        $preferenciaPago = 'CONTADO';
                        if (str_contains(strtoupper($pedido->observaciones ?? ''), 'CREDITO')) {
                            $preferenciaPago = 'CREDITO';
                        }
                    @endphp
                    <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                    <input type="hidden" name="cliente_id" value="{{ $pedido->cliente_id }}">
                    <div class="bg-cyan-50 dark:bg-cyan-950/50 border border-cyan-200 dark:border-cyan-800 p-4 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-cyan-900 dark:text-cyan-100 shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#00c1de] text-white flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                            </div>
                            <div>
                                <span class="font-extrabold text-sm text-finora-navy dark:text-white block">Facturando Pedido #{{ $pedido->id }}</span>
                                <span class="text-slate-600 dark:text-slate-300">Cliente: <strong>{{ $pedido->cliente->persona->nombre }} {{ $pedido->cliente->persona->apellido }}</strong> &bull; {{ $pedido->detalles->sum('cantidad') }} ítems &bull; Total: <strong>Bs {{ number_format($pedido->total, 2, ',', '.') }}</strong></span>
                            </div>
                        </div>
                        <a href="{{ route('panel.pedidos.show', $pedido) }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#00c1de] hover:underline shrink-0">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                            Ver ficha de pedido
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-finora-navy">Cliente *</label>
                            <button type="button" id="btn-toggle-nuevo-cliente" class="text-xs font-bold text-finora-blue hover:underline inline-flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-sm" id="icono-toggle-cliente">person_add</span>
                                <span id="texto-toggle-cliente">+ Registrar Cliente Nuevo (WhatsApp / Rápido)</span>
                            </button>
                        </div>

                        <!-- Selector de cliente existente -->
                        <div id="contenedor-cliente-existente">
                            <select id="cliente_id" name="cliente_id" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                                <option value="">-- Seleccionar Cliente --</option>
                                @foreach ($clientes as $cli)
                                    <option value="{{ $cli->id }}"
                                        data-estado="{{ $cli->estado_crediticio }}"
                                        data-deuda="{{ number_format($cli->saldo_deudor, 2) }}"
                                        data-vencidas="{{ $cli->cuotas_vencidas_count }}"
                                        {{ old('cliente_id') == $cli->id ? 'selected' : '' }}>
                                        {{ $cli->persona->nombre }} {{ $cli->persona->apellido }}
                                        @if ($cli->estado_crediticio === 'MOROSO')
                                            [⚠️ MOROSO - {{ $cli->cuotas_vencidas_count }} cuotas vencidas]
                                        @elseif ($cli->estado_crediticio === 'CON_DEUDA')
                                            [Saldo: Bs {{ number_format($cli->saldo_deudor, 2) }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div id="aviso-cliente-credito" class="hidden mt-2 p-3 rounded-xl border text-xs font-semibold flex items-center gap-2"></div>
                            @error('cliente_id') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Campos de cliente nuevo al vuelo -->
                        <div id="contenedor-cliente-nuevo" class="hidden p-4 rounded-xl border border-blue-200 bg-blue-50/60 space-y-3">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-blue-900">
                                <span class="material-symbols-outlined text-sm text-blue-600">contact_phone</span>
                                <span>Datos del Cliente Nuevo (Se guardará automáticamente en el sistema)</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label for="nuevo_cliente_nombre" class="block text-[11px] font-bold text-slate-700 mb-1">Nombre *</label>
                                    <input type="text" id="nuevo_cliente_nombre" name="nuevo_cliente_nombre" value="{{ old('nuevo_cliente_nombre') }}" placeholder="Ej. Carla" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none font-semibold">
                                    @error('nuevo_cliente_nombre') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="nuevo_cliente_apellido" class="block text-[11px] font-bold text-slate-700 mb-1">Apellido (Opcional)</label>
                                    <input type="text" id="nuevo_cliente_apellido" name="nuevo_cliente_apellido" value="{{ old('nuevo_cliente_apellido') }}" placeholder="Ej. Mendoza" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none">
                                </div>
                                <div>
                                    <label for="nuevo_cliente_telefono" class="block text-[11px] font-bold text-slate-700 mb-1">Celular / WhatsApp *</label>
                                    <input type="text" id="nuevo_cliente_telefono" name="nuevo_cliente_telefono" value="{{ old('nuevo_cliente_telefono') }}" placeholder="Ej. 71234567" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none font-semibold">
                                    @error('nuevo_cliente_telefono') <span class="text-red-600 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="forma_pago" class="block text-xs font-bold text-finora-navy mb-1">Forma de Pago *</label>
                        <select id="forma_pago" name="forma_pago" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="CONTADO" {{ old('forma_pago', $preferenciaPago ?? 'CONTADO') === 'CONTADO' ? 'selected' : '' }}>CONTADO (Pago inmediato)</option>
                            <option value="CREDITO" {{ old('forma_pago', $preferenciaPago ?? 'CONTADO') === 'CREDITO' ? 'selected' : '' }}>CRÉDITO (Cuenta por cobrar)</option>
                        </select>
                    </div>
                    <div id="cuotas-container" class="hidden">
                        <label for="numero_cuotas" class="block text-xs font-bold text-finora-navy mb-1">Número de cuotas</label>
                        <select id="numero_cuotas" name="numero_cuotas" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            @foreach ([1, 2, 3, 4, 6, 12] as $cuotas)
                                <option value="{{ $cuotas }}" {{ (int) old('numero_cuotas', 1) === $cuotas ? 'selected' : '' }}>{{ $cuotas }} {{ $cuotas === 1 ? 'cuota' : 'cuotas' }}</option>
                            @endforeach
                        </select>
                        @error('numero_cuotas') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div id="metodo-pago-container">
                        <label for="metodo_pago" class="block text-xs font-bold text-finora-navy mb-1">Método de pago *</label>
                        <select id="metodo_pago" name="metodo_pago" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="EFECTIVO" {{ old('metodo_pago', 'EFECTIVO') === 'EFECTIVO' ? 'selected' : '' }}>Efectivo</option>
                            <option value="TRANSFERENCIA" {{ old('metodo_pago') === 'TRANSFERENCIA' ? 'selected' : '' }}>Transferencia bancaria</option>
                            <option value="QR" {{ old('metodo_pago') === 'QR' ? 'selected' : '' }}>Pago QR</option>
                            <option value="TARJETA" {{ old('metodo_pago') === 'TARJETA' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="OTRO" {{ old('metodo_pago') === 'OTRO' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div>
                        <label for="descuento" class="block text-xs font-bold text-finora-navy mb-1">Descuento Global (Bs)</label>
                        <input type="number" step="0.01" min="0" id="descuento" name="descuento" value="{{ old('descuento', '0.00') }}" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                    </div>
                </div>

                <div class="border-t border-b border-slate-100 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-sm text-finora-navy">Detalle de Ítems Vendidos</h3>
                        @if (! $pedido)
                            <button type="button" id="btn-add-item" class="px-3 py-1.5 bg-blue-50 text-finora-blue hover:bg-blue-100 rounded-xl text-xs font-bold inline-flex items-center gap-1 transition-colors">
                                <span class="material-symbols-outlined text-sm">add</span>
                                Agregar Producto
                            </button>
                        @endif
                    </div>

                    <div id="items-container" class="space-y-3">
                        @if ($pedido)
                            @foreach ($pedido->detalles as $idx => $det)
                                <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                                    <div class="sm:col-span-6">
                                        <input type="hidden" name="detalles[{{ $idx }}][producto_id]" value="{{ $det->producto_id }}">
                                         <span class="block text-xs font-bold text-finora-navy">{{ $det->producto->codigo }} - {{ $det->producto->nombre }}</span>
                                         <span class="mt-1 block text-xs font-semibold {{ $det->producto->stock_disponible >= $det->cantidad ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300' }}">Stock disponible: {{ $det->producto->stock_disponible }}</span>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-bold text-finora-subtle mb-1">Cantidad</label>
                                        <input type="number" min="1" name="detalles[{{ $idx }}][cantidad]" value="{{ $det->cantidad }}" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                    </div>
                                    <div class="sm:col-span-3">
                                         <label class="block text-[10px] font-bold text-finora-subtle mb-1">Precio Unitario (Bs)</label>
                                        <input type="number" step="0.01" min="0" name="detalles[{{ $idx }}][precio_unitario]" value="{{ $det->precio_acordado }}" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                                <div class="sm:col-span-6">
                                    <label class="block text-[10px] font-bold text-finora-subtle mb-1">Producto</label>
                                    <select name="detalles[0][producto_id]" required class="product-select w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                        <option value="">-- Seleccionar Producto --</option>
                                        @foreach ($productos as $prod)
                                      <option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta_actual }}" data-stock="{{ $prod->stock_disponible }}">{{ $prod->codigo }} - {{ $prod->nombre }} (Bs {{ number_format($prod->precio_venta_actual, 2, ',', '.') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-bold text-finora-subtle mb-1">Cantidad</label>
                                     <input type="number" min="1" name="detalles[0][cantidad]" value="1" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                     <span class="stock-hint text-xs font-semibold text-finora-subtle">Selecciona un producto para ver su stock.</span>
                                </div>
                                <div class="sm:col-span-3">
                                     <label class="block text-[10px] font-bold text-finora-subtle mb-1">Precio Unitario (Bs)</label>
                                    <input type="number" step="0.01" min="0" name="detalles[0][precio_unitario]" value="0.00" required class="precio-unitario-input w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Alerta interactiva de validación (2.9) -->
                <div id="form-validation-alert" class="hidden rounded-xl border border-amber-300 bg-amber-50 p-4 text-xs text-amber-900">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <span class="material-symbols-outlined text-amber-600 text-sm">warning</span>
                        <span>Por favor completa los siguientes campos obligatorios antes de continuar:</span>
                    </div>
                    <ul id="missing-fields-list" class="list-disc list-inside space-y-0.5 text-amber-800"></ul>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ $pedido ? route('panel.pedidos.show', $pedido) : route('panel.ventas.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="finora-gradient-btn px-5 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn">
                        Guardar Venta en Borrador
                    </button>
                </div>
            </form>
        </div>
    </main>

    @include('partials.panel-footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const alertBox = document.getElementById('form-validation-alert');
            const list = document.getElementById('missing-fields-list');
            const formaPago = document.getElementById('forma_pago');
            const cuotasContainer = document.getElementById('cuotas-container');
            const cuotasInput = document.getElementById('numero_cuotas');
            const metodoPagoContainer = document.getElementById('metodo-pago-container');
            const metodoPago = document.getElementById('metodo_pago');

            function toggleCuotas() {
                const esCredito = formaPago?.value === 'CREDITO';
                cuotasContainer?.classList.toggle('hidden', ! esCredito);
                metodoPagoContainer?.classList.toggle('hidden', esCredito);
                if (cuotasInput) {
                    if (esCredito) {
                        cuotasInput.setAttribute('required', 'required');
                        cuotasInput.required = true;
                    } else {
                        cuotasInput.removeAttribute('required');
                        cuotasInput.required = false;
                    }
                }
                if (metodoPago) {
                    if (!esCredito) {
                        metodoPago.setAttribute('required', 'required');
                        metodoPago.required = true;
                        if (!metodoPago.value) metodoPago.value = 'EFECTIVO';
                    } else {
                        metodoPago.removeAttribute('required');
                        metodoPago.required = false;
                        metodoPago.value = '';
                    }
                }
            }

            formaPago?.addEventListener('change', toggleCuotas);
            toggleCuotas();

            // Auto-asignación de precio unitario (3.1)
            function attachAutoPriceListener(row) {
                 const select = row.querySelector('.product-select');
                 const inputPrecio = row.querySelector('.precio-unitario-input');
                 const stockHint = row.querySelector('.stock-hint');
                 if (select && inputPrecio) {
                     select.addEventListener('change', () => {
                         const opt = select.options[select.selectedIndex];
                         const precio = opt ? opt.dataset.precio : 0;
                        if (precio) {
                             inputPrecio.value = parseFloat(precio).toFixed(2);
                         }
                         if (stockHint) {
                             const stock = Number(opt?.dataset.stock || 0);
                             stockHint.textContent = `Stock disponible: ${stock}`;
                             stockHint.classList.toggle('text-emerald-700', stock > 0);
                             stockHint.classList.toggle('text-red-700', stock <= 0);
                         }
                     });
                 }
            }

            const container = document.getElementById('items-container');
            if (container) {
                container.querySelectorAll('.item-row').forEach(row => attachAutoPriceListener(row));
            }

            @if (! $pedido)
                let itemIdx = 1;
                const btnAdd = document.getElementById('btn-add-item');
                 const productsOptions = `@foreach ($productos as $prod)<option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta_actual }}" data-stock="{{ $prod->stock_disponible }}">{{ $prod->codigo }} - {{ $prod->nombre }} (Bs {{ number_format($prod->precio_venta_actual, 2, ',', '.') }})</option>@endforeach`;

                if (btnAdd) {
                    btnAdd.addEventListener('click', () => {
                        const div = document.createElement('div');
                        div.className = 'item-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200 relative';
                        div.innerHTML = `
                            <div class="sm:col-span-5">
                                <label class="block text-[10px] font-bold text-finora-subtle mb-1">Producto</label>
                                     <select name="detalles[${itemIdx}][producto_id]" required class="product-select w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                    <option value="">-- Seleccionar Producto --</option>
                                    ${productsOptions}
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold text-finora-subtle mb-1">Cantidad</label>
                                 <input type="number" min="1" name="detalles[${itemIdx}][cantidad]" value="1" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                 <span class="stock-hint text-xs font-semibold text-finora-subtle">Selecciona un producto para ver su stock.</span>
                            </div>
                            <div class="sm:col-span-3">
                                 <label class="block text-[10px] font-bold text-finora-subtle mb-1">Precio Unitario (Bs)</label>
                                <input type="number" step="0.01" min="0" name="detalles[${itemIdx}][precio_unitario]" value="0.00" required class="precio-unitario-input w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                            </div>
                            <div class="sm:col-span-1 text-right pt-3 sm:pt-0">
                                <button type="button" class="btn-remove-row text-red-500 hover:text-red-700 p-1" title="Eliminar fila">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        `;
                        container.appendChild(div);
                        attachAutoPriceListener(div);
                        itemIdx++;

                        div.querySelector('.btn-remove-row').addEventListener('click', () => {
                            div.remove();
                        });
                    });
                }
            @endif

            // Alternar entre cliente existente y registro rápido de cliente
            const btnToggleCliente = document.getElementById('btn-toggle-nuevo-cliente');
            const contClienteExistente = document.getElementById('contenedor-cliente-existente');
            const contClienteNuevo = document.getElementById('contenedor-cliente-nuevo');
            const inputNuevoNombre = document.getElementById('nuevo_cliente_nombre');
            const inputNuevoTelefono = document.getElementById('nuevo_cliente_telefono');
            const iconoToggleCliente = document.getElementById('icono-toggle-cliente');
            const textoToggleCliente = document.getElementById('texto-toggle-cliente');
            let modoNuevoCliente = false;

            if (btnToggleCliente && contClienteExistente && contClienteNuevo) {
                btnToggleCliente.addEventListener('click', () => {
                    modoNuevoCliente = !modoNuevoCliente;
                    if (modoNuevoCliente) {
                        contClienteExistente.classList.add('hidden');
                        contClienteNuevo.classList.remove('hidden');
                        if (selectCliente) {
                            selectCliente.removeAttribute('required');
                            selectCliente.value = '';
                            if (avisoCredito) {
                                avisoCredito.className = 'hidden';
                                avisoCredito.innerHTML = '';
                            }
                        }
                        if (inputNuevoNombre) inputNuevoNombre.setAttribute('required', 'required');
                        if (inputNuevoTelefono) inputNuevoTelefono.setAttribute('required', 'required');
                        if (iconoToggleCliente) iconoToggleCliente.textContent = 'list';
                        if (textoToggleCliente) textoToggleCliente.textContent = '← Elegir de clientes existentes';
                        inputNuevoNombre?.focus();
                    } else {
                        contClienteNuevo.classList.add('hidden');
                        contClienteExistente.classList.remove('hidden');
                        if (selectCliente) selectCliente.setAttribute('required', 'required');
                        if (inputNuevoNombre) {
                            inputNuevoNombre.removeAttribute('required');
                            inputNuevoNombre.value = '';
                        }
                        if (inputNuevoTelefono) {
                            inputNuevoTelefono.removeAttribute('required');
                            inputNuevoTelefono.value = '';
                        }
                        if (iconoToggleCliente) iconoToggleCliente.textContent = 'person_add';
                        if (textoToggleCliente) textoToggleCliente.textContent = '+ Registrar Cliente Nuevo (WhatsApp / Rápido)';
                    }
                });
            }

            // Alerta de crédito de cliente
            const selectCliente = document.getElementById('cliente_id');
            const avisoCredito = document.getElementById('aviso-cliente-credito');
            if (selectCliente && avisoCredito) {
                const actualizarAvisoCredito = () => {
                    const selected = selectCliente.selectedOptions[0];
                    if (!selected || !selected.value) {
                        avisoCredito.className = 'hidden';
                        avisoCredito.innerHTML = '';
                        return;
                    }
                    const estado = selected.getAttribute('data-estado');
                    const deuda = selected.getAttribute('data-deuda');
                    const vencidas = parseInt(selected.getAttribute('data-vencidas') || '0', 10);

                    if (estado === 'MOROSO') {
                        avisoCredito.className = 'mt-2 p-3 rounded-xl border border-red-300 bg-red-50 text-red-800 text-xs font-semibold flex items-center gap-2';
                        avisoCredito.innerHTML = `<span class="material-symbols-outlined text-red-600 text-base">warning</span> <span><strong>Atención:</strong> Este cliente tiene <strong>${vencidas} cuota(s) vencida(s)</strong> por cobrar (Deuda: Bs ${deuda}). Se recomienda cobrar al contado.</span>`;
                    } else if (estado === 'CON_DEUDA') {
                        avisoCredito.className = 'mt-2 p-3 rounded-xl border border-amber-300 bg-amber-50 text-amber-800 text-xs font-semibold flex items-center gap-2';
                        avisoCredito.innerHTML = `<span class="material-symbols-outlined text-amber-600 text-base">info</span> <span>Este cliente mantiene un saldo pendiente de <strong>Bs ${deuda}</strong> en cuotas no vencidas.</span>`;
                    } else {
                        avisoCredito.className = 'mt-2 p-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-medium flex items-center gap-2';
                        avisoCredito.innerHTML = `<span class="material-symbols-outlined text-emerald-600 text-base">check_circle</span> <span>Cliente con historial al día, sin deudas pendientes.</span>`;
                    }
                };
                selectCliente.addEventListener('change', actualizarAvisoCredito);
                actualizarAvisoCredito();
            }

            // Validación interactiva de campos requeridos (2.9)
            if (form && alertBox && list) {
                form.addEventListener('submit', (e) => {
                    const requiredInputs = form.querySelectorAll('[required]');
                    const missing = [];

                    requiredInputs.forEach(input => {
                        // Ignorar campos ocultos o que estén dentro de contenedores ocultos
                        if (input.type === 'hidden' || input.offsetParent === null || input.closest('.hidden')) {
                            return;
                        }

                        const val = input.value ? input.value.trim() : '';
                        if (!val) {
                            const label = form.querySelector(`label[for="${input.id}"]`) || input.closest('div')?.querySelector('label');
                            const fieldName = label ? label.textContent.replace('*', '').trim() : (input.name || 'Campo');
                            missing.push(fieldName);
                            input.classList.add('ring-2', 'ring-amber-500', 'border-amber-500');
                        } else {
                            input.classList.remove('ring-2', 'ring-amber-500', 'border-amber-500');
                        }
                    });

                    if (missing.length > 0) {
                        e.preventDefault();
                        list.innerHTML = '';
                        missing.forEach(name => {
                            const li = document.createElement('li');
                            li.textContent = name;
                            list.appendChild(li);
                        });
                        alertBox.classList.remove('hidden');
                        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        alertBox.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
