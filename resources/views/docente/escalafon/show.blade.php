@extends('layouts.app')

@section('title', 'Mi Escalafón')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Escalafón</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Mi Escalafón</h1>
            <p class="text-sm text-gray-500">Información académica y profesional</p>
        </div>
        <a href="{{ route('docente.escalafon.edit') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
    </div>

    {{-- Personal info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-16 h-16 rounded-full overflow-hidden bg-primary-100 border-2 border-primary-200 shrink-0">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-primary-600 text-xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">
                    {{ $profile->title ? $profile->title.' ' : '' }}{{ $user->name }}
                </h2>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
            </div>
        </div>

        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-400">Grado académico</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $profile->degree ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Especialidad</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $profile->specialty ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Categoría docente</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $profile->category ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Años de servicio</dt>
                <dd class="font-medium text-gray-900 mt-0.5">
                    {{ $profile->years_of_service !== null ? $profile->years_of_service.' años' : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">DNI</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $user->dni ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Teléfono</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $user->phone ?? '—' }}</dd>
            </div>
        </dl>

        @if($profile->bio)
        <div class="pt-4 mt-4 border-t border-gray-100">
            <dt class="text-gray-400 text-sm mb-2">Perfil profesional</dt>
            <dd class="text-sm text-gray-700 leading-relaxed">{{ $profile->bio }}</dd>
        </div>
        @endif
    </div>

    @if(!$profile->degree && !$profile->specialty)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
        Su perfil profesional está incompleto.
        <a href="{{ route('docente.escalafon.edit') }}" class="font-semibold underline">Complete su información</a>
        para que aparezca en el directorio institucional.
    </div>
    @endif

</div>
@endsection
