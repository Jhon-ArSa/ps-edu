@extends('layouts.app')

@section('title', 'Notificaciones')

@section('breadcrumb')
    <span class="text-gray-300">/</span>
    <span class="font-semibold text-gray-600">Notificaciones</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notificaciones</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                @php $unread = auth()->user()->unreadNotifications()->count() @endphp
                @if($unread > 0)
                    Tienes <span class="font-semibold text-primary-600">{{ $unread }}</span> sin leer
                @else
                    Todas las notificaciones leídas
                @endif
            </p>
        </div>

        @if($unread > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="btn-secondary btn-sm gap-1.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    Marcar todo como leído
                </button>
            </form>
        @endif
    </div>

    {{-- Lista de notificaciones --}}
    @if($notifications->count() > 0)
        <div class="card overflow-hidden">
            @foreach($notifications as $notification)
                @php $isUnread = is_null($notification->read_at); @endphp

                <div class="flex items-start gap-4 px-6 py-4 border-b border-gray-100 last:border-0 transition-colors {{ $isUnread ? 'bg-primary-50/40 hover:bg-primary-50/70' : 'hover:bg-gray-50/60' }}">

                    {{-- Ícono de tipo --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-primary-100' : 'bg-gray-100' }}">
                        @switch($notification->data['icon'] ?? 'default')
                            @case('task')
                                <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                    <rect x="9" y="2" width="6" height="4" rx="1"/>
                                    <path d="M9 14l2 2 4-4"/>
                                </svg>
                                @break
                            @case('grade')
                                <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                </svg>
                                @break
                            @case('evaluation')
                                <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                    <rect x="9" y="2" width="6" height="4" rx="1"/>
                                    <path d="M9 12h6M9 16h4"/>
                                </svg>
                                @break
                            @case('announcement')
                                <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.435 5.093A7.001 7.001 0 0111 4.176V5.882L5.435 5.093z"/>
                                </svg>
                                @break
                            @case('course')
                                <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3L1 9l11 6 11-6-11-6z"/>
                                    <path d="M5 12v5a7 7 0 0014 0v-5"/>
                                </svg>
                                @break
                                <svg class="w-5 h-5 {{ $isUnread ? 'text-primary-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.354 21c.705.622 1.632 1 2.646 1s1.94-.378 2.646-1M18 8a6 6 0 10-12 0c0 3.09-.78 5.206-1.65 6.605-.735 1.18-1.102 1.771-1.089 1.936.015.182.054.252.2.36.133.099.732.099 1.928.099H18.61c1.197 0 1.795 0 1.927-.098.147-.11.186-.179.2-.361.014-.165-.353-.756-1.088-1.936C18.78 13.206 18 11.09 18 8z"/>
                                </svg>
                        @endswitch
                    </div>

                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 leading-snug">
                                    {{ $notification->data['title'] ?? 'Notificación' }}
                                    @if($isUnread)
                                        <span class="inline-block w-2 h-2 bg-primary-500 rounded-full ml-1.5 align-middle"></span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600 mt-1 leading-snug">
                                    {{ $notification->data['body'] ?? '' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1.5 font-medium">
                                    {{ $notification->created_at->isoFormat('D [de] MMMM [a las] HH:mm') }}
                                    <span class="text-gray-300 mx-1">·</span>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if(isset($notification->data['url']))
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 {{ $isUnread ? 'bg-primary-600 text-white hover:bg-primary-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                            @if($isUnread)
                                                Ver
                                            @else
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                Ir
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>

    @else
        {{-- Empty state --}}
        <div class="card">
            <div class="card-body flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 animate-float">
                    <svg class="w-8 h-8 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                        <path d="M9.354 21c.705.622 1.632 1 2.646 1s1.94-.378 2.646-1M18 8a6 6 0 10-12 0c0 3.09-.78 5.206-1.65 6.605-.735 1.18-1.102 1.771-1.089 1.936.015.182.054.252.2.36.133.099.732.099 1.928.099H18.61c1.197 0 1.795 0 1.927-.098.147-.11.186-.179.2-.361.014-.165-.353-.756-1.088-1.936C18.78 13.206 18 11.09 18 8z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-700 mb-1">Todo al día</h3>
                <p class="text-sm text-gray-400 max-w-xs">
                    No tienes notificaciones por ahora. Aquí aparecerán alertas sobre tareas, calificaciones y comunicados.
                </p>
            </div>
        </div>
    @endif

</div>
@endsection
