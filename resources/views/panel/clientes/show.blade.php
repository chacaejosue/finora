<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora | Detalle de Cliente | Panel del Consultor</title>
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
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Ficha de Cliente</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.clientes.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Clientes</span>
                </a>
            </div>
        </div>
    </header>
        @include('partials.panel-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold text-finora-navy">
                        {{ $cliente->persona->nombre }} {{ $cliente->persona->apellido }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $cliente->activo ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-700' }}">
                        {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                    @if ($cliente->estado_crediticio === 'MOROSO')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                            Moroso ({{ $cliente->cuotas_vencidas_count }} cuotas vencidas)
                        </span>
                    @elseif ($cliente->estado_crediticio === 'CON_DEUDA')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Crédito en curso
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Historial al día
                        </span>
                    @endif
                </div>
                <p class="text-xs text-finora-subtle mt-1">
                    Registrado el {{ $cliente->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="text-right">
                    <span class="text-xs text-finora-subtle font-medium block">Deuda Pendiente</span>
                    <span class="text-xl font-heading font-extrabold text-finora-navy">
                        @money($totalDeuda)
                    </span>
                </div>
                <a href="{{ route('panel.clientes.edit', $cliente) }}" class="px-3.5 py-2 bg-finora-navy text-white text-xs font-semibold rounded-xl hover:bg-finora-dark transition-colors inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-3">
                <h3 class="font-heading font-bold text-sm text-finora-navy border-b border-slate-100 pb-2">Información de Contacto</h3>
                <div>
                    <span class="text-xs text-finora-subtle block">Teléfono:</span>
                    <span class="text-xs font-bold text-finora-navy">{{ $cliente->persona->telefono ?? 'Sin teléfono' }}</span>
                </div>
                <div>
                    <span class="text-xs text-finora-subtle block">Email:</span>
                    <span class="text-xs font-bold text-finora-navy">{{ $cliente->persona->email ?? 'Sin correo' }}</span>
                </div>
                <div>
                    <span class="text-xs text-finora-subtle block">Dirección:</span>
                    <span class="text-xs font-bold text-finora-navy">{{ $cliente->persona->direccion ?? 'Sin dirección' }}</span>
                </div>
                <div>
                    <span class="text-xs text-finora-subtle block">Observaciones:</span>
                    <span class="text-xs text-slate-700 italic">{{ $cliente->observaciones ?? 'Ninguna' }}</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-3">
                <h3 class="font-heading font-bold text-sm text-finora-navy border-b border-slate-100 pb-2">Acceso del cliente</h3>
                @if ($cliente->persona->usuario)
                    <p class="text-xs text-emerald-700 font-semibold">Este cliente ya puede iniciar sesión en su portal.</p>
                    <span class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2 py-1 text-[10px] font-bold text-emerald-800">{{ $cliente->persona->usuario->activo ? 'Acceso activo' : 'Acceso inactivo' }}</span>
                @else
                    <p class="text-xs text-finora-subtle">Crea una cuenta opcional para que consulte sus pedidos y compras.</p>
                    @if ($errors->has('acceso'))
                        <p class="text-xs text-red-600">{{ $errors->first('acceso') }}</p>
                    @endif
                    <form action="{{ route('panel.clientes.acceso', $cliente) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="email" name="email" value="{{ old('email', $cliente->persona->email) }}" required placeholder="Correo de acceso" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-finora-blue">
                        <input type="password" name="password" required minlength="6" placeholder="Contraseña inicial" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-finora-blue">
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Confirmar contraseña" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-finora-blue">
                        <button type="submit" class="w-full rounded-xl bg-finora-navy text-white px-3 py-2 text-xs font-bold hover:bg-finora-dark">Crear acceso</button>
                    </form>
                @endif
            </div>

            <div class="md:col-span-2 space-y-6">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                    <h3 class="font-heading font-bold text-sm text-finora-navy mb-3">Historial de Ventas</h3>
                    @if ($cliente->ventas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs divide-y divide-slate-100">
                                <thead class="bg-[#F8FAFC] text-finora-subtle font-bold uppercase">
                                    <tr>
                                        <th class="py-2 px-3">ID Venta</th>
                                        <th class="py-2 px-3">Fecha</th>
                                        <th class="py-2 px-3">Forma Pago</th>
                                        <th class="py-2 px-3">Estado</th>
                                        <th class="py-2 px-3 text-right">Total</th>
                                        <th class="py-2 px-3 text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($cliente->ventas as $v)
                                        <tr>
                                            <td class="py-2 px-3 font-bold">#{{ $v->id }}</td>
                                            <td class="py-2 px-3 text-slate-600">{{ $v->fecha->format('d/m/Y') }}</td>
                                            <td class="py-2 px-3"><span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-[10px]">{{ $v->forma_pago }}</span></td>
                                            <td class="py-2 px-3"><span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[10px]">{{ $v->estado }}</span></td>
                                            <td class="py-2 px-3 text-right font-bold">@money($v->total)</td>
                                            <td class="py-2 px-3 text-right font-bold text-red-600">@money($v->saldo_pendiente)</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-finora-subtle py-4 text-center">Este cliente aún no registra ventas.</p>
                    @endif
                </div>
            </div>
        </div>

    </main>

    @include('partials.panel-footer')
</body>
</html>
