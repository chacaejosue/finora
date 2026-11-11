<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Crear Pedido | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Nuevo Pedido</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.pedidos.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Pedidos</span>
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
            <h1 class="font-heading text-xl font-extrabold text-finora-navy mb-6">Registrar Nuevo Pedido de Cliente</h1>

            <form action="{{ route('panel.pedidos.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Selección de cliente o nuevo cliente (2.11) -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-finora-navy">Tipo de Cliente *</label>
                        <div class="inline-flex rounded-lg border border-slate-200 bg-white p-0.5 text-xs font-semibold">
                            <button type="button" id="tab-cliente-existente" class="px-3 py-1 rounded-md bg-finora-blue text-white transition-all">Cliente Registrado</button>
                            <button type="button" id="tab-cliente-nuevo" class="px-3 py-1 rounded-md text-finora-subtle hover:text-finora-navy transition-all">Cliente Nuevo / No Registrado</button>
                        </div>
                    </div>

                    <!-- Opción A: Cliente existente -->
                    <div id="seccion-cliente-existente">
                        <label for="cliente_id" class="block text-[11px] font-bold text-finora-subtle mb-1">Seleccionar de la lista de clientes *</label>
                        <select id="cliente_id" name="cliente_id" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach ($clientes as $cli)
                                <option value="{{ $cli->id }}" {{ old('cliente_id') == $cli->id ? 'selected' : '' }}>{{ $cli->persona->nombre }} {{ $cli->persona->apellido }} ({{ $cli->persona->telefono ?? $cli->persona->email }})</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Opción B: Cliente nuevo on-the-fly -->
                    <div id="seccion-cliente-nuevo" class="hidden space-y-3 pt-2 border-t border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="nuevo_cliente_nombre" class="block text-[11px] font-bold text-finora-subtle mb-1">Nombre *</label>
                                <input type="text" id="nuevo_cliente_nombre" name="nuevo_cliente_nombre" value="{{ old('nuevo_cliente_nombre') }}" placeholder="Ej. Ana" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none">
                                @error('nuevo_cliente_nombre') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="nuevo_cliente_apellido" class="block text-[11px] font-bold text-finora-subtle mb-1">Apellido</label>
                                <input type="text" id="nuevo_cliente_apellido" name="nuevo_cliente_apellido" value="{{ old('nuevo_cliente_apellido') }}" placeholder="Ej. Silva" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none">
                            </div>
                            <div>
                                <label for="nuevo_cliente_telefono" class="block text-[11px] font-bold text-finora-subtle mb-1">Teléfono / WhatsApp</label>
                                <input type="text" id="nuevo_cliente_telefono" name="nuevo_cliente_telefono" value="{{ old('nuevo_cliente_telefono') }}" placeholder="Ej. 76045028" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-finora-navy focus:ring-2 focus:ring-finora-blue outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-b border-slate-100 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-sm text-finora-navy">Productos Solicitados</h3>
                        <button type="button" id="btn-add-item" class="px-3 py-1.5 bg-blue-50 text-finora-blue hover:bg-blue-100 rounded-xl text-xs font-bold inline-flex items-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Agregar Producto
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <div class="sm:col-span-6">
                                <label class="block text-[10px] font-bold text-finora-subtle mb-1">Producto (2.13: Con Precio)</label>
                                <select name="detalles[0][producto_id]" required class="product-select w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                                    <option value="">-- Seleccionar Producto --</option>
                                    @foreach ($productos as $prod)
                                        <option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta_actual }}">{{ $prod->codigo }} - {{ $prod->nombre }} - Bs {{ number_format($prod->precio_venta_actual, 2, ',', '.') }} (Stock: {{ $prod->stock_disponible }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold text-finora-subtle mb-1">Cantidad</label>
                                <input type="number" min="1" name="detalles[0][cantidad]" value="1" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold text-finora-subtle mb-1">Precio Acordado ($)</label>
                                <input type="number" step="0.01" min="0" name="detalles[0][precio_acordado]" value="0.00" required class="precio-input w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="observaciones" class="block text-xs font-bold text-finora-navy mb-1">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="2" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none" placeholder="Fecha deseada de entrega, notas especiales...">{{ old('observaciones') }}</textarea>
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
                    <a href="{{ route('panel.pedidos.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="finora-gradient-btn px-5 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn">
                        Guardar Pedido
                    </button>
                </div>
            </form>
        </div>
    </main>

    @include('partials.panel-footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let itemIdx = 1;
            const container = document.getElementById('items-container');
            const btnAdd = document.getElementById('btn-add-item');
            const form = document.querySelector('form');
            const alertBox = document.getElementById('form-validation-alert');
            const list = document.getElementById('missing-fields-list');

            // Tabs de Cliente
            const tabExistente = document.getElementById('tab-cliente-existente');
            const tabNuevo = document.getElementById('tab-cliente-nuevo');
            const secExistente = document.getElementById('seccion-cliente-existente');
            const secNuevo = document.getElementById('seccion-cliente-nuevo');
            const clienteSelect = document.getElementById('cliente_id');
            const nuevoClienteNombre = document.getElementById('nuevo_cliente_nombre');

            tabExistente.addEventListener('click', () => {
                tabExistente.className = 'px-3 py-1 rounded-md bg-finora-blue text-white transition-all';
                tabNuevo.className = 'px-3 py-1 rounded-md text-finora-subtle hover:text-finora-navy transition-all';
                secExistente.classList.remove('hidden');
                secNuevo.classList.add('hidden');
                clienteSelect.setAttribute('required', 'required');
                nuevoClienteNombre.removeAttribute('required');
                nuevoClienteNombre.value = '';
            });

            tabNuevo.addEventListener('click', () => {
                tabNuevo.className = 'px-3 py-1 rounded-md bg-finora-blue text-white transition-all';
                tabExistente.className = 'px-3 py-1 rounded-md text-finora-subtle hover:text-finora-navy transition-all';
                secNuevo.classList.remove('hidden');
                secExistente.classList.add('hidden');
                clienteSelect.removeAttribute('required');
                clienteSelect.value = '';
                nuevoClienteNombre.setAttribute('required', 'required');
            });

            // Auto-rellenar precio acordado al seleccionar producto (3.1 & 2.13)
            function attachProductChangeListener(row) {
                const select = row.querySelector('.product-select');
                const precioInput = row.querySelector('.precio-input');
                if (select && precioInput) {
                    select.addEventListener('change', () => {
                        const opt = select.options[select.selectedIndex];
                        const precio = opt ? opt.dataset.precio : 0;
                        if (precio) {
                            precioInput.value = parseFloat(precio).toFixed(2);
                        }
                    });
                }
            }

            container.querySelectorAll('.item-row').forEach(row => attachProductChangeListener(row));

            const productsOptions = `@foreach ($productos as $prod)<option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta_actual }}">{{ $prod->codigo }} - {{ $prod->nombre }} - Bs {{ number_format($prod->precio_venta_actual, 2, ',', '.') }} (Stock: {{ $prod->stock_disponible }})</option>@endforeach`;

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
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-finora-subtle mb-1">Precio Acordado ($)</label>
                        <input type="number" step="0.01" min="0" name="detalles[${itemIdx}][precio_acordado]" value="0.00" required class="precio-input w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-finora-navy outline-none">
                    </div>
                    <div class="sm:col-span-1 text-right pt-3 sm:pt-0">
                        <button type="button" class="btn-remove-row text-red-500 hover:text-red-700 p-1" title="Eliminar fila">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                `;
                container.appendChild(div);
                attachProductChangeListener(div);
                itemIdx++;

                div.querySelector('.btn-remove-row').addEventListener('click', () => {
                    div.remove();
                });
            });

            // Validación interactiva de campos vacíos (2.9)
            form.addEventListener('submit', (e) => {
                const requiredInputs = form.querySelectorAll('[required]');
                const missing = [];

                requiredInputs.forEach(input => {
                    // Ignorar si está en sección oculta
                    if (input.closest('.hidden')) return;

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
        });
    </script>
</body>
</html>
