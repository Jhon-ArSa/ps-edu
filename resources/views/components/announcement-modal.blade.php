@props(['announcement'])

@php
$isUrgent = str_contains(strtolower($announcement->title), 'urgente') ||
           str_contains(strtolower($announcement->title), 'importante') ||
           str_contains(strtolower($announcement->content), 'urgente');

$modalGradients = [
    'urgent' => 'from-rose-500 via-pink-600 to-red-700',
    'important' => 'from-amber-400 via-orange-500 to-pink-600',
    'success' => 'from-emerald-400 via-teal-500 to-cyan-600',
    'info' => 'from-blue-500 via-indigo-600 to-violet-700',
    'dark' => 'from-slate-600 via-gray-700 to-zinc-800',
    'magical' => 'from-violet-500 via-purple-600 to-fuchsia-700',
    'ocean' => 'from-cyan-400 via-blue-500 to-indigo-600',
    'sunset' => 'from-orange-400 via-red-500 to-pink-600',
];

$gradient = $isUrgent ? $modalGradients['urgent'] : $modalGradients[array_rand($modalGradients)];
$hasImage = $announcement->hasValidImage();
$isNew = $announcement->published_at && $announcement->published_at->gt(now()->subDays(1));
@endphp

{{-- Modal Overlay --}}
<div x-data="announcementModal()"
     x-show="showModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>

    {{-- Modal Content --}}
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform"
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-8"
             x-transition:enter-end="opacity-1 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-1 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-8">

            {{-- Main Modal Container --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $gradient }} shadow-2xl">

                {{-- Enchanted Animated Background --}}
                <div class="absolute inset-0 overflow-hidden opacity-15">
                    {{-- Floating Orbs --}}
                    <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-gradient-to-br from-white/30 to-white/5 animate-pulse blur-xl" style="animation-duration: 4s;"></div>
                    <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-gradient-to-tl from-white/20 to-transparent blur-2xl animate-pulse" style="animation-duration: 6s; animation-delay: 1s;"></div>

                    {{-- Magical Particles --}}
                    <div class="absolute top-16 right-20 w-2 h-2 bg-yellow-300 rounded-full animate-ping" style="animation-delay: 0.5s; box-shadow: 0 0 10px rgba(253, 224, 71, 0.5);"></div>
                    <div class="absolute top-32 right-32 w-1.5 h-1.5 bg-pink-300 rounded-full animate-bounce" style="animation-delay: 1.2s; animation-duration: 2s; box-shadow: 0 0 8px rgba(248, 113, 113, 0.4);"></div>
                    <div class="absolute top-20 right-40 w-1 h-1 bg-blue-300 rounded-full animate-ping" style="animation-delay: 2.1s;"></div>
                    <div class="absolute bottom-20 right-16 w-2.5 h-2.5 bg-emerald-300 rounded-full animate-pulse" style="animation-delay: 1.8s; animation-duration: 3s;"></div>

                    {{-- Geometric Elements --}}
                    <div class="absolute top-28 right-28 w-6 h-6 bg-white/40 rotate-45 animate-spin" style="animation-duration: 12s; border-radius: 2px;"></div>
                    <div class="absolute bottom-24 right-20 w-4 h-4 bg-white/30" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%); animation: float 6s ease-in-out infinite;"></div>

                    {{-- Glowing Dots --}}
                    <div class="absolute top-1/3 left-8 w-2 h-2 bg-violet-400 rounded-full animate-ping" style="animation-delay: 2.5s; box-shadow: 0 0 12px rgba(139, 92, 246, 0.6);"></div>
                    <div class="absolute top-2/3 left-12 w-1.5 h-1.5 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 1.7s; animation-duration: 2.5s;"></div>
                    <div class="absolute bottom-1/3 left-6 w-1 h-1 bg-cyan-400 rounded-full animate-pulse" style="animation-delay: 3s;"></div>
                </div>

                {{-- Close Button --}}
                <button @click="closeModal()"
                        class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition-all duration-200 hover:scale-110">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Content --}}
                <div class="relative p-8 lg:p-12">

                    {{-- Header with Badges --}}
                    <div class="mb-6">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            @if($isUrgent)
                                <div class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-pink-600 backdrop-blur-sm rounded-full border border-red-300/30 shadow-lg shadow-red-500/25 animate-pulse">
                                    <svg class="w-5 h-5 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <span class="text-sm font-black text-white uppercase tracking-wider drop-shadow-sm">¡URGENTE!</span>
                                </div>
                            @endif

                            @if($isNew)
                                <div class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-400 to-teal-600 backdrop-blur-sm rounded-full border border-emerald-300/30 shadow-lg shadow-emerald-500/25">
                                    <div class="w-3 h-3 bg-white rounded-full animate-ping shadow-sm"></div>
                                    <span class="text-sm font-black text-white uppercase tracking-wider drop-shadow-sm">✨ Nuevo</span>
                                </div>
                            @endif

                            <div class="flex items-center gap-2.5 px-5 py-2.5 bg-white/25 backdrop-blur-lg rounded-full border border-white/40 shadow-lg">
                                @if($announcement->target_role === 'all')
                                    <svg class="w-4 h-4 text-white drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-white drop-shadow-sm">🌟 Para toda la comunidad</span>
                                @else
                                    <svg class="w-4 h-4 text-white drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-bold text-white drop-shadow-sm">🎯 Para {{ ucfirst($announcement->target_role) }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Elegant Title --}}
                        <h1 class="text-3xl lg:text-4xl font-black text-white leading-tight mb-4 bg-gradient-to-r from-white via-white to-white/80 bg-clip-text text-transparent drop-shadow-lg" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                            {{ $announcement->title }}
                        </h1>

                        {{-- Meta Info --}}
                        <div class="flex items-center gap-4 text-white/80 text-sm mb-6">
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

                    {{-- Image --}}
                    @if($hasImage)
                    <div class="mb-6">
                        <img src="{{ $announcement->image_url }}"
                             alt="{{ $announcement->title }}"
                             class="w-full h-64 lg:h-80 object-cover rounded-2xl border-4 border-white/20 shadow-2xl">
                    </div>
                    @endif

                    {{-- Content --}}
                    <div class="mb-8">
                        <div class="text-lg lg:text-xl text-white/95 leading-relaxed whitespace-pre-wrap">
                            {{ $announcement->content }}
                        </div>
                    </div>

                    {{-- Elegant Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <button @click="closeModal()"
                                class="w-full sm:w-auto flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-white to-gray-100 hover:from-gray-50 hover:to-white text-gray-900 font-bold rounded-2xl transition-all duration-300 hover:scale-105 shadow-xl hover:shadow-2xl transform">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-lg bg-gradient-to-r from-gray-700 to-gray-900 bg-clip-text text-transparent">✨ Entendido</span>
                        </button>

                        <button onclick="navigator.share ? navigator.share({title: '{{ addslashes($announcement->title) }}', text: '{{ addslashes(Str::limit(strip_tags($announcement->content), 100)) }}'}) : console.log('Share not available')"
                                class="w-full sm:w-auto flex items-center justify-center gap-3 px-8 py-4 bg-white/20 hover:bg-white/30 backdrop-blur-lg rounded-2xl border border-white/40 text-white font-bold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                            </svg>
                            <span class="text-lg drop-shadow-sm">🚀 Compartir</span>
                        </button>

                        <button @click="markAsRead({{ $announcement->id }})"
                                class="w-full sm:w-auto flex items-center justify-center gap-3 px-8 py-4 bg-white/15 hover:bg-white/25 backdrop-blur-lg rounded-2xl border border-white/30 text-white/90 font-semibold transition-all duration-300 hover:scale-105 text-sm shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m-3.122-3.122L3 3m6.878 6.878L12 12"/>
                            </svg>
                            <span class="drop-shadow-sm">👁️ No volver a mostrar</span>
                        </button>

                        {{-- Enhanced DEBUG Button --}}
                        <button @click="clearDebug({{ $announcement->id }})"
                                class="text-white/40 text-xs hover:text-white/70 transition-colors hover:scale-105 transform duration-200 px-3 py-2 rounded-lg bg-white/5 backdrop-blur-sm border border-white/10"
                                title="Debug: Limpiar localStorage">
                            🐛 Reset Debug
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function announcementModal() {
    return {
        showModal: false,

        init() {
            // Debug: mostrar en consola
            console.log('🔔 Modal de anuncio inicializado');
            console.log('📢 ID del anuncio: {{ $announcement->id }}');

            // Mostrar modal automáticamente después de que cargue la página
            setTimeout(() => {
                const modalShown = localStorage.getItem('announcement_{{ $announcement->id }}_shown');
                const modalRead = localStorage.getItem('announcement_{{ $announcement->id }}_read');

                console.log('📊 Estado del modal:', {
                    modalShown,
                    modalRead,
                    shouldShow: !modalShown && !modalRead
                });

                if (!modalShown && !modalRead) {
                    console.log('✅ Mostrando modal de anuncio');
                    this.showModal = true;
                    localStorage.setItem('announcement_{{ $announcement->id }}_shown', 'true');
                } else {
                    console.log('❌ Modal ya mostrado anteriormente');
                }
            }, 500); // Reducido a 0.5 segundos para debug
        },

        closeModal() {
            this.showModal = false;
        },

        markAsRead(announcementId) {
            localStorage.setItem(`announcement_${announcementId}_read`, 'true');
            this.closeModal();
        },

        clearDebug(announcementId) {
            localStorage.removeItem(`announcement_${announcementId}_shown`);
            localStorage.removeItem(`announcement_${announcementId}_read`);
            console.log('🗑️ localStorage limpiado para anuncio', announcementId);
            this.closeModal();
            // Recargar página para probar modal
            setTimeout(() => location.reload(), 500);
        }
    }
}
</script>

<style>
[x-cloak] {
    display: none !important;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
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

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

@keyframes glow {
    0%, 100% {
        box-shadow: 0 0 5px currentColor;
    }
    50% {
        box-shadow: 0 0 20px currentColor, 0 0 30px currentColor;
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

/* Animación de entrada suave para el modal */
.modal-enter {
    animation: modalEnter 0.5s ease-out;
}

@keyframes modalEnter {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Efecto de brillo sutil en el título */
.title-glow {
    background: linear-gradient(45deg, transparent 40%, rgba(255,255,255,0.8) 50%, transparent 60%);
    background-size: 200% 200%;
    animation: shimmer 3s infinite;
}

/* Efecto de partículas flotantes */
.particle-float {
    animation: float 4s ease-in-out infinite;
}

.particle-sparkle {
    animation: sparkle 2s ease-in-out infinite;
}
</style>