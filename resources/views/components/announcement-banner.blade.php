@props(['announcement', 'size' => 'default'])

@php
$sizeClasses = match($size) {
    'large' => 'p-6 lg:p-8',
    'small' => 'p-4',
    default => 'p-5 lg:p-6',
};

$gradients = [
    'primary' => 'from-blue-600 via-purple-600 to-blue-800',
    'success' => 'from-emerald-500 via-teal-600 to-cyan-600',
    'warning' => 'from-amber-500 via-orange-600 to-red-600',
    'info' => 'from-indigo-500 via-purple-600 to-pink-600',
    'dark' => 'from-gray-800 via-gray-900 to-gray-800',
];

$gradient = $gradients[array_rand($gradients)];

$isNew = $announcement->published_at && $announcement->published_at->gt(now()->subDays(2));
$hasImage = $announcement->hasValidImage();
@endphp

<div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $gradient }} shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-1 animate-fade-in-up"
     x-data="{ expanded: false }">

    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <pattern id="grid-{{ $announcement->id }}" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="10" cy="10" r="1" fill="currentColor" opacity="0.3"/>
                </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#grid-{{ $announcement->id }})"/>
        </svg>
    </div>

    {{-- Decorative shapes --}}
    <div class="absolute -top-8 -right-8 w-24 h-24 rounded-full bg-white/10 blur-sm"></div>
    <div class="absolute top-4 right-6 w-3 h-3 rounded-full bg-yellow-300/70 animate-pulse"></div>
    <div class="absolute bottom-6 right-12 w-5 h-5 bg-white/20" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);"></div>
    <div class="absolute -bottom-6 -left-6 w-20 h-20 rounded-full bg-white/5"></div>

    {{-- Content --}}
    <div class="relative {{ $sizeClasses }}">
        {{-- Header with title and badges --}}
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1 pr-4">
                {{-- Role badge --}}
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm text-white border border-white/30">
                        @if($announcement->target_role === 'all')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Para todos
                        @elseif($announcement->target_role === 'alumno')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            Estudiantes
                        @elseif($announcement->target_role === 'docente')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                            </svg>
                            Docentes
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                            </svg>
                            Administradores
                        @endif
                    </span>
                    @if($isNew)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-500 text-white animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                            NUEVO
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h3 class="text-lg lg:text-xl font-extrabold text-white leading-tight mb-2 line-clamp-2">
                    {{ $announcement->title }}
                </h3>
            </div>

            {{-- Priority/Action indicator --}}
            <div class="shrink-0">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 group-hover:bg-white/30 transition-all duration-300">
                    <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Image section (if exists) --}}
        @if($hasImage)
        <div class="mb-4 -mx-2">
            <img src="{{ $announcement->image_url }}"
                 alt="{{ $announcement->title }}"
                 class="w-full h-32 lg:h-40 object-cover rounded-xl border-2 border-white/20 shadow-lg">
        </div>
        @endif

        {{-- Content preview --}}
        <div class="mb-4">
            <p class="text-white/90 text-sm leading-relaxed line-clamp-2"
               x-show="!expanded">
                {{ Str::limit(strip_tags($announcement->content), 120) }}
            </p>
            <div class="text-white/90 text-sm leading-relaxed whitespace-pre-wrap"
                 x-show="expanded"
                 x-collapse>
                {{ $announcement->content }}
            </div>
        </div>

        {{-- Footer with actions --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 text-white/70 text-xs">
                <div class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ $announcement->published_at->diffForHumans() }}</span>
                </div>
                @if($announcement->author)
                <div class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ explode(' ', $announcement->author->name)[0] }}</span>
                </div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center gap-2">
                @if(strlen(strip_tags($announcement->content)) > 120)
                <button @click="expanded = !expanded"
                        class="px-3 py-1.5 bg-white/15 hover:bg-white/25 backdrop-blur-sm rounded-lg text-xs font-bold text-white border border-white/20 transition-all duration-200">
                    <span x-text="expanded ? 'Ver menos' : 'Ver más'"></span>
                </button>
                @endif

                <button onclick="navigator.share ? navigator.share({title: '{{ addslashes($announcement->title) }}', text: '{{ addslashes(Str::limit(strip_tags($announcement->content), 100)) }}'}) : console.log('Share not available')"
                        class="p-1.5 bg-white/15 hover:bg-white/25 backdrop-blur-sm rounded-lg text-white border border-white/20 transition-all duration-200"
                        title="Compartir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>