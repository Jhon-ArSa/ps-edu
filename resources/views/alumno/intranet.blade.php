@extends('layouts.app')

@section('title', 'Intranet')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Intranet</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Intranet</h1>
        <p class="text-sm text-gray-500">Comunicados institucionales</p>
    </div>

    {{-- Anuncios como banners visuales --}}
    <div class="space-y-8">
        @forelse($announcements as $index => $ann)
            @if($index === 0)
                {{-- Primer anuncio como poster destacado --}}
                <div class="animate-fade-in-up">
                    <x-announcement-poster :announcement="$ann" />
                </div>
            @else
                {{-- Resto de anuncios como banners en grid --}}
                @if($index === 1)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @endif
                        <div class="animate-fade-in-up @if($index === 1) delay-1 @elseif($index === 2) delay-2 @elseif($index === 3) delay-3 @else delay-4 @endif">
                            <x-announcement-banner :announcement="$ann" size="default" />
                        </div>
                @if($loop->last && $index > 0)
                    </div>
                @endif
            @endif
        @empty
        {{-- Estado vacio --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-100 via-gray-50 to-white border-2 border-gray-200 shadow-xl">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-gray-200/30"></div>
            <div class="absolute top-4 right-4 w-6 h-6 rounded-full bg-gray-300/50"></div>
            <div class="absolute bottom-4 left-4 w-4 h-4 bg-gray-300/40" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);"></div>

            <div class="relative p-16 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-300 to-gray-400 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Sin comunicados disponibles</h3>
                <p class="text-gray-500 font-medium max-w-md mx-auto">Aun no hay comunicados publicados. Vuelve pronto para ver las novedades.</p>
            </div>
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
