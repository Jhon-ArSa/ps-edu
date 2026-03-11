@extends('layouts.app')

@section('title', $topic->title . ' — Foro')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in-up">

    {{-- Breadcrumb / Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-800 text-white p-8 mb-6 shadow-xl">
        <div class="relative">
            <a href="{{ route($indexRoute, $course) }}" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm mb-3 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Foro del curso
            </a>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                @if($topic->is_pinned)
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">📌 Fijado</span>
                @endif
                @if($topic->is_closed)
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">🔒 Cerrado</span>
                @endif
            </div>
            <h1 class="text-xl font-bold leading-snug">{{ $topic->title }}</h1>
            <p class="text-white/70 text-sm mt-1">
                Publicado por <span class="font-semibold text-white/90">{{ $topic->author->name }}</span>
                &bull; {{ $topic->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Cuerpo del tema --}}
    <div class="card p-6 mb-6">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                    {{ strtoupper(substr($topic->author->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800">{{ $topic->author->name }}</p>
                    <p class="text-xs text-gray-500">{{ $topic->created_at->diffForHumans() }}</p>
                </div>
            </div>
            {{-- Acciones de moderación (docente) --}}
            @if($role === 'docente')
            <div class="flex gap-1 flex-shrink-0">
                @if($pinRoute)
                <form method="POST" action="{{ route($pinRoute, [$course, $topic]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 text-xs rounded-lg {{ $topic->is_pinned ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600' }} hover:bg-amber-100 hover:text-amber-700 transition-colors font-medium">
                        {{ $topic->is_pinned ? '📌 Desfijar' : '📌 Fijar' }}
                    </button>
                </form>
                @endif
                @if($closeRoute)
                <form method="POST" action="{{ route($closeRoute, [$course, $topic]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 text-xs rounded-lg {{ $topic->is_closed ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} transition-colors font-medium">
                        {{ $topic->is_closed ? '🔓 Abrir' : '🔒 Cerrar' }}
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
        <div class="mt-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $topic->body }}</div>
    </div>

    {{-- Respuestas --}}
    <div class="space-y-3 mb-6">
        <h3 class="text-sm font-semibold text-gray-600 flex items-center gap-2">
            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            {{ $topic->replies_count }} {{ $topic->replies_count === 1 ? 'respuesta' : 'respuestas' }}
        </h3>

        @forelse($replies as $reply)
        <div class="card p-5 group">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full {{ $reply->author->role === 'docente' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }} font-bold text-sm flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($reply->author->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-800">{{ $reply->author->name }}</p>
                            @if($reply->author->role === 'docente')
                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-100 text-blue-600 font-medium">Docente</span>
                            @endif
                            <p class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="mt-2 text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $reply->body }}</div>
                    </div>
                </div>
                {{-- Eliminar respuesta --}}
                @if($reply->canDelete(auth()->user()))
                <form method="POST" action="{{ route('forum.replies.destroy', [$topic, $reply]) }}"
                      onsubmit="return confirm('¿Eliminar esta respuesta?')"
                      class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="card p-8 text-center text-gray-400">
            <p class="text-sm">Aún no hay respuestas. ¡Sé el primero en responder!</p>
        </div>
        @endforelse

        {{-- Paginación --}}
        @if($replies->hasPages())
        <div class="mt-4">{{ $replies->links() }}</div>
        @endif
    </div>

    {{-- Formulario de respuesta --}}
    @if(!$topic->is_closed && $topic->canReply(auth()->user()))
    <div class="card p-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            Tu respuesta
        </h4>
        <form method="POST" action="{{ route('forum.replies.store', $topic) }}" x-data="{ len: 0 }">
            @csrf
            <textarea name="body"
                @input="len = $el.value.length"
                class="form-input mb-1" rows="4"
                minlength="5" maxlength="5000"
                placeholder="Escribe tu respuesta..."
                required>{{ old('body') }}</textarea>
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs text-gray-400" :class="len > 4800 && 'text-amber-500'">
                    <span x-text="len"></span>/5000
                </p>
            </div>
            @error('body')<p class="form-error mb-2">{{ $message }}</p>@enderror
            <div class="flex justify-end">
                <button type="submit" class="btn-primary bg-teal-600 hover:bg-teal-700 border-teal-600">
                    Publicar respuesta
                </button>
            </div>
        </form>
    </div>
    @elseif($topic->is_closed)
    <div class="card p-5 text-center bg-gray-50 border border-gray-200">
        <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zM10 11V7a2 2 0 114 0v4"/></svg>
            Este tema está cerrado y no acepta nuevas respuestas.
        </p>
    </div>
    @endif

</div>
@endsection
