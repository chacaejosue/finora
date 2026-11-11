<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Nuevo Comprobante Contable | Módulo Contable</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Nuevo Comprobante</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('contabilidad.libro-diario') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Volver al Libro Diario</span>
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (isset($errors) && $errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 text-sm font-medium">
                <div class="flex items-center gap-2 mb-2 font-bold">
                    <span class="material-symbols-outlined text-rose-600">error</span>
                    <span>No se pudo registrar el asiento:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6">
            <h1 class="text-2xl font-heading font-extrabold text-finora-navy tracking-tight">Registrar Asiento Contable Manual</h1>
            <p class="text-xs text-slate-500 mt-0.5">Permite asentar pagos de gastos operativos, alquileres, servicios básicos, seguros o reclasificaciones con verificación en vivo de Partida Doble.</p>
        </div>

        <form action="{{ route('contabilidad.asientos.store') }}" method="POST" id="formAsiento" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Cabecera del Comprobante -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tipo de Comprobante <span class="text-rose-500">*</span></label>
                    <select name="tipo_comprobante_id" required class="w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue">
                        @foreach($tiposComprobante as $tc)
                            <option value="{{ $tc->id }}" {{ $tc->codigo === 'TRS' ? 'selected' : '' }}>
                                {{ $tc->codigo }} - {{ $tc->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Fecha Contable <span class="text-rose-500">*</span></label>
                    <input type="date" name="fecha" value="{{ now()->toDateString() }}" required class="w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Correlativo</label>
                    <input type="text" disabled value="Auto-generado según normativa" class="w-full text-xs text-slate-400 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Glosa General de la Operación <span class="text-rose-500">*</span></label>
                <textarea name="glosa" rows="2" required placeholder="Ej. Pago de factura de energía eléctrica y servicios básicos de la oficina central correspondiente al mes de octubre." class="w-full text-xs font-medium px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-finora-blue">{{ old('glosa') }}</textarea>
            </div>

            <!-- Detalle de Partidas Dinámicas -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-finora-navy">Partidas Contables (Debe y Haber)</span>
                    <button type="button" onclick="agregarFila()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-finora-blue hover:bg-blue-100 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-sm">add</span>
                        <span>Agregar Fila</span>
                    </button>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="w-full text-xs text-left" id="tablaDetalles">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                <th class="py-2.5 px-3 w-5/12">Cuenta Contable</th>
                                <th class="py-2.5 px-3 w-3/12">Glosa Específica</th>
                                <th class="py-2.5 px-3 w-2/12 text-right">Debe (Bs)</th>
                                <th class="py-2.5 px-3 w-2/12 text-right">Haber (Bs)</th>
                                <th class="py-2.5 px-2 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="filasCuerpo" class="divide-y divide-slate-100">
                            <!-- Filas dinámicas generadas en JS -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50/90 font-mono font-bold text-xs border-t border-slate-200">
                                <td colspan="2" class="py-3 px-4 text-right font-sans uppercase tracking-wider text-[11px] text-slate-600">
                                    Sumas Iguales:
                                </td>
                                <td class="py-3 px-3 text-right text-finora-navy">
                                    <span id="txtTotalDebe">0.00</span>
                                </td>
                                <td class="py-3 px-3 text-right text-finora-navy">
                                    <span id="txtTotalHaber">0.00</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Barra de Estado de Cuadre de Partida Doble -->
                <div id="bannerCuadre" class="mt-4 p-4 rounded-2xl border transition-all flex items-center justify-between text-xs font-bold">
                    <div class="flex items-center gap-2" id="txtMensajeCuadre">
                        <!-- JS inserta estado de cuadre -->
                    </div>
                    <div id="txtDiferencia" class="font-mono">
                        Diferencia: Bs 0.00
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('contabilidad.libro-diario') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="btnGuardar" disabled class="px-6 py-2.5 rounded-xl text-xs font-bold bg-slate-300 text-slate-500 cursor-not-allowed transition-all">
                    Guardar Comprobante
                </button>
            </div>
        </form>

    </main>

    @include('partials.panel-footer')

    <!-- JSON con Cuentas Imputables para el selector JS -->
    <script>
        const cuentasDisponibles = @json($cuentas->map(fn($c) => ['id' => $c->id, 'codigo' => $c->codigo, 'nombre' => $c->nombre]));
        let filaIndex = 0;

        function crearFilaHtml(index, preId = '', preDebe = 0, preHaber = 0) {
            let options = cuentasDisponibles.map(c => 
                `<option value="${c.id}" ${c.id == preId ? 'selected' : ''}>${c.codigo} - ${c.nombre}</option>`
            ).join('');

            return `
                <tr id="fila_${index}" class="hover:bg-slate-50/50">
                    <td class="p-2">
                        <select name="detalles[${index}][cuenta_contable_id]" required class="w-full text-xs font-medium px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-finora-blue">
                            ${options}
                        </select>
                    </td>
                    <td class="p-2">
                        <input type="text" name="detalles[${index}][glosa_detalle]" placeholder="Opcional..." class="w-full text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                    </td>
                    <td class="p-2">
                        <input type="number" step="0.01" min="0" name="detalles[${index}][debe]" value="${preDebe > 0 ? preDebe : ''}" placeholder="0.00" oninput="actualizarCuadre()" class="input-debe w-full text-xs font-mono text-right px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                    </td>
                    <td class="p-2">
                        <input type="number" step="0.01" min="0" name="detalles[${index}][haber]" value="${preHaber > 0 ? preHaber : ''}" placeholder="0.00" oninput="actualizarCuadre()" class="input-haber w-full text-xs font-mono text-right px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-finora-blue"/>
                    </td>
                    <td class="p-2 text-center">
                        <button type="button" onclick="eliminarFila(${index})" class="text-slate-400 hover:text-rose-600 transition-colors cursor-pointer" title="Eliminar fila">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </td>
                </tr>
            `;
        }

        function agregarFila(preId = '', preDebe = 0, preHaber = 0) {
            const cuerpo = document.getElementById('filasCuerpo');
            cuerpo.insertAdjacentHTML('beforeend', crearFilaHtml(filaIndex++, preId, preDebe, preHaber));
            actualizarCuadre();
        }

        function eliminarFila(index) {
            const filas = document.querySelectorAll('#filasCuerpo tr');
            if (filas.length <= 2) {
                alert('Un asiento contable requiere al menos 2 partidas para cumplir con la Partida Doble.');
                return;
            }
            const fila = document.getElementById(`fila_${index}`);
            if (fila) fila.remove();
            actualizarCuadre();
        }

        function actualizarCuadre() {
            let totalDebe = 0;
            let totalHaber = 0;

            document.querySelectorAll('.input-debe').forEach(inp => {
                totalDebe += parseFloat(inp.value) || 0;
            });

            document.querySelectorAll('.input-haber').forEach(inp => {
                totalHaber += parseFloat(inp.value) || 0;
            });

            totalDebe = Math.round(totalDebe * 100) / 100;
            totalHaber = Math.round(totalHaber * 100) / 100;

            const diff = Math.round(Math.abs(totalDebe - totalHaber) * 100) / 100;
            const cuadrado = (diff === 0 && totalDebe > 0);

            document.getElementById('txtTotalDebe').innerText = totalDebe.toFixed(2);
            document.getElementById('txtTotalHaber').innerText = totalHaber.toFixed(2);
            document.getElementById('txtDiferencia').innerText = `Diferencia: Bs ${diff.toFixed(2)}`;

            const banner = document.getElementById('bannerCuadre');
            const mensaje = document.getElementById('txtMensajeCuadre');
            const btn = document.getElementById('btnGuardar');

            if (cuadrado) {
                banner.className = 'mt-4 p-4 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 transition-all flex items-center justify-between text-xs font-bold';
                mensaje.innerHTML = '<span class="material-symbols-outlined text-emerald-600 text-lg">verified</span><span>PARTIDA DOBLE CUADRADA: El Debe y el Haber coinciden al centavo.</span>';
                btn.disabled = false;
                btn.className = 'px-6 py-2.5 rounded-xl text-xs font-bold bg-finora-blue text-white hover:bg-blue-700 shadow-md transition-all cursor-pointer';
            } else {
                banner.className = 'mt-4 p-4 rounded-2xl border border-amber-200 bg-amber-50 text-amber-900 transition-all flex items-center justify-between text-xs font-bold';
                mensaje.innerHTML = '<span class="material-symbols-outlined text-amber-600 text-lg">warning</span><span>ASIENTO DESCUADRADO: Los débitos y créditos deben ser exactamente iguales para poder guardar.</span>';
                btn.disabled = true;
                btn.className = 'px-6 py-2.5 rounded-xl text-xs font-bold bg-slate-200 text-slate-400 cursor-not-allowed transition-all';
            }
        }

        // Inicializar con 2 filas básicas al cargar
        document.addEventListener('DOMContentLoaded', () => {
            agregarFila();
            agregarFila();
        });
    </script>
</body>
</html>
