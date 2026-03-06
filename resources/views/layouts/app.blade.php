<!DOCTYPE html>
<html lang="es" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') — {{ \App\Models\Setting::get('institution_acronym', config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-100 font-sans">

{{-- ── SIDEBAR ─────────────────────────────────────────────────────────────── --}}
<aside
    class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-900 flex flex-col transition-transform duration-300 md:translate-x-0 shadow-xl"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
>
    {{-- Brand / Logo --}}
    <div class="flex items-center gap-3 px-4 py-4 border-b border-primary-800/60">
        <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0">
            {{-- Escudo/Logo institucional SVG --}}
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7L12 2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 6v6m0 0l-3-3m3 3l3-3"/>
            </svg>
        </div>
        <div class="overflow-hidden flex-1">
            <p class="text-white font-bold text-sm leading-tight truncate">
                {{ \App\Models\Setting::get('institution_name', config('app.name')) }}
            </p>
            <p class="text-primary-300 text-[10px] truncate">
                {{ \App\Models\Setting::get('institution_subtitle', 'Posgrado') }}
            </p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        @php $role = auth()->user()->role; @endphp

        @if($role === 'admin')
            <x-sidebar-link route="admin.dashboard" icon="home">Dashboard</x-sidebar-link>

            <p class="px-3 pt-4 pb-1.5 text-primary-400 text-[10px] font-bold uppercase tracking-widest">Gestión Académica</p>
            <x-sidebar-link route="admin.users.index" icon="users">Usuarios</x-sidebar-link>
            <x-sidebar-link route="admin.courses.index" icon="book">Cursos</x-sidebar-link>
            <x-sidebar-link route="admin.enrollments.index" icon="clipboard">Matrículas</x-sidebar-link>
            <x-sidebar-link route="admin.announcements.index" icon="bell">Comunicados</x-sidebar-link>

            <p class="px-3 pt-4 pb-1.5 text-primary-400 text-[10px] font-bold uppercase tracking-widest">Sistema</p>
            <x-sidebar-link route="admin.settings" icon="cog">Configuración</x-sidebar-link>

        @elseif($role === 'docente')
            <x-sidebar-link route="docente.dashboard" icon="home">Inicio</x-sidebar-link>

            <p class="px-3 pt-4 pb-1.5 text-primary-400 text-[10px] font-bold uppercase tracking-widest">Aula Virtual</p>
            <x-sidebar-link route="docente.courses.index" icon="book">Mis Cursos</x-sidebar-link>
            <x-sidebar-link route="docente.intranet" icon="newspaper">Intranet</x-sidebar-link>

            <p class="px-3 pt-4 pb-1.5 text-primary-400 text-[10px] font-bold uppercase tracking-widest">Personal</p>
            <x-sidebar-link route="docente.escalafon.show" icon="id-card">Escalafón</x-sidebar-link>
            <x-sidebar-link route="docente.soporte" icon="support">Soporte Técnico</x-sidebar-link>

        @elseif($role === 'alumno')
            <x-sidebar-link route="alumno.dashboard" icon="home">Inicio</x-sidebar-link>
            <x-sidebar-link route="alumno.intranet" icon="newspaper">Intranet</x-sidebar-link>
        @endif
    </nav>

    {{-- User Profile Footer --}}
    <div class="border-t border-primary-800/60 p-3" x-data="{ open: false }">
        {{-- Role badge --}}
        <div class="px-3 mb-2">
            @php
                $roleBadge = match(auth()->user()->role) {
                    'admin'   => ['bg-red-500/20 text-red-300 border border-red-500/30', 'Administrador'],
                    'docente' => ['bg-violet-500/20 text-violet-300 border border-violet-500/30', 'Docente'],
                    default   => ['bg-blue-500/20 text-blue-300 border border-blue-500/30', 'Alumno'],
                };
            @endphp
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $roleBadge[0] }}">
                {{ $roleBadge[1] }}
            </span>
        </div>

        <button @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-primary-800 transition-colors group">
            <div class="w-9 h-9 rounded-xl bg-primary-700 overflow-hidden shrink-0 ring-2 ring-primary-600">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1 overflow-hidden text-left">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-primary-400 text-xs truncate">{{ auth()->user()->email }}</p>
            </div>
            <svg class="w-3.5 h-3.5 text-primary-400 transition-transform shrink-0"
                 :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
             class="mt-1.5 space-y-0.5">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-primary-300 hover:bg-primary-800 hover:text-white text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mi Perfil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-primary-300 hover:bg-red-900/50 hover:text-red-300 text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── MOBILE OVERLAY ───────────────────────────────────────────────────────── --}}
<div
    x-show="sidebarOpen"
    x-cloak
    x-transition:enter="transition-opacity duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/60 md:hidden"
></div>

{{-- ── MAIN CONTENT ─────────────────────────────────────────────────────────── --}}
<div class="md:pl-64 min-h-screen flex flex-col">

    {{-- Top Bar --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 h-14 flex items-center px-4 gap-3 shadow-sm">
        {{-- Mobile menu button --}}
        <button @click="sidebarOpen = true"
                class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Breadcrumb --}}
        <div class="flex-1 flex items-center gap-1.5 text-sm min-w-0">
            @hasSection('breadcrumb')
                <div class="flex items-center gap-1.5 text-gray-500 truncate">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    @yield('breadcrumb')
                </div>
            @else
                <span class="font-semibold text-gray-800">@yield('title')</span>
            @endif
        </div>

        {{-- Right side: date + user info --}}
        <div class="flex items-center gap-3 shrink-0">
            <span class="hidden sm:block text-xs text-gray-400 font-medium">
                {{ now()->isoFormat('D [de] MMMM, YYYY') }}
            </span>

            {{-- User chip --}}
            <div class="hidden sm:flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                <div class="w-6 h-6 rounded-full bg-primary-600 overflow-hidden shrink-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white text-[10px] font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <span class="text-xs font-semibold text-gray-700 max-w-24 truncate">
                    {{ explode(' ', auth()->user()->name)[0] }}
                </span>
            </div>
        </div>
    </header>

    {{-- Page Content --}}
    <main class="flex-1 p-4 sm:p-6">

        {{-- Flash messages --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm shadow-sm">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-3 px-6 text-center text-xs text-gray-400 border-t border-gray-200 bg-white">
        © {{ date('Y') }} {{ \App\Models\Setting::get('institution_name', config('app.name')) }} — Sistema de Gestión Académica de Posgrado
    </footer>
</div>

@stack('scripts')
</body>
</html>
