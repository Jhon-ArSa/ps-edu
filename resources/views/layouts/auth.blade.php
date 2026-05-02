<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Acceso') — {{ \App\Models\Setting::get('institution_acronym', config('app.name')) }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo/logo-educacion.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/logo-educacion.png') }}">

    {{-- SEO / Open Graph --}}
    <meta name="description" content="{{ \App\Models\Setting::get('institution_name', config('app.name')) }} — Plataforma de gestión académica y aula virtual">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ \App\Models\Setting::get('institution_name', config('app.name')) }}">
    <meta property="og:description" content="Plataforma integral para la gestión académica, aula virtual y comunicación institucional">
    <meta property="og:image" content="{{ asset('logo/logo-educacion.png') }}">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ \App\Models\Setting::get('institution_name', config('app.name')) }}">
    <meta name="twitter:image" content="{{ asset('logo/logo-educacion.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Base ──────────────────────────────────────────────── */
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-custom { font-family: 'JetBrains Mono', monospace; }

        /* ── Glass card ────────────────────────────────────────── */
        .glass-panel {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px) saturate(150%);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }

        /* ── Orb blur ──────────────────────────────────────────── */
        .orb { filter: blur(120px); pointer-events: none; opacity: 0.4; }

        /* ── Float ─────────────────────────────────────────────── */
        @keyframes float-y {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
        .float-anim { animation: float-y 4s ease-in-out infinite; }

        /* ── Status dot ────────────────────────────────────────── */
        .status-dot { 
            background: #2563eb; 
            box-shadow: 0 0 8px rgba(37,99,235,0.6);
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* ── Material icons size fix ───────────────────────────── */
        .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 24; }

        /* ── Auth inputs (dark theme) ──────────────────────────── */
        .auth-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(226,232,240,0.9);
            margin-bottom: 0.375rem;
            letter-spacing: 0.01em;
        }
        .auth-input {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(148,163,184,0.25);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #f1f5f9;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .auth-input::placeholder {
            color: rgba(148,163,184,0.4);
        }
        .auth-input:focus {
            border-color: rgba(59,130,246,0.6);
            background: rgba(59,130,246,0.08);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .auth-input.error {
            border-color: rgba(239,68,68,0.6);
            background: rgba(239,68,68,0.06);
        }
        .auth-input:focus.error {
            box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
        }
        .auth-icon {
            color: rgba(148,163,184,0.6);
            transition: color 0.2s;
        }
        .auth-input:focus ~ .auth-icon,
        .auth-input-wrap:focus-within .auth-icon {
            color: rgba(96,165,250,0.9);
        }

        /* ── Responsive ────────────────────────────────────────── */
        @media (max-width: 640px) {
            .auth-input {
                padding: 0.625rem 0.875rem;
                font-size: 16px; /* Evita zoom en iOS */
            }
            .auth-label {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body class="min-h-screen flex antialiased overflow-hidden" style="background:#0f172a; color:#e2e8f0;">

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- FONDO GLOBAL – orbs sutiles                                --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="absolute inset-0 pointer-events-none overflow-hidden" style="z-index:0;">
    {{-- Orbs sutiles --}}
    <div class="orb absolute" style="top:-10%;right:-5%;width:480px;height:480px;border-radius:50%;background:rgba(59,130,246,0.15);"></div>
    <div class="orb absolute" style="bottom:-12%;left:-8%;width:560px;height:560px;border-radius:50%;background:rgba(37,99,235,0.12);"></div>
    
    {{-- Grid sutil --}}
    <div class="absolute inset-0 opacity-[0.02]"
         style="background-image:linear-gradient(rgba(148,163,184,0.3) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,0.3) 1px,transparent 1px);background-size:64px 64px;"></div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- HEADER STRIP                                               --}}
{{-- ═══════════════════════════════════════════════════════════ --}}

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- LAYOUT PRINCIPAL                                           --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="relative z-10 flex w-full min-h-screen">

    {{-- ── PANEL IZQUIERDO – Branding ──────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative flex-col items-center justify-center overflow-hidden">

        {{-- Imagen de fondo --}}
        <img src="{{ asset('logo/posgrado-uncp.jpg') }}"
             alt="Posgrado UNCP"
             class="absolute inset-0 w-full h-full object-cover"
             style="opacity:0.25;">

        {{-- Overlays --}}
        <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(15,23,42,0.92) 0%,rgba(30,41,59,0.88) 50%,rgba(15,23,42,0.95) 100%);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to right, rgba(15,23,42,0.3) 0%, transparent 60%);"></div>

        {{-- Contenido branding --}}
        <div class="relative z-10 text-center px-12 max-w-xl">

            {{-- Logo --}}
            <div class="mb-8">
                <div class="relative inline-block float-anim">
                    <div class="absolute inset-0 rounded-full blur-2xl" style="background:rgba(59,130,246,0.15);transform:scale(1.4);"></div>
                    <img src="{{ asset('logo/logo-educacion.png') }}"
                         alt="{{ \App\Models\Setting::get('institution_name', config('app.name')) }}"
                         class="relative mx-auto object-contain"
                         style="width:120px;height:120px;filter:drop-shadow(0 4px 12px rgba(0,0,0,0.3));">
                </div>
            </div>

            {{-- Nombre institución --}}
            <h1 style="font-size:1.875rem;font-weight:700;color:#f8fafc;line-height:1.3;letter-spacing:-0.01em;">
                {{ \App\Models\Setting::get('institution_name', 'Sistema Académico') }}
            </h1>

            @php $subtitle = \App\Models\Setting::get('institution_subtitle', 'Posgrado') @endphp
            @if($subtitle)
            <p class="mt-2 text-sm font-medium" style="color:#94a3b8;letter-spacing:0.05em;">
                {{ $subtitle }}
            </p>
            @endif

            {{-- Separador --}}
            <div class="mt-6 mx-auto" style="height:1px;background:linear-gradient(to right,transparent,rgba(148,163,184,0.3),transparent);max-width:280px;"></div>

            <p class="mt-5 text-sm leading-relaxed mx-auto" style="color:rgba(203,213,225,0.75);max-width:340px;">
                Plataforma integral para la gestión académica, aula virtual y comunicación institucional
            </p>

            {{-- Feature cards --}}
            <div class="mt-7 grid grid-cols-3 gap-3">
                @foreach([
                    ['icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','label'=>'Aula Virtual'],
                    ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','label'=>'Gestión'],
                    ['icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9','label'=>'Intranet'],
                ] as $feat)
                <div class="group relative transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="background:rgba(255,255,255,0.05);border:1px solid rgba(148,163,184,0.15);border-radius:10px;padding:14px 8px;backdrop-filter:blur(8px);"
                     onmouseover="this.style.background='rgba(59,130,246,0.08)';this.style.borderColor='rgba(96,165,250,0.3)';"
                     onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='rgba(148,163,184,0.15)';">
                    <div class="mx-auto mb-2 flex items-center justify-center" style="width:36px;height:36px;background:rgba(59,130,246,0.12);border-radius:8px;">
                        <svg style="width:18px;height:18px;color:#60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $feat['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="text-center text-xs font-medium" style="color:rgba(203,213,225,0.85);">{{ $feat['label'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Badge año --}}
            <div class="mt-7 inline-flex items-center gap-2 mx-auto"
                 style="background:rgba(59,130,246,0.08);border:1px solid rgba(96,165,250,0.2);border-radius:999px;padding:7px 18px;backdrop-filter:blur(8px);">
                <span class="status-dot" style="width:6px;height:6px;border-radius:50%;display:inline-block;"></span>
                <span class="text-xs font-medium" style="color:rgba(226,232,240,0.85);">
                    Año Académico {{ date('Y') }}
                </span>
            </div>
        </div>

        {{-- Footer izquierdo --}}
        <p class="absolute bottom-6 text-xs" style="color:rgba(148,163,184,0.5);z-index:10;">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }} — Todos los derechos reservados
        </p>
    </div>

    {{-- ── PANEL DERECHO – Formulario ───────────────────────── --}}
    <div class="w-full lg:w-1/2 xl:w-2/5 flex flex-col items-center justify-center px-6 sm:px-8 py-12 relative"
         style="background:rgba(15,23,42,0.7);backdrop-filter:blur(4px);">

        {{-- Línea decorativa izquierda (solo desktop) --}}
        <div class="hidden lg:block absolute left-0 top-0 bottom-0" style="width:1px;background:linear-gradient(to bottom,transparent,rgba(148,163,184,0.2),transparent);"></div>

        {{-- Móvil: logo --}}
        <div class="lg:hidden text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4"
                 style="width:68px;height:68px;background:rgba(59,130,246,0.08);border:1px solid rgba(96,165,250,0.2);border-radius:14px;">
                <img src="{{ asset('logo/logo-educacion.png') }}"
                     alt="{{ \App\Models\Setting::get('institution_name', config('app.name')) }}"
                     style="width:48px;height:48px;object-fit:contain;">
            </div>
            <h2 class="text-base font-bold" style="color:#f1f5f9;">{{ \App\Models\Setting::get('institution_name', config('app.name')) }}</h2>
            <p class="mt-1 text-xs font-medium" style="color:#94a3b8;">{{ \App\Models\Setting::get('institution_subtitle', '') }}</p>
        </div>

        {{-- Glass card del formulario --}}
        <div class="w-full relative"
             style="max-width:420px;background:rgba(255,255,255,0.06);border:1px solid rgba(148,163,184,0.15);border-radius:14px;padding:36px 32px;box-shadow:0 4px 24px rgba(0,0,0,0.12);backdrop-filter:blur(20px);">

            {{-- Encabezado del form (cada vista lo define) --}}
            @yield('form-header')

            {{-- Formulario --}}
            @yield('content')

            {{-- Footer del card --}}
            <div class="mt-7 pt-5 flex flex-wrap justify-center gap-4 sm:gap-6" style="border-top:1px solid rgba(148,163,184,0.1);">
                <div class="flex items-center gap-1.5 text-xs" style="color:rgba(148,163,184,0.7);">
                    <span class="material-symbols-outlined" style="font-size:16px;">shield_lock</span>
                    <span class="font-medium">Cifrado 256-bit</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs" style="color:rgba(148,163,184,0.7);">
                    <span class="material-symbols-outlined" style="font-size:16px;">verified_user</span>
                    <span class="font-medium">Acceso Seguro</span>
                </div>
            </div>
        </div>

        {{-- Copyright móvil --}}
        <p class="mt-8 lg:hidden text-center text-xs" style="color:rgba(148,163,184,0.5);">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }}
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- FOOTER STRIP                                               --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<footer class="absolute bottom-0 left-0 right-0 z-30 hidden md:flex flex-col md:flex-row items-center justify-between px-6 py-3.5 gap-3"
        style="background:rgba(15,23,42,0.85);backdrop-filter:blur(12px);border-top:1px solid rgba(148,163,184,0.1);">
    <p class="text-xs" style="color:rgba(148,163,184,0.6);">
        © {{ date('Y') }} {{ \App\Models\Setting::get('institution_name', 'Plataforma Académica') }} · Sistema Integrado de Gestión
    </p>
    <nav class="flex gap-5">
        @foreach(['Soporte','Privacidad','Términos'] as $lnk)
        <a href="#" class="text-xs transition-colors duration-200"
           style="color:rgba(148,163,184,0.6);"
           onmouseover="this.style.color='#60a5fa'"
           onmouseout="this.style.color='rgba(148,163,184,0.6)'">{{ $lnk }}</a>
        @endforeach
    </nav>
</footer>

</body>
</html>
