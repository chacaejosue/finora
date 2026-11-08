<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Editar Usuario | Panel del Consultor</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy flex flex-col min-h-screen">

    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 backdrop-blur-md bg-white/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="openPanelDrawerBtn" type="button" class="p-2 rounded-xl text-slate-600 hover:text-finora-navy hover:bg-slate-100 transition-colors cursor-pointer" title="Navegación de módulos">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('panel.index') }}">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Editar Usuario</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('panel.usuarios.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-finora-blue hover:text-finora-deep-blue">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Volver a usuarios
                </a>
            </div>
        </div>
    </header>
    @include('partials.panel-nav')

    <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
            <h1 class="font-heading text-xl font-extrabold text-finora-navy mb-6">Editar Datos de Usuario</h1>

            <form action="{{ route('panel.usuarios.update', $usuario) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-xs font-bold text-finora-navy mb-1">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->persona->nombre) }}" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @error('nombre') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="apellido" class="block text-xs font-bold text-finora-navy mb-1">Apellido</label>
                        <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $usuario->persona->apellido) }}" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @error('apellido') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold text-finora-navy mb-1">Correo Electrónico (Login) *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $usuario->persona->email) }}" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @error('email') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="telefono" class="block text-xs font-bold text-finora-navy mb-1">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $usuario->persona->telefono) }}" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @error('telefono') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="rol" class="block text-xs font-bold text-finora-navy mb-1">Rol de Usuario *</label>
                        <select id="rol" name="rol" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="CONSULTOR" {{ old('rol', $usuario->rol) === 'CONSULTOR' ? 'selected' : '' }}>CONSULTOR</option>
                            <option value="COLABORADOR" {{ old('rol', $usuario->rol) === 'COLABORADOR' ? 'selected' : '' }}>COLABORADOR</option>
                            <option value="CLIENTE" {{ old('rol', $usuario->rol) === 'CLIENTE' ? 'selected' : '' }}>CLIENTE</option>
                        </select>
                        @error('rol') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="genero" class="block text-xs font-bold text-finora-navy mb-1">Género / Trato</label>
                        <select id="genero" name="genero" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                            <option value="MASCULINO" {{ old('genero', $usuario->persona->genero) === 'MASCULINO' ? 'selected' : '' }}>Masculino (Bienvenido)</option>
                            <option value="FEMENINO" {{ old('genero', $usuario->persona->genero) === 'FEMENINO' ? 'selected' : '' }}>Femenino (Bienvenida)</option>
                            <option value="OTRO" {{ old('genero', $usuario->persona->genero) === 'OTRO' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('genero') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-bold text-finora-navy mb-1">Nueva Contraseña (Opcional)</label>
                        <input type="password" id="password" name="password" placeholder="Dejar en blanco para no modificar" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-finora-navy focus:bg-white focus:ring-2 focus:ring-finora-blue outline-none">
                        @error('password') <span class="text-red-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('panel.usuarios.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="finora-gradient-btn px-5 py-2 rounded-xl text-white font-heading font-semibold text-xs shadow-finora-btn">
                        Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
