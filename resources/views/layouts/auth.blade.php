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
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(40px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.10);
            box-shadow: 0 0 60px rgba(34,211,238,0.08), inset 0 1px 0 rgba(255,255,255,0.06);
        }
        .glass-form {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.08);
        }

        /* ── Cyan glow ─────────────────────────────────────────── */
        .cyan-glow { filter: drop-shadow(0 0 18px rgba(34,211,238,0.55)); }
        .text-cyan { color: #22d3ee; }
        .border-cyan-custom { border-color: rgba(34,211,238,0.4); }
        .bg-cyan-subtle { background: rgba(34,211,238,0.08); }

        /* ── Orb blur ──────────────────────────────────────────── */
        .orb { filter: blur(90px); pointer-events: none; }

        /* ── Particles ─────────────────────────────────────────── */
        @keyframes particle-rise {
            0%   { transform: translateY(100vh) translateX(0);    opacity: 0; }
            15%  { opacity: 0.6; }
            85%  { opacity: 0.3; }
            100% { transform: translateY(-5vh)  translateX(30px); opacity: 0; }
        }
        .particle {
            position: absolute;
            background: #22d3ee;
            border-radius: 50%;
            pointer-events: none;
            animation: particle-rise linear infinite;
        }

        /* ── Background zoom ───────────────────────────────────── */
        @keyframes subtle-zoom {
            0%   { transform: scale(1); }
            100% { transform: scale(1.12); }
        }
        .bg-zoom { animation: subtle-zoom 28s ease-in-out infinite alternate; }

        /* ── Float ─────────────────────────────────────────────── */
        @keyframes float-y {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-14px); }
        }
        .float-anim { animation: float-y 6s ease-in-out infinite; }

        /* ── Drift orbs ────────────────────────────────────────── */
        @keyframes drift {
            0%   { transform: translateX(-8%) translateY(0)    scale(1);    }
            50%  { transform: translateX(4%)  translateY(-4%)  scale(1.08); }
            100% { transform: translateX(-8%) translateY(0)    scale(1);    }
        }
        .drift-anim { animation: drift 22s ease-in-out infinite; }

        /* ── Shimmer button ────────────────────────────────────── */
        @keyframes shimmer-move {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }
        .btn-shimmer {
            background: linear-gradient(90deg,
                rgba(34,211,238,0) 0%,
                rgba(34,211,238,0.18) 50%,
                rgba(34,211,238,0) 100%);
            background-size: 200% 100%;
            animation: shimmer-move 3s infinite linear;
        }

        /* ── Corner accents ────────────────────────────────────── */
        .corner-tl::before {
            content:''; position:absolute; top:0; left:0;
            width:24px; height:24px;
            border-top:1px solid rgba(34,211,238,0.5);
            border-left:1px solid rgba(34,211,238,0.5);
        }
        .corner-br::after {
            content:''; position:absolute; bottom:0; right:0;
            width:24px; height:24px;
            border-bottom:1px solid rgba(34,211,238,0.5);
            border-right:1px solid rgba(34,211,238,0.5);
        }

        /* ── Status dot ────────────────────────────────────────── */
        .status-dot { background:#22d3ee; box-shadow: 0 0 8px #22d3ee; }

        /* ── Scan line hover ───────────────────────────────────── */
        .scan-btn { position:relative; overflow:hidden; }
        .scan-btn .scan-line {
            position:absolute; inset:0;
            background: linear-gradient(90deg, transparent 0%, rgba(34,211,238,0.15) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.9s ease;
        }
        .scan-btn:hover .scan-line { transform: translateX(100%); }

        /* ── Material icons size fix ───────────────────────────── */
        .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 24; }
    </style>
</head>

<body class="min-h-screen flex antialiased overflow-hidden" style="background:#010f2a; color:#d8e2ff;">

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- FONDO GLOBAL – orbs + partículas compartidas               --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="absolute inset-0 pointer-events-none overflow-hidden" style="z-index:0;">
    {{-- Orbs --}}
    <div class="orb drift-anim absolute" style="top:-12%;right:-8%;width:520px;height:520px;border-radius:50%;background:rgba(59,130,246,0.18);"></div>
    <div class="orb absolute"           style="bottom:-14%;left:-10%;width:620px;height:620px;border-radius:50%;background:rgba(34,211,238,0.12);animation:drift 26s ease-in-out infinite reverse;"></div>
    <div class="orb absolute"           style="top:40%;left:38%;width:320px;height:320px;border-radius:50%;background:rgba(99,102,241,0.10);animation:drift 18s ease-in-out infinite;"></div>

    {{-- Partículas --}}
    <div class="particle" style="width:2px;height:2px;left:8%;animation-duration:9s;animation-delay:0s;"></div>
    <div class="particle" style="width:1px;height:1px;left:20%;animation-duration:13s;animation-delay:2s;"></div>
    <div class="particle" style="width:2px;height:2px;left:35%;animation-duration:11s;animation-delay:4s;"></div>
    <div class="particle" style="width:1px;height:1px;left:55%;animation-duration:16s;animation-delay:1s;"></div>
    <div class="particle" style="width:2px;height:2px;left:72%;animation-duration:10s;animation-delay:5s;"></div>
    <div class="particle" style="width:1px;height:1px;left:88%;animation-duration:12s;animation-delay:3s;"></div>
    <div class="particle" style="width:2px;height:2px;left:95%;animation-duration:8s;animation-delay:7s;"></div>

    {{-- Grid sutil --}}
    <div class="absolute inset-0 opacity-[0.03]"
         style="background-image:linear-gradient(rgba(34,211,238,0.5) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,0.5) 1px,transparent 1px);background-size:48px 48px;"></div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- HEADER STRIP                                               --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<header class="absolute top-0 left-0 right-0 z-30 flex items-center justify-between px-8 py-5"
        style="border-bottom:1px solid rgba(34,211,238,0.10);">
    <div class="flex items-center gap-3">
        <div style="width:32px;height:32px;border:1px solid rgba(34,211,238,0.35);display:flex;align-items:center;justify-content:center;border-radius:4px;">
            <span class="material-symbols-outlined text-cyan" style="font-size:18px;">school</span>
        </div>
        <span class="font-mono-custom text-xs tracking-[0.25em] uppercase" style="color:rgba(216,226,255,0.7);">
            {{ \App\Models\Setting::get('institution_acronym', 'UNCP') }}
        </span>
    </div>
    <div class="hidden md:flex items-center gap-6 font-mono-custom" style="font-size:10px;letter-spacing:0.15em;color:rgba(197,198,205,0.6);">
        <div class="flex items-center gap-2">
            <span class="status-dot" style="width:6px;height:6px;border-radius:50%;display:inline-block;animation:pulse 2s ease-in-out infinite;"></span>
            SISTEMA ACTIVO
        </div>
        <div class="hidden lg:block">PROTOCOLO SEGURO · TLS 1.3</div>
        <div class="hidden lg:block">AÑO ACADÉMICO {{ date('Y') }}</div>
    </div>
</header>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- LAYOUT PRINCIPAL                                           --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="relative z-10 flex w-full min-h-screen">

    {{-- ── PANEL IZQUIERDO – Branding ──────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative flex-col items-center justify-center overflow-hidden">

        {{-- Imagen de fondo con zoom --}}
        <img src="{{ asset('logo/posgrado-uncp.jpg') }}"
             alt="Posgrado UNCP"
             class="bg-zoom absolute inset-0 w-full h-full object-cover"
             style="opacity:0.35;">

        {{-- Overlays --}}
        <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(1,15,42,0.88) 0%,rgba(1,20,55,0.82) 50%,rgba(1,12,38,0.92) 100%);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to right, rgba(1,15,42,0.2) 0%, transparent 60%);"></div>

        {{-- Contenido branding --}}
        <div class="relative z-10 text-center px-12 max-w-xl">

            {{-- Logo con glow --}}
            <div class="mb-10">
                <div class="relative inline-block float-anim">
                    <div class="absolute inset-0 rounded-full blur-3xl" style="background:rgba(34,211,238,0.18);transform:scale(1.6);"></div>
                    <div class="absolute inset-0 rounded-full blur-xl"  style="background:rgba(255,255,255,0.12);transform:scale(1.3);"></div>
                    <img src="{{ asset('logo/logo-educacion.png') }}"
                         alt="{{ \App\Models\Setting::get('institution_name', config('app.name')) }}"
                         class="relative mx-auto object-contain cyan-glow"
                         style="width:140px;height:140px;transition:transform .5s;cursor:default;"
                         onmouseover="this.style.transform='scale(1.08)'"
                         onmouseout="this.style.transform='scale(1)'">
                </div>
            </div>

            {{-- Nombre institución --}}
            <h1 style="font-size:2.1rem;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-0.01em;text-shadow:0 4px 32px rgba(0,0,0,0.5);">
                {{ \App\Models\Setting::get('institution_name', 'Sistema Académico') }}
            </h1>

            @php $subtitle = \App\Models\Setting::get('institution_subtitle', 'Posgrado') @endphp
            @if($subtitle)
            <p class="font-mono-custom mt-3 uppercase tracking-widest" style="font-size:11px;color:#22d3ee;letter-spacing:0.3em;">
                ── {{ $subtitle }} ──
            </p>
            @endif

            {{-- Separador --}}
            <div class="mt-7 mx-auto" style="height:1px;background:linear-gradient(to right,transparent,rgba(34,211,238,0.4),transparent);max-width:320px;"></div>

            <p class="mt-5 text-sm leading-relaxed mx-auto" style="color:rgba(216,226,255,0.6);max-width:320px;">
                Plataforma integral para la gestión académica, aula virtual y comunicación institucional
            </p>

            {{-- Feature cards --}}
            <div class="mt-8 grid grid-cols-3 gap-3">
                @foreach([
                    ['icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','label'=>'Aula Virtual'],
                    ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','label'=>'Gestión'],
                    ['icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9','label'=>'Intranet'],
                ] as $feat)
                <div class="group relative corner-tl corner-br transition-all duration-300 hover:-translate-y-1 cursor-default"
                     style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:16px 8px;backdrop-filter:blur(12px);"
                     onmouseover="this.style.background='rgba(34,211,238,0.07)';this.style.borderColor='rgba(34,211,238,0.25)';"
                     onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.borderColor='rgba(255,255,255,0.08)';">
                    <div class="mx-auto mb-2 flex items-center justify-center" style="width:38px;height:38px;background:rgba(34,211,238,0.1);border-radius:10px;">
                        <svg style="width:18px;height:18px;color:#22d3ee;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $feat['icon'] }}"/>
                        </svg>
                    </div>
                    <p class="font-mono-custom text-center" style="font-size:9px;letter-spacing:0.12em;color:rgba(197,198,205,0.75);text-transform:uppercase;">{{ $feat['label'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Badge año --}}
            <div class="mt-7 inline-flex items-center gap-2 mx-auto"
                 style="background:rgba(34,211,238,0.07);border:1px solid rgba(34,211,238,0.2);border-radius:999px;padding:8px 20px;backdrop-filter:blur(8px);">
                <span class="status-dot" style="width:7px;height:7px;border-radius:50%;display:inline-block;animation:pulse 2s ease-in-out infinite;"></span>
                <span class="font-mono-custom" style="font-size:10px;letter-spacing:0.18em;color:rgba(216,226,255,0.75);text-transform:uppercase;">
                    Año Académico {{ date('Y') }}
                </span>
            </div>
        </div>

        {{-- Footer izquierdo --}}
        <p class="absolute bottom-5 font-mono-custom" style="font-size:10px;color:rgba(216,226,255,0.25);letter-spacing:0.08em;z-index:10;">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }} — Todos los derechos reservados
        </p>
    </div>

    {{-- ── PANEL DERECHO – Formulario ───────────────────────── --}}
    <div class="w-full lg:w-1/2 xl:w-2/5 flex flex-col items-center justify-center px-6 py-24 relative"
         style="background:rgba(1,12,38,0.6);backdrop-filter:blur(2px);">

        {{-- Línea decorativa izquierda (solo desktop) --}}
        <div class="hidden lg:block absolute left-0 top-0 bottom-0" style="width:1px;background:linear-gradient(to bottom,transparent,rgba(34,211,238,0.3),transparent);"></div>

        {{-- Móvil: logo --}}
        <div class="lg:hidden text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4"
                 style="width:72px;height:72px;background:rgba(34,211,238,0.07);border:1px solid rgba(34,211,238,0.25);border-radius:16px;">
                <img src="{{ asset('logo/logo-educacion.png') }}"
                     alt="{{ \App\Models\Setting::get('institution_name', config('app.name')) }}"
                     style="width:52px;height:52px;object-fit:contain;">
            </div>
            <h2 class="text-base font-bold" style="color:#d8e2ff;">{{ \App\Models\Setting::get('institution_name', config('app.name')) }}</h2>
            <p class="font-mono-custom mt-1 uppercase" style="font-size:9px;letter-spacing:0.2em;color:#22d3ee;">{{ \App\Models\Setting::get('institution_subtitle', '') }}</p>
        </div>

        {{-- Glass card del formulario --}}
        <div class="w-full relative corner-tl corner-br"
             style="max-width:400px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.09);border-radius:16px;padding:40px 36px;box-shadow:0 0 60px rgba(34,211,238,0.07),inset 0 1px 0 rgba(255,255,255,0.05);">

            {{-- Icono flotante top-right --}}
            <div class="absolute float-anim" style="top:-20px;right:-18px;opacity:0.35;">
                <span class="material-symbols-outlined cyan-glow text-cyan" style="font-size:56px;">auto_stories</span>
            </div>

            {{-- Encabezado del form --}}
            <div class="mb-8 text-center">
                <p class="font-mono-custom mb-2 uppercase tracking-widest" style="font-size:10px;color:#22d3ee;letter-spacing:0.25em;">Portal de Acceso</p>
                <h2 style="font-size:1.6rem;font-weight:800;color:#d8e2ff;letter-spacing:-0.01em;">Iniciar Sesión</h2>
                <p class="mt-1 text-sm" style="color:rgba(197,198,205,0.6);">Ingresa tus credenciales institucionales</p>
            </div>

            {{-- Formulario --}}
            @yield('content')

            {{-- Footer del card --}}
            <div class="mt-8 pt-5 flex justify-center gap-6" style="border-top:1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-1.5 font-mono-custom uppercase" style="font-size:9px;letter-spacing:0.12em;color:rgba(143,144,151,0.7);">
                    <span class="material-symbols-outlined" style="font-size:14px;">shield_lock</span>
                    Cifrado 256-bit
                </div>
                <div class="flex items-center gap-1.5 font-mono-custom uppercase" style="font-size:9px;letter-spacing:0.12em;color:rgba(143,144,151,0.7);">
                    <span class="material-symbols-outlined" style="font-size:14px;">verified_user</span>
                    Acceso Seguro
                </div>
            </div>
        </div>

        {{-- Copyright móvil --}}
        <p class="mt-8 lg:hidden font-mono-custom text-center" style="font-size:10px;color:rgba(216,226,255,0.25);">
            © {{ date('Y') }} {{ \App\Models\Setting::get('institution_acronym', '') }}
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- FOOTER STRIP                                               --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<footer class="absolute bottom-0 left-0 right-0 z-30 flex flex-col md:flex-row items-center justify-between px-8 py-4 gap-3"
        style="background:rgba(0,8,24,0.75);backdrop-filter:blur(16px);border-top:1px solid rgba(34,211,238,0.1);">
    <p class="font-mono-custom" style="font-size:10px;letter-spacing:0.1em;color:rgba(100,116,139,0.7);text-transform:uppercase;">
        © {{ date('Y') }} {{ \App\Models\Setting::get('institution_name', 'Plataforma Académica') }} · Sistema Integrado de Gestión
    </p>
    <nav class="flex gap-6">
        @foreach(['Soporte','Privacidad','Términos'] as $lnk)
        <a href="#" class="font-mono-custom uppercase transition-colors duration-200"
           style="font-size:10px;letter-spacing:0.1em;color:rgba(100,116,139,0.6);"
           onmouseover="this.style.color='#22d3ee'"
           onmouseout="this.style.color='rgba(100,116,139,0.6)'">{{ $lnk }}</a>
        @endforeach
    </nav>
</footer>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- BADGE SERVIDOR (solo escritorio grande)                    --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="fixed hidden xl:block z-30"
     style="bottom:72px;right:32px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:14px 18px;backdrop-filter:blur(24px);box-shadow:0 0 30px rgba(34,211,238,0.06);">
    @foreach(['PROTOCOLO' => 'TLS 1.3', 'ESTADO' => 'EN LÍNEA', 'UPTIME' => '99.9 %'] as $k => $v)
    <div class="flex justify-between gap-8 font-mono-custom" style="font-size:10px;{{ !$loop->first ? 'margin-top:6px;' : '' }}">
        <span style="color:rgba(197,198,205,0.5);">{{ $k }}:</span>
        <span style="color:{{ $k==='ESTADO' ? '#22d3ee' : '#d8e2ff' }};">{{ $v }}</span>
    </div>
    @endforeach
</div>

</body>
</html>
