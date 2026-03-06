<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Acceso') — {{ \App\Models\Setting::get('institution_acronym', config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex">

    {{-- ── PANEL IZQUIERDO – Branding institucional ──────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative bg-primary-950 flex-col items-center justify-center overflow-hidden">

        {{-- Fondo decorativo --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-10">
            <svg viewBox="0 0 600 600" class="absolute -top-32 -left-32 w-[500px] h-[500px] text-primary-400" fill="currentColor">
                <circle cx="300" cy="300" r="300"/>
            </svg>
            <svg viewBox="0 0 400 400" class="absolute -bottom-20 -right-20 w-[350px] h-[350px] text-primary-500" fill="currentColor">
                <circle cx="200" cy="200" r="200"/>
            </svg>
            <svg viewBox="0 0 200 200" class="absolute top-1/3 right-1/4 w-[150px] h-[150px] text-accent-500" fill="currentColor">
                <circle cx="100" cy="100" r="100"/>
            </svg>
        </div>

        {{-- Líneas decorativas --}}
        <div class="absolute inset-0 pointer-events-none" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 50px, rgba(255,255,255,0.02) 50px, rgba(255,255,255,0.02) 51px), repeating-linear-gradient(90deg, transparent, transparent 50px, rgba(255,255,255,0.02) 50px, rgba(255,255,255,0.02) 51px);"></div>

        {{-- Contenido branding --}}
        <div class="relative z-10 text-center px-10 max-w-lg">
            {{-- Escudo/Logo --}}
            <div class="mx-auto mb-8 w-28 h-28 bg-white/10 backdrop-blur-sm rounded-full border-4 border-white/20 flex items-center justify-center shadow-2xl">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 14l6.16-3.422A12.083 12.083 0 0121 21H3a12.083 12.083 0 012.84-10.422L12 14z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 14V2"/>
                </svg>
            </div>

            <h1 class="text-3xl font-extrabold text-white leading-tight">
                {{ \App\Models\Setting::get('institution_name', 'Sistema Académico') }}
            </h1>

            @php $subtitle = \App\Models\Setting::get('institution_subtitle', 'Posgrado') @endphp
            @if($subtitle)
            <p class="text-primary-300 text-lg font-medium mt-2">{{ $subtitle }}</p>
            @endif

            <div class="mt-8 h-px bg-gradient-to-r from-transparent via-primary-400 to-transparent"></div>

            <p class="mt-6 text-primary-300 text-sm leading-relaxed">
                Campus Virtual — Sistema de Gestión Académica Integrada
            </p>

            {{-- Features --}}
            <div class="mt-8 grid grid-cols-3 gap-4 text-center">
                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                    <svg class="w-7 h-7 text-accent-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-primary-300 text-xs mt-1.5">Cursos</p>
                </div>
                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                    <svg class="w-7 h-7 text-accent-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-primary-300 text-xs mt-1.5">Matrículas</p>
                </div>
                <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                    <svg class="w-7 h-7 text-accent-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-primary-300 text-xs mt-1.5">Intranet</p>
                </div>
            </div>

            {{-- Año académico --}}
            <div class="mt-6 inline-flex items-center gap-2 bg-primary-800/50 rounded-full px-4 py-2 border border-primary-700/50">
                <span class="w-2 h-2 rounded-full bg-accent-400 animate-pulse"></span>
                <span class="text-primary-300 text-xs font-medium">Año Académico {{ date('Y') }}</span>
            </div>
        </div>

        <p class="absolute bottom-5 text-primary-600 text-xs">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }} — Todos los derechos reservados
        </p>
    </div>

    {{-- ── PANEL DERECHO – Formulario ────────────────────────────────────── --}}
    <div class="w-full lg:w-1/2 xl:w-2/5 flex flex-col items-center justify-center px-6 py-10 bg-white min-h-screen">

        {{-- Móvil: solo logo --}}
        <div class="lg:hidden text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-600 mb-3 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422A12.083 12.083 0 0121 21H3a12.083 12.083 0 012.84-10.422L12 14z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">{{ \App\Models\Setting::get('institution_name', config('app.name')) }}</h2>
            <p class="text-gray-500 text-sm">{{ \App\Models\Setting::get('institution_subtitle', '') }}</p>
        </div>

        {{-- Formulario --}}
        <div class="w-full max-w-sm">
            @yield('content')
        </div>

        <p class="mt-8 text-xs text-gray-400 text-center lg:hidden">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }}
        </p>
    </div>

</body>
</html>
