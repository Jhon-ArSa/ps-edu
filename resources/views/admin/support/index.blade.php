@extends('layouts.app')

@section('title', 'Gestión de Soporte')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Gestión de Soporte</span>
@endsection

@section('content')
<div class="space-y-5" x-data="supportManager()">

    {{-- ═══════════════════════════════════════════════════════════════════════
         HEADER + STATS
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Gestión de Soporte</h1>
                <p class="text-blue-100 text-sm mt-1">Administra tickets de estudiantes y docentes</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-blue-200 text-sm">Tickets totales</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         STATS CARDS
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['open'] }}</p>
                    <p class="text-xs text-gray-500">Abiertos</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
                    <p class="text-xs text-gray-500">En progreso</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['resolved'] }}</p>
                    <p class="text-xs text-gray-500">Resueltos</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['closed'] }}</p>
                    <p class="text-xs text-gray-500">Cerrados</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         FILTERS
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <form method="GET" class="space-y-4">
            <div class="flex items-center gap-4 flex-wrap">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Buscar por asunto, mensaje, usuario o email..."
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Buscar
                </button>
                <a href="{{ route('admin.support.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Limpiar
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abiertos</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En progreso</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resueltos</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrados</option>
                </select>

                <select name="category" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las categorías</option>
                    <option value="tecnico" {{ request('category') === 'tecnico' ? 'selected' : '' }}>Técnico</option>
                    <option value="academico" {{ request('category') === 'academico' ? 'selected' : '' }}>Académico</option>
                    <option value="acceso" {{ request('category') === 'acceso' ? 'selected' : '' }}>Acceso</option>
                    <option value="otro" {{ request('category') === 'otro' ? 'selected' : '' }}>Otro</option>
                </select>

                <select name="priority" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las prioridades</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baja</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Media</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>

                <select name="assigned_to" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los asignados</option>
                    <option value="0" {{ request('assigned_to') === '0' ? 'selected' : '' }}>Sin asignar</option>
                    @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>

                <a href="{{ route('admin.support.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar
                </a>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         TICKETS LIST
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($tickets->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($tickets as $ticket)
            @php
                $statusBadge = $ticket->status_badge;
                $priorityBadge = $ticket->priority_badge;
            @endphp
            <div class="p-5 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                <a href="{{ route('admin.support.show', $ticket) }}">{{ $ticket->subject }}</a>
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge['class'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $priorityBadge['class'] }}">
                                {{ $priorityBadge['label'] }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-xs text-gray-500 mb-2">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $ticket->user->name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $ticket->category_label }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $ticket->created_at->diffForHumans() }}
                            </span>
                            @if($ticket->assignedTo)
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                {{ $ticket->assignedTo->name }}
                            </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-600 line-clamp-2">{{ $ticket->message }}</p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.support.show', $ticket) }}"
                           class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">
                            Ver
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-3 bg-gray-50 border-t">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">No hay tickets</h3>
            <p class="text-xs text-gray-500">No se encontraron tickets de soporte con los filtros aplicados.</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function supportManager() {
    return {
        // Future enhancements can go here
    };
}
</script>
@endpush
@endsection