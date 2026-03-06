@extends('layouts.app')

@section('title', 'Comunicados')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Comunicados</span>
@endsection

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Comunicados</h1>
            <p class="text-sm text-gray-500">{{ $announcements->total() }} comunicados registrados</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}"
           class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo comunicado
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Título</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Destinatarios</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Publicado</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Autor</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($announcements as $ann)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-900">{{ $ann->title }}</p>
                            <p class="text-xs text-gray-400 line-clamp-1">{{ Str::limit(strip_tags($ann->content), 60) }}</p>
                        </td>
                        <td class="px-5 py-3 hidden sm:table-cell">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $ann->target_role === 'all' ? 'bg-blue-100 text-blue-700' :
                                   ($ann->target_role === 'docente' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ $ann->target_role === 'all' ? 'Todos' : ucfirst($ann->target_role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs hidden md:table-cell">
                            @if($ann->published_at)
                                {{ $ann->published_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-400 italic">No publicado</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 hidden lg:table-cell">{{ $ann->author->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.announcements.edit', $ann) }}" title="Editar"
                                   class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}"
                                      onsubmit="return confirm('¿Eliminar este comunicado?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Eliminar"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                            No hay comunicados registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $announcements->links() }}</div>
        @endif
    </div>

</div>
@endsection
