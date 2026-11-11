<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Registrar Pago | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Registrar Pago</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.pagos.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-finora-blue hover:text-finora-deep-blue">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Volver a pagos
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
            <h1 class="font-heading text-xl font-extrabold text-finora-navy mb-6">Registrar Abono o Pago de Cliente</h1>

            <form action="{{ route('panel.pagos.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="venta_id" class="block text-xs font-bold text-finora-navy mb-1">Venta a Pagar *</label>
                    <select id="venta_id" name="venta_id" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        <option value="">-- Seleccionar Venta Pendiente --</option>
                        @foreach ($ventasConSaldo as $v)
                            <option value="{{ $v->id }}" {{ (old('venta_id', $ventaSeleccionada?->id) == $v->id) ? 'selected' : '' }}>
                                Venta #{{ $v->id }} &bull; Cliente: {{ $v->cliente->persona->nombre }} {{ $v->cliente->persona->apellido }} (Saldo: @money($v->saldo_pendiente))
                            </option>
                        @endforeach
                    </select>
                    @error('venta_id') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="monto" class="block text-xs font-bold text-finora-navy mb-1">Monto del Pago ($) *</label>
                        <input type="number" step="0.01" min="0.01" id="monto" name="monto" value="{{ old('monto', $ventaSeleccionada?->saldo_pendiente) }}" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @error('monto') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="metodo" class="block text-xs font-bold text-finora-navy mb-1">Método de Pago *</label>
                        <select id="metodo" name="metodo" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="EFECTIVO" {{ old('metodo') === 'EFECTIVO' ? 'selected' : '' }}>EFECTIVO</option>
                            <option value="TRANSFERENCIA" {{ old('metodo') === 'TRANSFERENCIA' ? 'selected' : '' }}>TRANSFERENCIA BANCARIA</option>
                            <option value="QR" {{ old('metodo') === 'QR' ? 'selected' : '' }}>PAGO QR</option>
                            <option value="TARJETA" {{ old('metodo') === 'TARJETA' ? 'selected' : '' }}>TARJETA DÉBITO / CRÉDITO</option>
                            <option value="OTRO" {{ old('metodo') === 'OTRO' ? 'selected' : '' }}>OTRO</option>
                        </select>
                        @error('metodo') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="observacion" class="block text-xs font-bold text-finora-navy mb-1">Observaciones / Comprobante</label>
                    <input type="text" id="observacion" name="observacion" value="{{ old('observacion') }}" placeholder="Nro de transferencia, recibo, etc." class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
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
                    <a href="{{ route('panel.pagos.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="finora-gradient-btn px-5 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn">
                        Guardar Pago
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const alertBox = document.getElementById('form-validation-alert');
            const list = document.getElementById('missing-fields-list');

            if (form && alertBox && list) {
                form.addEventListener('submit', (e) => {
                    const requiredInputs = form.querySelectorAll('[required]');
                    const missing = [];

                    requiredInputs.forEach(input => {
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
