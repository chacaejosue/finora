<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Finora - Iniciar Sesión | Sistema de Gestión Comercial y Financiera</title>

    <!-- Favicon de Finora -->
    <link rel="icon" type="image/png" href="{{ asset('images/branding/finora-icono.png') }}"/>

    <!-- Tipografías de Google: Outfit & Manrope -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>

    <!-- Iconos Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    <!-- Carga de estilos y scripts del proyecto mediante Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page min-h-screen lg:h-full font-sans bg-finora-surface text-finora-navy antialiased selection:bg-finora-cyan selection:text-finora-navy overflow-x-hidden lg:overflow-hidden">
    <!-- Contenedor principal de pantalla dividida (Completamente responsivo en móvil y desktop) -->
    <div class="min-h-screen lg:h-screen w-full flex flex-col lg:flex-row overflow-x-hidden lg:overflow-hidden">

        <!-- Panel izquierdo: Formulario de acceso seguro -->
        <main class="w-full lg:w-[48%] xl:w-[45%] flex flex-col justify-between px-6 py-5 sm:px-12 md:px-14 lg:px-12 xl:px-16 bg-white z-10 min-h-screen lg:min-h-0 lg:h-full overflow-y-auto lg:overflow-hidden">

            <!-- Logotipo y marca principal -->
            <header class="w-full">
                <a class="inline-flex items-center gap-3.5 group" href="{{ route('landing') }}" title="Finora - Portal Principal">
                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/branding/finora-isotipo.png') }}" alt="Finora" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading text-xl font-extrabold tracking-tight text-finora-navy">Finora</span>
                        <span class="text-[10px] font-medium text-finora-subtle -mt-1 tracking-wide">Gestión comercial y financiera</span>
                    </div>
                </a>
            </header>

            <!-- Contenedor central del formulario con animación de entrada -->
            <section aria-labelledby="login-title" class="my-auto py-2 max-w-md w-full mx-auto animate-fade-in-up">
                <div class="mb-4 text-left">
                    <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy tracking-tight" id="login-title">
                        Bienvenido a Finora
                    </h1>
                    <p class="mt-1.5 text-sm text-finora-subtle font-medium">
                        Accede a Finora según tu perfil: cliente, colaborador o consultor.
                    </p>
                </div>

                <!-- Formulario de autenticación (2.5: manejado para evitar duplicar entradas en historial del navegador) -->
                <form id="loginForm" action="{{ route('login.authenticate') }}" class="space-y-4" method="POST" autocomplete="on" data-prevent-submit-feedback="true">
                    @csrf

                    <!-- Campo: Correo electrónico -->
                    <div>
                        <label class="block text-xs font-bold font-sans text-finora-navy mb-1.5" for="identity">
                            Correo electrónico
                        </label>
                        <div class="relative rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                    <input autocomplete="email" class="block w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 text-finora-navy focus:bg-white focus:outline-none focus:ring-2 focus:ring-finora-blue focus:border-transparent transition-all" id="identity" name="identity" placeholder="tu@email.com" required="" type="email" value=""/>
                        </div>
                    </div>

                    <!-- Campo: Contraseña con botón para alternar visibilidad -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold font-sans text-finora-navy" for="password">
                                Contraseña
                            </label>
                        </div>
                        <div class="relative rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </div>
                            <input autocomplete="current-password" class="block w-full pl-10 pr-11 py-2.5 bg-[#F8FAFC] border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 text-finora-navy focus:bg-white focus:outline-none focus:ring-2 focus:ring-finora-blue focus:border-transparent transition-all" id="password" name="password" placeholder="••••••••••••" required="" type="password"/>
                            <button aria-label="Mostrar u ocultar contraseña" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-finora-navy transition-colors focus:outline-none cursor-pointer" id="togglePassword" type="button">
                                <span class="material-symbols-outlined text-[20px]" id="eyeIcon">visibility</span>
                            </button>
                        </div>
                    </div>

                    {{-- Mensajes de error de autenticación --}}
                    <div id="loginErrorBox" class="{{ $errors->any() ? '' : 'hidden' }} rounded-xl border border-red-200 bg-red-50 p-3.5" role="alert">
                        <p id="loginErrorMessage" class="text-sm font-medium text-red-700">
                            {{ $errors->first() }}
                        </p>
                    </div>

                    <!-- Botón principal de acceso -->
                    <div class="pt-2">
                        <button id="loginSubmitBtn" class="finora-gradient-btn w-full py-3 px-6 rounded-xl text-white font-heading font-semibold text-base shadow-finora-btn flex items-center justify-center gap-2 cursor-pointer focus:outline-none focus:ring-4 focus:ring-cyan-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed" type="submit">
                            <span id="loginBtnText">Iniciar sesión</span>
                            <span class="material-symbols-outlined text-lg" id="loginBtnIcon">arrow_forward</span>
                        </button>
                    </div>
                </form>

                <!-- Enlace secundario: Volver al catálogo público (2.4: alineado con flex) -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-1.5 text-xs text-finora-subtle">
                    <span>¿Solo quieres explorar productos?</span>
                    <a class="font-bold text-finora-blue hover:text-finora-deep-blue transition-colors inline-flex items-center gap-1 group" href="{{ route('landing') }}">
                        <span class="material-symbols-outlined text-sm transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                        <span>Volver al catálogo</span>
                    </a>
                </div>
            </section>

            <!-- Pie del panel de acceso (2.2: texto unificado con panel derecho) -->
            <footer class="w-full pt-3 pb-3 border-t border-slate-100 flex items-center justify-center text-xs text-finora-subtle text-center">
                <span>Sistema de gestión comercial para consultores independientes</span>
            </footer>
        </main>

        <!-- Panel derecho: Identidad visual Finora y módulos de valor -->
        <aside aria-label="Identidad Finora" class="hidden lg:flex lg:w-[52%] xl:w-[55%] finora-soft-bg relative overflow-hidden flex-col justify-between p-12 xl:p-16 border-l border-slate-200/70">

            <!-- Emblema artístico translúcido de fondo -->
            <div aria-hidden="true" class="absolute -right-24 top-1/2 -translate-y-1/2 w-[760px] h-[760px] pointer-events-none select-none opacity-10">
                <svg class="w-full h-full" fill="none" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="bgArtGrad" x1="10%" x2="90%" y1="90%" y2="10%">
                            <stop offset="0%" stop-color="#0050CC"></stop>
                            <stop offset="60%" stop-color="#0062FF"></stop>
                            <stop offset="100%" stop-color="#00D2DF"></stop>
                        </linearGradient>
                        <linearGradient id="bgArtWing" x1="0%" x2="100%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#00E5C9"></stop>
                            <stop offset="100%" stop-color="#00A3FF"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M22 24C34 16 54 14 74 15C81 15.3 83 23 76 26.5C62 33 44 35 24 35C21 35 19 26 22 24Z" fill="url(#bgArtWing)"></path>
                    <path d="M20 28C22 28 24 30 24 34V68C24 75 19 75 19 68V32C19 29.5 20 28 20 28Z" fill="url(#bgArtGrad)"></path>
                    <path d="M24 42C38 38 52 38 68 42C74 43.5 73 50.5 67 52C53 55 37 56 24 57V74C24 81 16 80 16 73V35C16 31 19 28 24 28" fill="url(#bgArtGrad)"></path>
                </svg>
            </div>

            <!-- Orbes luminosos difuminados de fondo -->
            <div aria-hidden="true" class="absolute top-12 right-20 w-80 h-80 rounded-full bg-cyan-200/35 blur-3xl pointer-events-none"></div>
            <div aria-hidden="true" class="absolute bottom-16 left-12 w-96 h-96 rounded-full bg-blue-300/25 blur-3xl pointer-events-none"></div>

            <!-- Encabezado del panel derecho -->
            <div class="relative z-10 flex items-center justify-between w-full">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 backdrop-blur-md border border-white/70 shadow-sm text-xs font-semibold text-finora-navy">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Acceso a Finora
                </div>
                <div class="text-xs font-medium text-finora-subtle">
                    Sistema web de gestión
                </div>
            </div>

            <!-- Tarjeta central de acceso general -->
            <div class="relative z-10 my-auto py-10 max-w-lg mx-auto text-center lg:text-left w-full">
                <div class="bg-white/80 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-white shadow-finora-card">

                    <!-- Etiqueta decorativa superior -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50/80 border border-blue-100 text-finora-blue text-xs font-bold tracking-wide uppercase mb-5">
                        <span class="material-symbols-outlined text-sm">hub</span>
                         Acceso general
                    </div>

                    <!-- Mensaje y propuesta de valor -->
                    <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-finora-navy tracking-tight leading-snug">
                         Todo Finora empieza aquí.
                    </h2>
                    <p class="mt-3 text-sm sm:text-base text-finora-subtle leading-relaxed font-normal">
                         Ingresa con tu cuenta y continúa con las herramientas disponibles para tu perfil.
                    </p>

                    <!-- Cuadrícula con los 4 módulos conceptuales -->
                    <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 gap-3">

                        <!-- Módulo: Ventas -->
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/70 border border-slate-100 shadow-xs hover:bg-white hover:shadow-md hover:-translate-y-0.5 transition-all">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-finora-blue flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-lg">point_of_sale</span>
                            </div>
                            <span class="text-xs font-bold text-finora-navy">Según tu rol</span>
                        </div>

                        <!-- Módulo: Pedidos -->
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/70 border border-slate-100 shadow-xs hover:bg-white hover:shadow-md hover:-translate-y-0.5 transition-all">
                            <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-lg">inventory_2</span>
                            </div>
                            <span class="text-xs font-bold text-finora-navy">Pedidos</span>
                        </div>

                        <!-- Módulo: Clientes -->
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/70 border border-slate-100 shadow-xs hover:bg-white hover:shadow-md hover:-translate-y-0.5 transition-all">
                            <div class="w-9 h-9 rounded-xl bg-sky-50 text-finora-sky flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-lg">group</span>
                            </div>
                            <span class="text-xs font-bold text-finora-navy">Catálogo</span>
                        </div>

                        <!-- Módulo: Finanzas -->
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/70 border border-slate-100 shadow-xs hover:bg-white hover:shadow-md hover:-translate-y-0.5 transition-all">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-lg">account_balance</span>
                            </div>
                            <span class="text-xs font-bold text-finora-navy">Atención</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie de página del panel derecho -->
            <div class="relative z-10 flex items-center justify-center text-xs text-finora-subtle border-t border-slate-200/50 pt-4">
                <span>Sistema de gestión comercial para consultores independientes</span>
            </div>
        </aside>
    </div>

    {{-- La interacción de contraseña se maneja en resources/js/app.js --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 2.5: Guardar el origen previo para evitar bucles de navegación hacia atrás con credenciales inválidas
            const referrer = document.referrer;
            if (referrer && !referrer.includes('/login')) {
                sessionStorage.setItem('finora_login_origin', referrer);
            } else if (!sessionStorage.getItem('finora_login_origin')) {
                sessionStorage.setItem('finora_login_origin', "{{ route('landing') }}");
            }

            // Si se cargó con error HTTP normal, reemplazar la entrada en el historial
            if (window.history && window.history.replaceState) {
                window.history.replaceState({ page: 'login' }, document.title, window.location.href);
            }

            // Interceptar envío para no recargar ni ensuciar el historial del navegador
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('loginSubmitBtn');
            const btnText = document.getElementById('loginBtnText');
            const btnIcon = document.getElementById('loginBtnIcon');
            const errorBox = document.getElementById('loginErrorBox');
            const errorMessage = document.getElementById('loginErrorMessage');

            if (form && submitBtn) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (errorBox) errorBox.classList.add('hidden');
                    submitBtn.disabled = true;
                    const originalText = btnText ? btnText.textContent : 'Iniciar sesión';
                    if (btnText) btnText.textContent = 'Verificando...';
                    if (btnIcon) {
                        btnIcon.textContent = 'sync';
                        btnIcon.classList.add('animate-spin');
                    }

                    try {
                        const formData = new FormData(form);
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.redirect) {
                            window.location.replace(data.redirect);
                        } else {
                            const msg = data.errors?.identity?.[0] || data.message || 'Las credenciales proporcionadas son incorrectas.';
                            if (errorMessage) errorMessage.textContent = msg;
                            if (errorBox) errorBox.classList.remove('hidden');
                            submitBtn.disabled = false;
                            if (btnText) btnText.textContent = originalText;
                            if (btnIcon) {
                                btnIcon.textContent = 'arrow_forward';
                                btnIcon.classList.remove('animate-spin');
                            }
                        }
                    } catch (err) {
                        // En caso de error inesperado, permitir fallback nativo
                        form.submit();
                    }
                });
            }
        });
    </script>
</body>
</html>
