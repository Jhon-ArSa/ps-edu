@extends('layouts.app')

@section('title', 'Supervisión de Foros')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Supervisión de Foros</span>
@endsection

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Supervisión de Foros</h1>
            <p class="text-sm text-gray-500">{{ $topics->total() }} tema{{ $topics->total() !== 1 ? 's' : '' }} en todos los cursos</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tema</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Curso</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Autor</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Resp.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Fecha</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topics as $topic)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                @if($topic->is_pinned)
                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6z"/>
                                    </svg>
                                @endif
                                <p class="font-medium text-gray-900 line-clamp-1">{{ $topic->title }}</p>
                            </div>
                            <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">{{ Str::limit(strip_tags($topic->body), 70) }}</p>
                        </td>
                        <td class="px-5 py-3 hidden sm:table-cell">
                            <span class="text-xs font-medium text-primary-700 bg-primary-50 px-2 py-0.5 rounded-full">
                                {{ $topic->course->code ?? '—' }}
                            </span>
                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($topic->course->name ?? '', 30) }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-700 hidden md:table-cell">
                            {{ $topic->author->name ?? 'Desconocido' }}
                            <p class="text-xs text-gray-400">{{ ucfirst($topic->author->role ?? '') }}</p>
                        </td>
                        <td class="px-5 py-3 text-center text-gray-600 hidden lg:table-cell">
                            {{ $topic->replies_count }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 hidden lg:table-cell">
                            {{ $topic->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($topic->is_closed)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Cerrado</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Activo</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            No hay temas en ningún foro aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($topics->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $topics->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
