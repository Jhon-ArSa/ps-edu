@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Mi Perfil</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════════════
         PROFILE HERO HEADER
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="relative bg-gradient-to-br from-primary-700 via-primary-800 to-indigo-900 rounded-2xl overflow-hidden shadow-lg">
        <div class="absolute inset-0 opacity-[0.04]" style="background-image:url(&quot;data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='1' fill-rule='evenodd'%3E%3Cpath d='M0 38.59l2.83-2.83 1.41 1.41L1.41 40H0v-1.41zM0 1.4l2.83 2.83 1.41-1.41L1.41 0H0v1.41zM38.59 40l-2.83-2.83 1.41-1.41L40 38.59V40h-1.41zM40 1.41l-2.83 2.83-1.41-1.41L38.59 0H40v1.41zM20 18.6l2.83-2.83 1.41 1.41L21.41 20l2.83 2.83-1.41 1.41L20 21.41l-2.83 2.83-1.41-1.41L18.59 20l-2.83-2.83 1.41-1.41L20 18.59z'/%3E%3C/g%3E%3C/svg%3E&quot;)"></div>
        <div class="absolute -top-20 -right-20 w-56 h-56 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative px-6 py-7 sm:px-8">
            <div class="flex items-center gap-5">
                {{-- Avatar with upload --}}
                <div class="relative group shrink-0"
                     x-data="{ hovering: false }"
                     @mouseenter="hovering = true"
                     @mouseleave="hovering = false">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden ring-4 ring-white/20 shadow-lg bg-white/10">
                        @if($user->avatar)
                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover" id="avatar-preview">
                        @else
                        <div id="avatar-preview" class="w-full h-full flex items-center justify-center text-white text-3xl font-bold bg-gradient-to-br from-primary-500 to-indigo-600">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    {{-- Overlay for upload --}}
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data"
                          x-ref="avatarForm"
                          class="absolute inset-0">
                        @csrf
                        <label class="absolute inset-0 rounded-full cursor-pointer flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="file" name="avatar" accept="image/*" class="sr-only"
                                   @change="$refs.avatarForm.submit()">
                        </label>
                    </form>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-extrabold text-white leading-tight">{{ $user->name }}</h1>
                    <p class="text-primary-200/80 text-sm mt-0.5">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $user->status ? 'bg-emerald-400/20 text-emerald-200 ring-1 ring-emerald-400/30' : 'bg-red-400/20 text-red-200 ring-1 ring-red-400/30' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->status ? 'bg-emerald-400 animate-pulse' : 'bg-red-400' }}"></span>
                            {{ $user->status ? 'Activo' : 'Inactivo' }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs text-primary-200 bg-white/10 px-2.5 py-1 rounded-md font-semibold capitalize">
                            @if($user->isDocente())
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @elseif($user->isAlumno())
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                            @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @endif
                            {{ $user->role }}
                        </span>
                        @if($user->dni)
                        <span class="text-xs font-mono text-primary-200/80 bg-white/10 px-2.5 py-1 rounded-md">DNI: {{ $user->dni }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3.5 flex items-center gap-3" x-data x-init="setTimeout(() => $el.remove(), 4000)">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-emerald-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ═══════════════════════════════════════════════════════════════════
             LEFT COLUMN — Read-only personal info
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Personal information (read-only) --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Información personal</h3>
                    @if(!$user->isAdmin())
                    <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Solo lectura
                    </span>
                    @endif
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Nombre completo</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Correo electrónico</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100 truncate">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">DNI</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100 font-mono">{{ $user->dni ?: '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Rol</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100 capitalize">{{ $user->role }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Docente profile (read-only) --}}
            @if($user->isDocente())
            @php $dp = $user->docenteProfile; @endphp
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Perfil profesional</h3>
                    <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Solo lectura
                    </span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Título</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $dp?->title ?: '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Grado académico</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $dp?->degree ?: '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Especialidad</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $dp?->specialty ?: '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Categoría</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $dp?->category ?: '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Años de servicio</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $dp?->years_of_service ?? '—' }}</p>
                        </div>
                    </div>
                    @if($dp?->bio)
                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Biografía</label>
                        <p class="text-sm text-gray-700 bg-gray-50 px-3.5 py-3 rounded-lg border border-gray-100 leading-relaxed">{{ $dp->bio }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Alumno profile (read-only) --}}
            @if($user->isAlumno())
            @php $ap = $user->alumnoProfile; @endphp
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Datos académicos</h3>
                    <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Solo lectura
                    </span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Código de alumno</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100 font-mono">{{ $ap?->code ?: '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Año de promoción</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $ap?->promotion_year ?: '—' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Programa</label>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 px-3.5 py-2.5 rounded-lg border border-gray-100">{{ $ap?->program ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             RIGHT COLUMN — Editable fields (phone) + password
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="space-y-5">

            {{-- Phone update --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Teléfono</h3>
                    <span class="ml-auto text-xs text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full flex items-center gap-1 font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editable
                    </span>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Número de teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
                                   placeholder="999 999 999">
                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit"
                                class="w-full bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                            Guardar teléfono
                        </button>
                    </form>
                </div>
            </div>

            {{-- Change password --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50/80 to-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Contraseña</h3>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-3.5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Contraseña actual</label>
                            <input type="password" name="current_password"
                                   class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                            @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nueva contraseña</label>
                            <input type="password" name="password" placeholder="Mínimo 8 caracteres"
                                   class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                        </div>
                        <button type="submit"
                                class="w-full bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                            Actualizar contraseña
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info notice --}}
            <div class="bg-blue-50 rounded-xl border border-blue-100 p-4">
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-blue-800">¿Necesitas actualizar tus datos?</p>
                        <p class="text-xs text-blue-600 mt-0.5 leading-relaxed">Los datos personales y académicos solo pueden ser modificados por un administrador. Contacta a la secretaría si necesitas realizar cambios.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
