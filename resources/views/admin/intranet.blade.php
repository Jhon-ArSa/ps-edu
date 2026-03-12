@extends('layouts.app')

@section('title', 'Intranet')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Intranet</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Intranet</h1>
            <p class="text-sm text-gray-500">Comunicados institucionales</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nuevo comunicado
        </a>
    </div>

    @forelse($announcements as $ann)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($ann->image_path)
        <img src="{{ $ann->image_url }}" alt="{{ $ann->title }}" class="w-full h-48 object-cover">
        @endif
        <div class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base font-semibold text-gray-900 leading-tight">{{ $ann->title }}</h2>
                        @if($ann->published_at->gt(now()->subDays(3)))
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Nuevo</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Publicado {{ $ann->published_at->diffForHumans() }}
                        @if($ann->author) &middot; Por {{ $ann->author->name }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                        {{ $ann->target_role === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-violet-100 text-violet-700' }}">
                        {{ match($ann->target_role) {
                            'all'     => 'Todos',
                            'docente' => 'Docentes',
                            'alumno'  => 'Alumnos',
                            'admin'   => 'Admin',
                            default   => $ann->target_role,
                        } }}
                    </span>
                    <a href="{{ route('admin.announcements.edit', $ann) }}"
                       class="text-xs text-gray-400 hover:text-primary-600 transition-colors">Editar</a>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ann->content }}</div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <p class="text-gray-500 font-medium">No hay comunicados publicados</p>
        <a href="{{ route('admin.announcements.create') }}"
           class="mt-3 inline-flex items-center text-sm text-primary-600 hover:underline">
            Crear el primero
        </a>
    </div>
    @endforelse

    @if($announcements->hasPages())
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        {{ $announcements->links() }}
    </div>
    @endif

</div>
@endsection
