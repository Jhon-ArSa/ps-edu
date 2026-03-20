@props(['announcement'])

@php
$isUrgent = str_contains(strtolower($announcement->title), 'urgente') ||
           str_contains(strtolower($announcement->title), 'importante') ||
           str_contains(strtolower($announcement->content), 'urgente');

$posterGradients = [
    'urgent' => 'from-rose-500 via-pink-600 to-red-700',
    'important' => 'from-amber-400 via-orange-500 to-pink-600',
    'success' => 'from-emerald-400 via-teal-500 to-cyan-600',
    'info' => 'from-blue-500 via-indigo-600 to-violet-700',
    'dark' => 'from-slate-600 via-gray-700 to-zinc-800',
    'magical' => 'from-violet-500 via-purple-600 to-fuchsia-700',
    'ocean' => 'from-cyan-400 via-blue-500 to-indigo-600',
    'sunset' => 'from-orange-400 via-red-500 to-pink-600',
    'forest' => 'from-green-500 via-emerald-600 to-teal-700',
    'royal' => 'from-indigo-500 via-purple-600 to-pink-700',
];

$gradient = $isUrgent ? $posterGradients['urgent'] : $posterGradients[array_rand($posterGradients)];
$hasImage = $announcement->hasValidImage();
$isNew = $announcement->published_at && $announcement->published_at->gt(now()->subDays(1));
@endphp

