@props(['announcement'])

@php
$isUrgent = str_contains(strtolower($announcement->title), 'urgente') ||
           str_contains(strtolower($announcement->title), 'importante') ||
           str_contains(strtolower($announcement->content), 'urgente');

// Colores institucionales profesionales y formales
$modalStyles = [
    'urgent' => [
        'bg' => 'bg-gradient-to-br from-red-50 via-white to-orange-50',
        'border' => 'border-red-300',
        'accent' => 'bg-gradient-to-r from-red-600 to-orange-600',
        'badge' => 'bg-red-100 text-red-800 border-red-300',
        'icon' => 'text-red-600',
        'button' => 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800',
    ],
    'important' => [
        'bg' => 'bg-gradient-to-br from-amber-50 via-white to-yellow-50',
        'border' => 'border-amber-300',
        'accent' => 'bg-gradient-to-r from-amber-500 to-orange-500',
        'badge' => 'bg-amber-100 text-amber-800 border-amber-300',
        'icon' => 'text-amber-600',
        'button' => 'bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800',
    ],
    'success' => [
        'bg' => 'bg-gradient-to-br from-emerald-50 via-white to-teal-50',
        'border' => 'border-emerald-300',
        'accent' => 'bg-gradient-to-r from-emerald-600 to-teal-600',
        'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        'icon' => 'text-emerald-600',
        'button' => 'bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800',
    ],
    'info' => [
        'bg' => 'bg-gradient-to-br from-blue-50 via-white to-indigo-50',
        'border' => 'border-blue-300',
        'accent' => 'bg-gradient-to-r from-blue-600 to-indigo-600',
        'badge' => 'bg-blue-100 text-blue-800 border-blue-300',
        'icon' => 'text-blue-600',
        'button' => 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800',
    ],
];

// Determinar el estilo según el tipo de anuncio
if ($isUrgent) {
    $style = $modalStyles['urgent'];
} elseif (str_contains(strtolower($announcement->title), 'éxito') || 
          str_contains(strtolower($announcement->title), 'felicitaciones')) {
    $style = $modalStyles['success'];
} elseif (str_contains(strtolower($announcement->title), 'importante')) {
    $style = $modalStyles['important'];
} else {
    // Por defecto: azul institucional
    $style = $modalStyles['info'];
}

$hasImage = $announcement->hasValidImage();
$isNew = $announcement->published_at && $announcement->published_at->gt(now()->subDays(1));
@endphp

{{-- Modal Overlay --}}
<div x-data="announcementModal_{{ $announcement->id }}()"
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
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-md" @click="closeModal()"></div>

    {{-- Modal Content --}}
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
        <div class="relative w-full max-w-4xl transform"
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-1 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-1 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">

            {{-- Main Modal Container --}}
            <div class="relative overflow-hidden rounded-3xl {{ $style['bg'] }} border-2 {{ $style['border'] }} shadow-2xl">

                {{-- Barra de acento superior con gradiente --}}
                <div class="{{ $style['accent'] }} h-3 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer" style="background-size: 200% 100%;"></div>
                </div>

                {{-- Close Button --}}
                <button @click="closeModal()"
                        class="absolute top-5 right-5 z-10 w-11 h-11 bg-white/90 hover:bg-white rounded-xl flex items-center justify-center text-gray-600 hover:text-gray-900 transition-all duration-200 hover:scale-110 hover:rotate-90 border border-gray-200 shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Content --}}
                <div class="relative p-7 sm:p-10 lg:p-12">

                    {{-- Header with Badges --}}
                    <div class="mb-7">
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            @if($isUrgent)
                                <div class="flex items-center gap-2.5 px-4 py-2.5 {{ $style['badge'] }} rounded-xl border-2 font-bold shadow-sm animate-pulse">
                                    <svg class="w-5 h-5 {{ $style['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <span class="text-sm uppercase tracking-wider">⚠️ Urgente</span>
                                </div>
                            @endif

                            @if($isNew)
                                <div class="flex items-center gap-2.5 px-4 py-2.5 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border-2 border-blue-300 rounded-xl font-bold shadow-sm">
                                    <div class="w-2.5 h-2.5 bg-blue-600 rounded-full animate-pulse"></div>
                                    <span class="text-sm uppercase tracking-wider">✨ Nuevo</span>
                                </div>
                            @endif

                            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border-2 border-gray-300 rounded-xl font-semibold shadow-sm">
                                @if($announcement->target_role === 'all')
                                    <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    <span class="text-sm">🌟 Toda la comunidad</span>
                                @else
                                    <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm">🎯 {{ ucfirst($announcement->target_role) }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Title --}}
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 leading-tight mb-5 tracking-tight">
                            {{ $announcement->title }}
                        </h1>

                        {{-- Meta --}}
                        <div class="flex flex-wrap items-center gap-5 text-gray-700 text-sm">
                            <div class="flex items-center gap-2.5 px-3 py-1.5 bg-white/60 rounded-lg border border-gray-200">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold">{{ $announcement->published_at->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
                            </div>
                            @if($announcement->author)
                                <div class="flex items-center gap-2.5 px-3 py-1.5 bg-white/60 rounded-lg border border-gray-200">
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-semibold">{{ $announcement->author->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Image --}}
                    @if($hasImage)
                    <div class="mb-7">
                        <div class="relative overflow-hidden rounded-2xl border-2 {{ $style['border'] }} shadow-xl">
                            <img src="{{ $announcement->image_url }}"
                                 alt="{{ $announcement->title }}"
                                 class="w-full h-60 sm:h-72 lg:h-96 object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
                        </div>
                    </div>
                    @endif

                    {{-- Content --}}
                    <div class="mb-8">
                        <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed whitespace-pre-wrap bg-white/50 rounded-xl p-6 border border-gray-200">
                            {{ $announcement->content }}
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-7 border-t-2 border-gray-200">
                        <button @click="closeModal()"
                                class="flex-1 sm:flex-initial flex items-center justify-center gap-3 px-8 py-4 {{ $style['button'] }} text-white font-bold rounded-xl transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl text-base">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Entendido</span>
                        </button>

                        <button @click="markAsRead({{ $announcement->id }})"
                                class="flex-1 sm:flex-initial flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 rounded-xl border-2 border-gray-300 text-gray-800 font-semibold transition-all duration-200 hover:scale-105 shadow-md hover:shadow-lg text-base">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12"/>
                            </svg>
                            <span>No volver a mostrar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function announcementModal_{{ $announcement->id }}() {
    return {
        showModal: false,

        init() {
            document.addEventListener('show-popup-{{ $announcement->id }}', () => {
                if (!localStorage.getItem('announcement_{{ $announcement->id }}_read')) {
                    this.showModal = true;
                    localStorage.setItem('announcement_{{ $announcement->id }}_shown', 'true');
                } else {
                    // Ya marcado como leído, avanzar cola
                    document.dispatchEvent(new CustomEvent('popup-closed'));
                }
            });
        },

        closeModal() {
            this.showModal = false;
            setTimeout(() => document.dispatchEvent(new CustomEvent('popup-closed')), 300);
        },

        markAsRead(announcementId) {
            localStorage.setItem(`announcement_${announcementId}_read`, 'true');
            this.showModal = false;
            setTimeout(() => document.dispatchEvent(new CustomEvent('popup-closed')), 300);
        },
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
