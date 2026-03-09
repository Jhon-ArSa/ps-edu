@props(['route', 'icon' => 'dot'])

@php
    $active = request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<a href="{{ route($route) }}"
   class="sidebar-link group {{ $active ? 'sidebar-link-active' : '' }}"
   x-data x-bind:title="$root.sidebarCollapsed ? '{{ addslashes(strip_tags($slot)) }}' : ''"
   x-bind:class="$root.sidebarCollapsed ? 'md:!justify-center md:!px-2 md:!gap-0 sidebar-link-collapsed' : ''">

    {{-- Active indicator --}}
    @if($active)
        <span class="sidebar-active-bar"
              x-bind:class="$root.sidebarCollapsed ? 'md:!left-1/2 md:!-translate-x-1/2 md:!top-auto md:!-bottom-0.5 md:!w-4 md:!h-[2px] md:!rounded-full' : ''"></span>
    @endif

    <div class="sidebar-link-icon {{ $active ? 'sidebar-link-icon-active' : '' }}">
        @switch($icon)
            @case('home')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21V13.6c0-.56 0-.84.109-1.054a1 1 0 01.437-.437C9.76 12 10.04 12 10.6 12h2.8c.56 0 .84 0 1.054.109a1 1 0 01.437.437C15 12.76 15 13.04 15 13.6V21M11.018 2.764L4.235 8.039c-.453.353-.68.53-.843.75a2 2 0 00-.318.65C3 9.704 3 9.991 3 10.565V17.8c0 1.12 0 1.68.218 2.108a2 2 0 00.874.874C4.52 21 5.08 21 6.2 21h11.6c1.12 0 1.68 0 2.108-.218a2 2 0 00.874-.874C21 19.48 21 18.92 21 17.8v-7.235c0-.574 0-.861-.074-1.126a2.002 2.002 0 00-.318-.65c-.163-.22-.39-.397-.843-.75l-6.783-5.275c-.351-.273-.527-.41-.72-.462a1 1 0 00-.524 0c-.193.052-.369.189-.72.462z"/>
                </svg>
                @break
            @case('users')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 15.837C19.778 16.558 21 17.837 21 19.5M15 11a4 4 0 10-4.5-6.434M3 19.5c0-2.485 2.686-4.5 6-4.5s6 2.015 6 4.5M13 7.5a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                @break
            @case('book')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 7.5a5.5 5.5 0 00-5.096-3.476A5 5 0 002 9v8.5A5.5 5.5 0 017.5 12H12m0-4.5a5.5 5.5 0 015.096-3.476A5 5 0 0122 9v8.5A5.5 5.5 0 0016.5 12H12m0-4.5V21"/>
                </svg>
                @break
            @case('bell')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.354 21c.705.622 1.632 1 2.646 1s1.94-.378 2.646-1M2.294 5.65A4 4 0 014.643 2m17.063 3.65A4 4 0 0019.357 2M18 8a6 6 0 10-12 0c0 3.09-.78 5.206-1.65 6.605-.735 1.18-1.102 1.771-1.089 1.936.015.182.054.252.2.36.133.099.732.099 1.928.099H18.61c1.197 0 1.795 0 1.927-.098.147-.11.186-.179.2-.361.014-.165-.353-.756-1.088-1.936C18.78 13.206 18 11.09 18 8z"/>
                </svg>
                @break
            @case('cog')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.395 2.974a9.39 9.39 0 011.654-.37c.272-.036.523.15.588.418l.244 1.012c.122.507.455.934.905 1.127.45.193.96.172 1.408-.064l.912-.48a.526.526 0 01.674.108c.635.72 1.14 1.553 1.484 2.467a.526.526 0 01-.17.58l-.668.533c-.388.31-.623.746-.623 1.22s.235.91.623 1.22l.668.533a.526.526 0 01.17.58 9.3 9.3 0 01-1.484 2.467.526.526 0 01-.674.108l-.912-.48a1.563 1.563 0 00-1.408-.064c-.45.193-.783.62-.905 1.127l-.244 1.012a.526.526 0 01-.588.418 9.44 9.44 0 01-1.654-.37.526.526 0 01-.35-.505V19.38c0-.52-.202-.989-.576-1.27-.375-.282-.858-.353-1.32-.193l-.947.33a.526.526 0 01-.632-.228 9.308 9.308 0 01-1.116-2.685.526.526 0 01.245-.554l.822-.478c.45-.262.718-.711.718-1.212v-.18c0-.5-.268-.95-.718-1.213l-.822-.478a.526.526 0 01-.245-.553A9.308 9.308 0 015.697 8.28a.526.526 0 01.632-.228l.947.33c.462.16.945.089 1.32-.193.374-.281.576-.75.576-1.27V5.48a.526.526 0 01.35-.505z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                @break
            @case('newspaper')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 11h1M14 15h1M8.4 3h7.2c1.68 0 2.52 0 3.162.327a3 3 0 011.311 1.311C20.4 5.28 20.4 6.12 20.4 7.8v8.4c0 1.68 0 2.52-.327 3.162a3 3 0 01-1.311 1.311C18.12 21 17.28 21 15.6 21H8.4c-1.68 0-2.52 0-3.162-.327a3 3 0 01-1.311-1.311C3.6 18.72 3.6 17.88 3.6 16.2V7.8c0-1.68 0-2.52.327-3.162a3 3 0 011.311-1.311C5.88 3 6.72 3 8.4 3zM8 11h2v4H8v-4zM8 7h6"/>
                </svg>
                @break
            @case('id-card')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 9.1c0-2.24 0-3.36.436-4.216a4 4 0 011.748-1.748C5.04 2.7 6.16 2.7 8.4 2.7h7.2c2.24 0 3.36 0 4.216.436a4 4 0 011.748 1.748C22 5.74 22 6.86 22 9.1v5.8c0 2.24 0 3.36-.436 4.216a4 4 0 01-1.748 1.748C18.96 21.3 17.84 21.3 15.6 21.3H8.4c-2.24 0-3.36 0-4.216-.436a4 4 0 01-1.748-1.748C2 18.26 2 17.14 2 14.9V9.1z"/>
                    <circle cx="9" cy="10" r="2.5"/>
                    <path d="M5 17.5c0-1.38 1.79-2.5 4-2.5s4 1.12 4 2.5M15 9h3.5M15 13h2"/>
                </svg>
                @break
            @case('support')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 002 2zM20 16.8V12c0-3.7-2.5-6.8-6-7.7V3.5C14 2.7 13.1 2 12 2s-2 .7-2 1.5v.8C6.5 5.2 4 8.3 4 12v4.8L2 18v1h20v-1l-2-1.2z"/>
                    <path d="M8 12a4 4 0 018 0"/>
                </svg>
                @break
            @case('calendar')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                    <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>
                </svg>
                @break
            @case('clipboard')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="2" width="6" height="4" rx="1"/>
                    <path d="M9 14l2 2 4-4"/>
                </svg>
                @break
            @case('graduation')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    <path d="M12 14v7"/>
                </svg>
                @break
            @case('user')
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                @break
            @default
                <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="3" fill="currentColor"/>
                </svg>
        @endswitch
    </div>
    <span class="flex-1 sidebar-link-text whitespace-nowrap overflow-hidden sidebar-hideable" x-bind:class="$root.sidebarCollapsed && 'sidebar-hide'">{{ $slot }}</span>
    @if($active)
        <span class="sidebar-active-dot shrink-0 sidebar-hideable" x-bind:class="$root.sidebarCollapsed && 'sidebar-hide'"></span>
    @endif
</a>