<div class="relative w-full max-w-4xl mx-auto group">
    {{-- Main poster container --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $gradient }} shadow-2xl transform transition-all duration-700 hover:scale-[1.02] hover:shadow-3xl"
         style="min-height: 280px;"
         x-data="{ showDetails: false }">

        {{-- Magical Animated Background --}}
        <div class="absolute inset-0 overflow-hidden opacity-15">
            {{-- Enhanced Background Gradients --}}
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 via-white/5 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-tl from-white/10 via-transparent to-white/15"></div>

            {{-- SVG Pattern with Enhanced Design --}}
            <svg class="w-full h-full animate-pulse" viewBox="0 0 100 100" preserveAspectRatio="none" style="animation-duration: 4s;">
                <defs>
                    <pattern id="poster-pattern-{{ $announcement->id }}" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="1.2" fill="currentColor" opacity="0.3">
                            <animate attributeName="opacity" values="0.3;0.7;0.3" dur="3s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="5" cy="5" r="0.8" fill="currentColor" opacity="0.4">
                            <animate attributeName="opacity" values="0.4;0.8;0.4" dur="4s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="15" cy="15" r="0.6" fill="currentColor" opacity="0.5">
                            <animate attributeName="opacity" values="0.5;0.9;0.5" dur="5s" repeatCount="indefinite"/>
                        </circle>
                        <polygon points="10,2 12,8 8,8" fill="currentColor" opacity="0.2">
                            <animateTransform attributeName="transform" attributeType="XML" type="rotate" values="0 10 10;360 10 10" dur="8s" repeatCount="indefinite"/>
                        </polygon>
                    </pattern>
                    <radialGradient id="glow-{{ $announcement->id }}" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" style="stop-color:white;stop-opacity:0.3" />
                        <stop offset="100%" style="stop-color:white;stop-opacity:0" />
                    </radialGradient>
                </defs>
                <rect width="100" height="100" fill="url(#poster-pattern-{{ $announcement->id }})"/>
                <circle cx="50" cy="50" r="30" fill="url(#glow-{{ $announcement->id }})" opacity="0.6">
                    <animate attributeName="r" values="30;40;30" dur="6s" repeatCount="indefinite"/>
                </circle>
            </svg>
        </div>

        {{-- Enhanced Decorative Elements --}}
        <div class="absolute -top-20 -right-20 w-48 h-48 rounded-full bg-gradient-to-br from-white/20 to-white/5 blur-2xl animate-pulse" style="animation-duration: 5s;"></div>
        <div class="absolute top-10 right-16 w-8 h-8 bg-gradient-to-r from-yellow-300 to-orange-400 rounded-full animate-bounce shadow-lg shadow-yellow-400/50" style="animation-delay: 0.5s;"></div>
        <div class="absolute top-24 right-24 w-6 h-6 bg-gradient-to-br from-white/60 to-white/40 animate-spin shadow-md" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%); animation-duration: 12s;"></div>
        <div class="absolute bottom-16 right-12 w-10 h-10 bg-gradient-to-tr from-white/30 to-white/10 rounded-3xl rotate-12 animate-pulse shadow-lg" style="animation-delay: 1s; animation-duration: 4s;"></div>
        <div class="absolute -bottom-24 -left-24 w-56 h-56 rounded-full bg-gradient-to-t from-white/8 to-transparent blur-3xl"></div>

        {{-- Floating Particles --}}
        <div class="absolute top-1/4 left-8 w-4 h-4 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full animate-ping shadow-lg shadow-green-400/50" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/3 left-16 w-2 h-2 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full animate-bounce" style="animation-delay: 1.5s; animation-duration: 3s;"></div>
        <div class="absolute bottom-1/3 left-6 w-3 h-3 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full animate-pulse" style="animation-delay: 2.5s;"></div>
        <div class="absolute top-2/3 right-32 w-2 h-2 bg-gradient-to-r from-red-400 to-pink-500 rounded-full animate-ping" style="animation-delay: 3s;"></div>

        {{-- Content grid --}}
        <div class="relative h-full {{ $hasImage ? 'grid grid-cols-1 lg:grid-cols-12 gap-0' : 'flex items-center' }} p-8 lg:p-12">

            {{-- Text content side --}}
            <div class="{{ $hasImage ? 'lg:col-span-7 flex flex-col justify-center' : 'flex-1 text-center' }} z-10">

                {{-- Enhanced Priority Badges --}}
                <div class="flex {{ $hasImage ? 'justify-start' : 'justify-center' }} items-center flex-wrap gap-3 mb-6">
                    @if($isUrgent)
                        <div class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-red-500 to-pink-600 backdrop-blur-lg rounded-full border border-red-300/30 animate-pulse shadow-xl shadow-red-500/30">
                            <svg class="w-5 h-5 text-white animate-spin drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span class="text-sm font-black text-white uppercase tracking-wider drop-shadow-lg">⚠️ ¡URGENTE!</span>
                        </div>
                    @endif

                    @if($isNew)
                        <div class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-400 to-teal-600 backdrop-blur-lg rounded-full border border-emerald-300/30 shadow-xl shadow-emerald-500/30">
                            <div class="w-3 h-3 bg-white rounded-full animate-ping shadow-lg"></div>
                            <span class="text-sm font-black text-white uppercase tracking-wider drop-shadow-lg">✨ NUEVO</span>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 px-5 py-3 bg-white/25 backdrop-blur-xl rounded-full border border-white/40 shadow-xl">
                        @if($announcement->target_role === 'all')
                            <svg class="w-5 h-5 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                            <span class="text-sm font-bold text-white drop-shadow-lg">🌟 Comunidad</span>
                        @else
                            <svg class="w-5 h-5 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-bold text-white drop-shadow-lg">🎯 {{ ucfirst($announcement->target_role) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Spectacular Main Title --}}
                <div class="mb-6">
                    <h1 class="text-2xl lg:text-4xl xl:text-5xl font-black text-white leading-tight mb-4 {{ $hasImage ? 'text-left' : 'text-center' }} relative">
                        <span class="bg-gradient-to-r from-white via-white to-white/90 bg-clip-text text-transparent drop-shadow-2xl relative z-10" style="text-shadow: 0 4px 8px rgba(0,0,0,0.4);">
                            {{ $announcement->title }}
                        </span>
                        {{-- Subtle glow effect behind title --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-white/10 blur-lg rounded-lg -z-10 opacity-30"></div>
                    </h1>

                    {{-- Content preview --}}
                    <div class="relative">
                        <p class="text-lg lg:text-xl text-white/90 leading-relaxed {{ $hasImage ? 'text-left' : 'text-center' }} line-clamp-3"
                           x-show="!showDetails">
                            {{ Str::limit(strip_tags($announcement->content), 150) }}
                        </p>
                        <div class="text-lg text-white/90 leading-relaxed {{ $hasImage ? 'text-left' : 'text-center' }} whitespace-pre-wrap"
                             x-show="showDetails"
                             x-collapse>
                            {{ $announcement->content }}
                        </div>
                    </div>
                </div>

                {{-- Enhanced Action Buttons --}}
                <div class="flex {{ $hasImage ? 'justify-start' : 'justify-center' }} items-center gap-4 flex-wrap">
                    @if(strlen(strip_tags($announcement->content)) > 150)
                        <button @click="showDetails = !showDetails"
                                class="group flex items-center gap-3 px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-lg rounded-2xl border border-white/40 text-white font-bold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="text-lg drop-shadow-sm" x-text="showDetails ? '📖 Ver menos' : '📚 Leer completo'"></span>
                            <svg class="w-5 h-5 transition-transform duration-300 drop-shadow-sm"
                                 :class="showDetails ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    @endif

                    <button onclick="navigator.share ? navigator.share({title: '{{ addslashes($announcement->title) }}', text: '{{ addslashes(Str::limit(strip_tags($announcement->content), 100)) }}'}) : console.log('Share not available')"
                            class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-white to-gray-100 hover:from-gray-50 hover:to-white transform hover:scale-105 text-gray-800 font-bold rounded-2xl transition-all duration-300 shadow-xl hover:shadow-2xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                        </svg>
                        <span class="text-lg bg-gradient-to-r from-gray-700 to-gray-900 bg-clip-text text-transparent">🚀 Compartir</span>
                    </button>
                </div>

                {{-- Footer info --}}
                <div class="mt-8 pt-6 border-t border-white/20">
                    <div class="flex {{ $hasImage ? 'justify-start' : 'justify-center' }} items-center gap-6 text-white/70 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">{{ $announcement->published_at->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                        </div>
                        @if($announcement->author)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold">{{ $announcement->author->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Image side --}}
            @if($hasImage)
            <div class="lg:col-span-5 mt-8 lg:mt-0">
                <div class="relative h-64 lg:h-full min-h-64">
                    <img src="{{ $announcement->image_url }}"
                         alt="{{ $announcement->title }}"
                         class="w-full h-full object-cover rounded-2xl border-4 border-white/30 shadow-2xl transform rotate-2 group-hover:rotate-0 transition-transform duration-500">

                    {{-- Image overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent rounded-2xl"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Enhanced Animated Border --}}
        <div class="absolute inset-0 rounded-3xl border-2 border-white/30 animate-pulse shadow-2xl" style="animation-duration: 4s;"></div>
        <div class="absolute inset-0 rounded-3xl border border-white/20" style="animation: borderGlow 6s ease-in-out infinite;"></div>
    </div>
</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes borderGlow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(255,255,255,0.3), inset 0 0 5px rgba(255,255,255,0.1);
    }
    50% {
        box-shadow: 0 0 20px rgba(255,255,255,0.5), inset 0 0 10px rgba(255,255,255,0.2);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.7;
    }
    50% {
        transform: translateY(-10px) rotate(180deg);
        opacity: 1;
    }
}

@keyframes sparkle {
    0%, 100% {
        opacity: 0.4;
        transform: scale(0.8);
    }
    50% {
        opacity: 1;
        transform: scale(1.2);
    }
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Animación de entrada para el poster */
.poster-enter {
    animation: posterSlide 0.8s ease-out;
}

@keyframes posterSlide {
    from {
        opacity: 0;
        transform: translateX(-30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
}

/* Efecto de hover para los elementos interactivos */
.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.3s ease;
}

/* Animación suave para elementos flotantes */
.particle-float {
    animation: float 4s ease-in-out infinite;
}

.particle-sparkle {
    animation: sparkle 2s ease-in-out infinite;
}
</style>