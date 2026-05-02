@extends('layouts.app')

@section('title', 'Comunicados')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Comunicados</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Comunicados</h1>
            <p class="text-sm text-gray-500">Gestiona los comunicados institucionales</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-lg shadow-primary-500/25">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo comunicado
        </a>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 animate-fade-in-up">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-400">Total</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['published'] }}</p>
                    <p class="text-xs text-gray-400">Publicados</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['drafts'] }}</p>
                    <p class="text-xs text-gray-400">Borradores</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['thisWeek'] }}</p>
                    <p class="text-xs text-gray-400">Esta semana</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Announcements grid --}}
    <div class="space-y-3 animate-fade-in-up delay-1">
        @forelse($announcements as $ann)
        @php
            $isPublished = $ann->published_at && $ann->published_at->isPast();
            $isNew = $isPublished && $ann->published_at->gt(now()->subDays(3));
            $roleBadge = match($ann->target_role) {
                'all'     => ['label' => 'Todos',    'class' => 'bg-blue-100 text-blue-700',    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                'docente' => ['label' => 'Docentes', 'class' => 'bg-violet-100 text-violet-700', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2M12 14v.01'],
                'alumno'  => ['label' => 'Alumnos',  'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z'],
                'admin'   => ['label' => 'Admin',    'class' => 'bg-red-100 text-red-700',      'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                default   => ['label' => 'Todos',    'class' => 'bg-blue-100 text-blue-700',    'icon' => ''],
            };
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200 group">
            <div class="flex items-stretch">
                {{-- Thumbnail --}}
                @if($ann->image_path && $ann->hasValidImage())
                <div class="w-32 sm:w-40 shrink-0 hidden sm:block">
                    <img src="{{ $ann->image_url }}" alt="{{ $ann->title }}" class="w-full h-full object-cover">
                </div>
                @endif

                {{-- Content --}}
                <div class="flex-1 p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                @if(!$isPublished)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Borrador
                                </span>
                                @elseif($isNew)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Nuevo
                                </span>
                                @endif
                                @if($ann->is_popup)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Emergente
                                </span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $roleBadge['class'] }}">
                                    {{ $roleBadge['label'] }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 leading-snug group-hover:text-primary-700 transition-colors">{{ $ann->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($ann->content), 120) }}</p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.announcements.edit', $ann) }}" title="Editar"
                               class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" data-confirm="¿Eliminar este comunicado?">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @if($ann->published_at)
                                {{ $ann->published_at->format('d/m/Y H:i') }}
                            @else
                                <span class="italic">Sin fecha</span>
                            @endif
                        </div>
                        @if($ann->author)
                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $ann->author->name }}
                        </div>
                        @endif
                        @if($ann->image_path && $ann->hasValidImage())
                        <div class="flex items-center gap-1 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Con imagen
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-primary-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Sin comunicados</h3>
            <p class="text-xs text-gray-400 mb-4">Crea el primer comunicado para compartir con la comunidad.</p>
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Crear comunicado
            </a>
        </div>
        @endforelse
    </div>

    @if($announcements->hasPages())
    <div class="bg-white rounded-xl border border-gray-200 p-4">{{ $announcements->links() }}</div>
    @endif

</div>
@endsection
