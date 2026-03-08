<!DOCTYPE html>
<html lang="es" x-data="{
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    ready: false,
    toggleCollapse() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
    }
}" x-init="$nextTick(() => { setTimeout(() => ready = true, 50) })">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') — {{ \App\Models\Setting::get('institution_acronym', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50/80 font-sans antialiased">

{{-- ── SIDEBAR ─────────────────────────────────────────────────────────────── --}}
<aside
    class="fixed inset-y-0 left-0 z-50 sidebar-container flex flex-col md:translate-x-0 overflow-hidden"
    :class="[
        sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0',
        sidebarCollapsed ? 'md:w-[72px]' : 'md:w-[270px]',
        'w-[270px]',
        ready ? 'transition-all duration-300 ease-out' : ''
    ]"
>
    {{-- Brand / Logo --}}
    <div class="relative flex items-center" :class="sidebarCollapsed ? 'justify-center px-3 py-5' : 'px-5 py-5'">
        <div class="flex items-center gap-3" :class="sidebarCollapsed && 'md:justify-center'">
            {{-- Logo mark --}}
            <div class="sidebar-logo shrink-0">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z" fill="currentColor"/>
                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" fill="currentColor" opacity="0.6"/>
                </svg>
            </div>
            {{-- Institution text --}}
            <div class="overflow-hidden flex-1 whitespace-nowrap sidebar-hideable" :class="sidebarCollapsed && 'sidebar-hide'">
                <p class="text-gray-900 font-extrabold text-[13.5px] leading-tight truncate tracking-tight">
                    {{ \App\Models\Setting::get('institution_name', config('app.name')) }}
                </p>
                <p class="text-primary-400 text-[10.5px] font-semibold truncate mt-0.5 tracking-wide uppercase">
                    {{ \App\Models\Setting::get('institution_subtitle', 'Posgrado') }}
                </p>
            </div>
        </div>
        <div class="absolute bottom-0 left-4 right-4 h-px bg-gradient-to-r from-gray-200/80 via-gray-200/40 to-transparent"></div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden sidebar-scroll py-3 space-y-0.5" :class="sidebarCollapsed ? 'md:px-2 px-3' : 'px-3'">
        @php $role = auth()->user()->role; @endphp

        @if($role === 'admin')
            <x-sidebar-link route="admin.dashboard" icon="home">Dashboard</x-sidebar-link>

            <div class="sidebar-section-label sidebar-section-hideable" :class="sidebarCollapsed && 'sidebar-hide'">
                <span class="sidebar-section-text">Gestión Académica</span>
                <div class="sidebar-section-line"></div>
            </div>
            {{-- Collapsed: show a thin divider --}}
            <div class="sidebar-divider-hideable" :class="sidebarCollapsed && 'sidebar-show'">
                <div class="mx-auto my-3 w-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
            </div>
            <x-sidebar-link route="admin.users.index" icon="users">Usuarios</x-sidebar-link>
            <x-sidebar-link route="admin.courses.index" icon="book">Cursos</x-sidebar-link>
            <x-sidebar-link route="admin.enrollments.index" icon="clipboard">Matrículas</x-sidebar-link>
            <x-sidebar-link route="admin.announcements.index" icon="bell">Comunicados</x-sidebar-link>

            <div class="sidebar-section-label sidebar-section-hideable" :class="sidebarCollapsed && 'sidebar-hide'">
                <span class="sidebar-section-text">Sistema</span>
                <div class="sidebar-section-line"></div>
            </div>
            <div class="sidebar-divider-hideable" :class="sidebarCollapsed && 'sidebar-show'">
                <div class="mx-auto my-3 w-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
            </div>
            <x-sidebar-link route="admin.settings" icon="cog">Configuración</x-sidebar-link>

        @elseif($role === 'docente')
            <x-sidebar-link route="docente.dashboard" icon="home">Inicio</x-sidebar-link>

            <div class="sidebar-section-label sidebar-section-hideable" :class="sidebarCollapsed && 'sidebar-hide'">
                <span class="sidebar-section-text">Aula Virtual</span>
                <div class="sidebar-section-line"></div>
            </div>
            <div class="sidebar-divider-hideable" :class="sidebarCollapsed && 'sidebar-show'">
                <div class="mx-auto my-3 w-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
            </div>
            <x-sidebar-link route="docente.courses.index" icon="book">Mis Cursos</x-sidebar-link>
            <x-sidebar-link route="docente.intranet" icon="newspaper">Intranet</x-sidebar-link>

            <div class="sidebar-section-label sidebar-section-hideable" :class="sidebarCollapsed && 'sidebar-hide'">
                <span class="sidebar-section-text">Personal</span>
                <div class="sidebar-section-line"></div>
            </div>
            <div class="sidebar-divider-hideable" :class="sidebarCollapsed && 'sidebar-show'">
                <div class="mx-auto my-3 w-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
            </div>
            <x-sidebar-link route="docente.escalafon.show" icon="id-card">Escalafón</x-sidebar-link>
            <x-sidebar-link route="docente.soporte" icon="support">Soporte Técnico</x-sidebar-link>

        @elseif($role === 'alumno')
            <x-sidebar-link route="alumno.dashboard" icon="home">Inicio</x-sidebar-link>
            <x-sidebar-link route="alumno.intranet" icon="newspaper">Intranet</x-sidebar-link>
        @endif

        {{-- Cuenta --}}
        <div class="sidebar-section-label sidebar-section-hideable" :class="sidebarCollapsed && 'sidebar-hide'">
            <span class="sidebar-section-text">Cuenta</span>
            <div class="sidebar-section-line"></div>
        </div>
        <div class="sidebar-divider-hideable" :class="sidebarCollapsed && 'sidebar-show'">
            <div class="mx-auto my-3 w-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
        </div>
        <x-sidebar-link route="profile.edit" icon="user">Mi Perfil</x-sidebar-link>

        {{-- Cerrar sesión --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="sidebar-link sidebar-link-logout group w-full"
                    x-bind:title="sidebarCollapsed ? 'Cerrar sesión' : ''"
                    x-bind:class="sidebarCollapsed ? 'md:!justify-center md:!px-2 md:!gap-0 sidebar-link-collapsed' : ''">
                <div class="sidebar-link-icon">
                    <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                </div>
                <span class="flex-1 sidebar-link-text whitespace-nowrap overflow-hidden text-left sidebar-hideable" x-bind:class="sidebarCollapsed && 'sidebar-hide'">Cerrar sesión</span>
            </button>
        </form>
    </nav>


</aside>

{{-- ── SIDEBAR COLLAPSE BUTTON (floating on edge) ───────────────────────────── --}}
<button @click="toggleCollapse()"
        class="sidebar-collapse-btn hidden md:flex"
        :class="[
            sidebarCollapsed ? 'md:left-[60px]' : 'md:left-[258px]'
        ]"
        :title="sidebarCollapsed ? 'Expandir menú' : 'Colapsar menú'">
    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
        <path x-show="!sidebarCollapsed" d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path x-show="sidebarCollapsed" d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

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
    class="fixed inset-0 z-40 bg-gray-900/30 backdrop-blur-sm md:hidden"
></div>

{{-- ── MAIN CONTENT ─────────────────────────────────────────────────────────── --}}
<div class="min-h-screen flex flex-col" :class="[
    sidebarCollapsed ? 'md:pl-[72px]' : 'md:pl-[270px]',
    ready ? 'transition-all duration-300' : ''
]">

    {{-- Top Bar --}}
    <header class="topbar sticky top-0 z-30 h-16 flex items-center px-4 sm:px-6 gap-4">
        {{-- Mobile menu button --}}
        <button @click="sidebarOpen = true"
                class="md:hidden p-2.5 -ml-1 rounded-xl text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all duration-200 active:scale-95">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                <path d="M3 7h18M3 12h12M3 17h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        {{-- Breadcrumb / Title --}}
        <div class="flex-1 flex items-center gap-2 text-sm min-w-0">
            @hasSection('breadcrumb')
                <nav class="flex items-center gap-1.5 text-gray-400 truncate">
                    <div class="w-6 h-6 rounded-lg bg-primary-50 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-primary-500" viewBox="0 0 24 24" fill="none">
                            <path d="M3 9.5L12 4l9 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19 13v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    @yield('breadcrumb')
                </nav>
            @else
                <h1 class="font-extrabold text-gray-800 text-base">@yield('title')</h1>
            @endif
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-2.5 shrink-0">
            {{-- Date badge --}}
            <div class="hidden lg:flex items-center gap-2 text-xs text-gray-400 font-medium bg-gray-50 rounded-lg px-3 py-1.5 border border-gray-100">
                <svg class="w-3.5 h-3.5 text-gray-300" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M3 10h18M8 2v4M16 2v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>{{ now()->isoFormat('D MMM YYYY') }}</span>
            </div>

            {{-- User pill with dropdown --}}
            <div class="hidden sm:block relative" x-data="{ userMenu: false }">
                <button @click="userMenu = !userMenu" class="flex items-center gap-2.5 topbar-user-pill cursor-pointer">
                    <div class="w-8 h-8 rounded-xl overflow-hidden shrink-0 ring-2 ring-primary-100">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-600 text-white text-[11px] font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="hidden md:block">
                        <p class="text-xs font-bold text-gray-700 leading-tight max-w-28 truncate">
                            {{ explode(' ', auth()->user()->name)[0] }}
                        </p>
                        <p class="text-[10px] text-primary-400 font-medium leading-tight">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300 hidden md:block transition-transform duration-200" :class="userMenu && 'rotate-180'" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div x-show="userMenu" x-cloak @click.away="userMenu = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 p-1.5 z-50">
                    <div class="px-3 py-2.5 border-b border-gray-100 mb-1">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-gray-400 font-medium">
                            @php $roleLabel = match(auth()->user()->role) { 'admin' => 'Administrador', 'docente' => 'Docente', default => 'Alumno' }; @endphp
                            {{ $roleLabel }}
                        </p>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-primary-50 hover:text-primary-700 text-sm font-medium transition-all duration-200 group">
                        <div class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-primary-100 flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Mi Perfil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 text-sm font-medium transition-all duration-200 group">
                            <div class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center transition-colors">
                                <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Page Content --}}
    <main class="flex-1 p-4 sm:p-6 lg:p-8">

        {{-- Flash messages --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 x-init="setTimeout(() => show = false, 4000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200/60 text-emerald-800 rounded-xl px-5 py-3.5 text-sm animate-fade-in-up">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="flex-1 font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors p-1 rounded-lg hover:bg-emerald-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200/60 text-red-800 rounded-xl px-5 py-3.5 text-sm animate-fade-in-up">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="flex-1 font-medium">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors p-1 rounded-lg hover:bg-red-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200/60 rounded-xl p-5 animate-fade-in-up">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-red-800 font-bold text-sm">Se encontraron errores</h3>
                </div>
                <ul class="list-disc list-inside text-red-700 text-sm space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-4 px-6 text-center text-xs text-gray-400 border-t border-gray-100 bg-white/50">
        © {{ date('Y') }} {{ \App\Models\Setting::get('institution_name', config('app.name')) }} — Sistema de Gestión Académica
    </footer>
</div>

@stack('scripts')
</body>
</html>
