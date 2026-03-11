@extends('layouts.app')

@section('title', 'Foro — ' . $course->name)

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in-up">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-800 text-white p-8 mb-6 shadow-xl">
        <div class="absolute inset-0 opacity-10" style="background-image:url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                @if($role === 'docente')
                <a href="{{ route('docente.courses.show', $course) }}" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm mb-3 transition-colors">
                @else
                <a href="{{ route('alumno.courses.show', $course) }}" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm mb-3 transition-colors">
                @endif
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $course->name }}
                </a>
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-6 h-6 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <h1 class="text-2xl font-bold">Foro del Curso</h1>
                </div>
                <p class="text-white/70 text-sm">{{ $topics->total() }} {{ $topics->total() === 1 ? 'tema' : 'temas' }} publicados</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Formulario nuevo tema --}}
    <div x-data="{ open: false }" class="card mb-6">
        <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition-colors rounded-2xl">
            <span class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Publicar nuevo tema
            </span>
            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="border-t border-gray-100 p-5">
            <form method="POST" action="{{ route($storeRoute, $course) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Título del tema</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-input" placeholder="¿Sobre qué quieres hablar?" required maxlength="255">
                    @error('title')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div x-data="{ len: {{ strlen(old('body', '')) }} }">
                    <label class="form-label">Contenido</label>
                    <textarea name="body" @input="len = $el.value.length" class="form-input" rows="4" minlength="10" maxlength="5000" placeholder="Escribe el contenido del tema..." required>{{ old('body') }}</textarea>
                    <p class="text-xs text-gray-400 text-right" :class="len > 4800 && 'text-amber-500'">
                        <span x-text="len"></span>/5000
                    </p>
                    @error('body')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary bg-teal-600 hover:bg-teal-700 border-teal-600">Publicar tema</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de temas --}}
    @forelse($topics as $topic)
    <div class="card mb-3 group">
        <div class="p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-700 font-bold text-sm flex items-center justify-center flex-shrink-0 mt-0.5">
                        {{ strtoupper(substr($topic->author->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            @if($topic->is_pinned)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                📌 Fijado
                            </span>
                            @endif
                            @if($topic->is_closed)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                🔒 Cerrado
                            </span>
                            @endif
                        </div>
                        <a href="{{ route($showRoute, [$course, $topic]) }}" class="text-base font-semibold text-gray-800 hover:text-teal-600 transition-colors line-clamp-2">
                            {{ $topic->title }}
                        </a>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium text-gray-600">{{ $topic->author->name }}</span>
                            &bull; {{ $topic->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <div class="text-center hidden sm:block">
                        <p class="text-base font-bold text-gray-700">{{ $topic->replies_count }}</p>
                        <p class="text-xs text-gray-400">respuestas</p>
                    </div>
                    {{-- Acciones docente (hover) --}}
                    @if($role === 'docente' && ($pinRoute || $closeRoute))
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if($pinRoute)
                        <form method="POST" action="{{ route($pinRoute, [$course, $topic]) }}" x-data @submit.prevent="fetch($el.action, {method:'PATCH', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload())">
                            @csrf @method('PATCH')
                            <button type="submit" title="{{ $topic->is_pinned ? 'Desfijar' : 'Fijar' }}" class="p-1.5 rounded-lg text-gray-400 hover:text-amber-500 hover:bg-amber-50 transition-colors">
                                <svg class="w-4 h-4" fill="{{ $topic->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </form>
                        @endif
                        @if($closeRoute)
                        <form method="POST" action="{{ route($closeRoute, [$course, $topic]) }}" x-data @submit.prevent="fetch($el.action, {method:'PATCH', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload())">
                            @csrf @method('PATCH')
                            <button type="submit" title="{{ $topic->is_closed ? 'Abrir' : 'Cerrar' }}" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $topic->is_closed ? 'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z' : 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zM10 11V7a2 2 0 114 0v4' }}"/></svg>
                            </button>
                        </form>
                        @endif
                        @if($topic->canDelete(auth()->user()))
                        <form method="POST" action="{{ route($destroyRoute, [$course, $topic]) }}" onsubmit="return confirm('¿Eliminar este tema y todas sus respuestas?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                    @elseif($topic->user_id === auth()->id())
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                        <form method="POST" action="{{ route($destroyRoute, [$course, $topic]) }}" onsubmit="return confirm('¿Eliminar tu tema?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @if($topic->last_reply_at)
            <p class="text-xs text-gray-400 mt-2 pl-12">Última respuesta {{ $topic->last_reply_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="card p-12 text-center text-gray-400">
        <div class="text-4xl mb-3">💬</div>
        <p class="font-semibold text-gray-600">El foro está vacío aún.</p>
        <p class="text-sm mt-1">¡Sé el primero en publicar un tema!</p>
    </div>
    @endforelse

    {{-- Paginación --}}
    @if($topics->hasPages())
    <div class="mt-6">{{ $topics->links() }}</div>
    @endif

</div>
@endsection
