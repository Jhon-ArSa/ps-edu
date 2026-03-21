@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('breadcrumb')
    <a href="{{ route('admin.support.index') }}" class="hover:text-primary-600">Soporte</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Ticket #{{ $ticket->id }}</span>
@endsection

@section('content')
<div class="space-y-5" x-data="ticketManager()">

    {{-- ═══════════════════════════════════════════════════════════════════════
         TICKET HEADER
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-xl font-bold">{{ $ticket->subject }}</h1>
                    @php
                        $statusBadge = $ticket->status_badge;
                        $priorityBadge = $ticket->priority_badge;
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                        {{ $statusBadge['label'] }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                        {{ $priorityBadge['label'] }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-blue-100 text-sm">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $ticket->user->name }} ({{ $ticket->user->email }})
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        {{ $ticket->category_label }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">#{{{ $ticket->id }}</div>
                @if($ticket->assignedTo)
                <div class="text-blue-200 text-sm mt-1">Asignado a {{ $ticket->assignedTo->name }}</div>
                @else
                <div class="text-blue-200 text-sm mt-1">Sin asignar</div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- ═══════════════════════════════════════════════════════════════════════
             TICKET CONTENT
        ═══════════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Original message --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Mensaje Original
                </h2>
                <div class="prose prose-sm max-w-none">
                    <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">{{ $ticket->message }}</div>
                </div>
            </div>

            {{-- Admin notes --}}
            @if($ticket->admin_notes)
            <div class="bg-amber-50 rounded-xl border border-amber-200 p-6">
                <h3 class="text-lg font-semibold text-amber-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Notas Administrativas
                </h3>
                <div class="whitespace-pre-wrap text-amber-800 leading-relaxed">{{ $ticket->admin_notes }}</div>
            </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════════
             MANAGEMENT PANEL
        ═══════════════════════════════════════════════════════════════════════ --}}
        <div class="space-y-5">
            {{-- Quick actions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                    @if($ticket->status !== 'resolved')
                    <form method="POST" action="{{ route('admin.support.update-status', $ticket) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Marcar como Resuelto
                        </button>
                    </form>
                    @endif

                    @if($ticket->status !== 'closed')
                    <form method="POST" action="{{ route('admin.support.update-status', $ticket) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="closed">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cerrar Ticket
                        </button>
                    </form>
                    @endif

                    @if($ticket->status === 'closed')
                    <form method="POST" action="{{ route('admin.support.update-status', $ticket) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="open">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                            Reabrir Ticket
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Update form --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestionar Ticket</h3>
                <form method="POST" action="{{ route('admin.support.update', $ticket) }}" class="space-y-4">
                    @csrf @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Abierto</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>En progreso</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resuelto</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label>
                        <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Baja</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asignar a</label>
                        <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sin asignar</option>
                            @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notas administrativas</label>
                        <textarea name="admin_notes" rows="4"
                                  placeholder="Agregar notas internas sobre el manejo de este ticket..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Actualizar Ticket
                    </button>
                </form>
            </div>

            {{-- Ticket info --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Ticket</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Creado:</span>
                        <span class="font-medium">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Actualizado:</span>
                        <span class="font-medium">{{ $ticket->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($ticket->resolved_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Resuelto:</span>
                        <span class="font-medium text-green-600">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Usuario:</span>
                        <span class="font-medium">{{ $ticket->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email:</span>
                        <span class="font-medium">{{ $ticket->user->email }}</span>
                    </div>
                    @if($ticket->user->alumnoProfile?->student_code)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Código estudiante:</span>
                        <span class="font-medium">{{ $ticket->user->alumnoProfile->student_code }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function ticketManager() {
    return {
        // Future enhancements can go here
    };
}
</script>
@endpush
@endsection