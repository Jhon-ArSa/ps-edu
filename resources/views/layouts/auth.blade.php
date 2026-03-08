<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Acceso') — {{ \App\Models\Setting::get('institution_acronym', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white flex antialiased">

    {{-- ── PANEL IZQUIERDO – Branding institucional ──────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative bg-gradient-to-br from-primary-700 via-primary-800 to-primary-950 flex-col items-center justify-center overflow-hidden">

        {{-- Formas decorativas --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -right-32 w-[500px] h-[500px] bg-primary-400/8 rounded-full blur-3xl"></div>
            <div class="absolute top-1/4 right-1/4 w-48 h-48 bg-accent-500/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Patrón de puntos --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.03]"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px;"></div>

        {{-- Contenido --}}
        <div class="relative z-10 text-center px-10 max-w-lg animate-fade-in">
            {{-- Logo --}}
            <div class="mx-auto mb-8 w-24 h-24 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 flex items-center justify-center shadow-2xl shadow-black/20">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 14l6.16-3.422A12.083 12.083 0 0121 21H3a12.083 12.083 0 012.84-10.422L12 14z"/>
                </svg>
            </div>

            <h1 class="text-3xl font-extrabold text-white leading-tight tracking-tight">
                {{ \App\Models\Setting::get('institution_name', 'Sistema Académico') }}
            </h1>

            @php $subtitle = \App\Models\Setting::get('institution_subtitle', 'Posgrado') @endphp
            @if($subtitle)
            <p class="text-primary-200 text-lg font-medium mt-2">{{ $subtitle }}</p>
            @endif

            <div class="mt-8 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

            <p class="mt-6 text-primary-200/80 text-sm leading-relaxed max-w-sm mx-auto">
                Plataforma integral para la gestión académica, aula virtual y comunicación institucional
            </p>

            {{-- Features --}}
            <div class="mt-10 grid grid-cols-3 gap-3 text-center">
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10 hover:bg-white/10 transition-colors duration-300">
                    <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="text-white/70 text-xs font-medium">Aula Virtual</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10 hover:bg-white/10 transition-colors duration-300">
                    <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <p class="text-white/70 text-xs font-medium">Gestión</p>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 border border-white/10 hover:bg-white/10 transition-colors duration-300">
                    <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-white/70 text-xs font-medium">Intranet</p>
                </div>
            </div>

            {{-- Año académico --}}
            <div class="mt-8 inline-flex items-center gap-2 bg-white/5 backdrop-blur-sm rounded-full px-5 py-2.5 border border-white/10">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-white/70 text-xs font-medium">Año Académico {{ date('Y') }}</span>
            </div>
        </div>

        <p class="absolute bottom-5 text-white/30 text-xs">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }} — Todos los derechos reservados
        </p>
    </div>

    {{-- ── PANEL DERECHO – Formulario ────────────────────────────────────── --}}
    <div class="w-full lg:w-1/2 xl:w-2/5 flex flex-col items-center justify-center px-6 py-10 bg-white min-h-screen">

        {{-- Móvil: solo logo --}}
        <div class="lg:hidden text-center mb-8 animate-fade-in-up">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 mb-3 shadow-lg shadow-primary-500/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l6.16-3.422A12.083 12.083 0 0121 21H3a12.083 12.083 0 012.84-10.422L12 14z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">{{ \App\Models\Setting::get('institution_name', config('app.name')) }}</h2>
            <p class="text-gray-400 text-sm">{{ \App\Models\Setting::get('institution_subtitle', '') }}</p>
        </div>

        {{-- Formulario --}}
        <div class="w-full max-w-sm animate-fade-in-up">
            @yield('content')
        </div>

        <p class="mt-8 text-xs text-gray-400 text-center lg:hidden">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }}
        </p>
    </div>

</body>
</html>
