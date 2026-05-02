@extends('layouts.app')

@section('title', 'Mis Anuncios Emergentes')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Mis Anuncios Emergentes</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Mis Anuncios Emergentes</h1>
            <p class="text-sm text-gray-500">Comunicados popup para alumnos de tus programas</p>
        </div>
        <a href="{{ route('docente.announcements.create') }}"
           class="shrink-0 inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-lg shadow-orange-500/25">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo anuncio
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Lista --}}
    <div class="space-y-3">
        @forelse($announcements as $ann)
        @php
            $isPublished = $ann->published_at && $ann->published_at->isPast();
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200 group">
            <div class="flex items-stretch">
                {{-- Thumbnail --}}
                @if($ann->hasValidImage())
                <div class="w-28 sm:w-36 shrink-0 hidden sm:block">
                    <img src="{{ $ann->image_url }}" alt="{{ $ann->title }}" class="w-full h-full object-cover">
                </div>
                @endif

                {{-- Indicador de color --}}
                <div class="w-1 flex-shrink-0 {{ $isPublished ? 'bg-orange-400' : 'bg-gray-300' }}"></div>

                {{-- Contenido --}}
                <div class="flex-1 p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                @if(!$isPublished)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                    Borrador
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                    Emergente activo
                                </span>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 leading-snug">{{ $ann->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($ann->content), 120) }}</p>

                            {{-- Cursos destinatarios --}}
                            @if($ann->courses->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach($ann->courses->take(4) as $course)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    {{ $course->name }}
                                </span>
                                @endforeach
                                @if($ann->courses->count() > 4)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                                    +{{ $ann->courses->count() - 4 }} más
                                </span>
                                @endif
                            </div>
                            @endif
                        </div>

                        {{-- Acciones --}}
                        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <form method="POST" action="{{ route('docente.announcements.destroy', $ann) }}"
                                  data-confirm="¿Eliminar este anuncio emergente?">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            @if($ann->published_at)
                                {{ $ann->published_at->format('d/m/Y H:i') }}
                            @else
                                <span class="italic">Sin fecha de publicación</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-orange-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Sin anuncios emergentes</h3>
            <p class="text-xs text-gray-400 mb-4">Crea un anuncio emergente para notificar a los alumnos de tus programas.</p>
            <a href="{{ route('docente.announcements.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear anuncio
            </a>
        </div>
        @endforelse
    </div>

    @if($announcements->hasPages())
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        {{ $announcements->links() }}
    </div>
    @endif

</div>
@endsection
