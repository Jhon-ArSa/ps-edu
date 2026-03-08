@extends('layouts.app')

@section('title', 'Editar: ' . $user->name)

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}" class="hover:text-primary-600">Usuarios</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.users.show', $user) }}" class="hover:text-primary-600">{{ $user->name }}</a>
    <svg class="w-3.5 h-3.5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-700 font-medium">Editar</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ role: '{{ old('role', $user->role) }}', showPassword: false, showPasswordConfirm: false }">

    {{-- Header con info del usuario --}}
    <div class="flex items-center gap-4 mb-6 animate-fade-in-up">
        <div class="w-12 h-12 rounded-full overflow-hidden bg-primary-100 shrink-0">
            @if($user->avatar)
                <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover" alt="">
            @else
                <div class="w-full h-full flex items-center justify-center text-primary-600 text-lg font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar usuario</h1>
            <p class="text-sm text-gray-500">Modifica los datos de <strong>{{ $user->name }}</strong> · Registrado el {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ── Sección 1: Tipo de usuario ──────────────────────────────────── --}}
        <div class="card animate-fade-in-up delay-1">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Tipo de usuario</h2>
                        <p class="text-xs text-gray-500">Rol actual del usuario en el sistema</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="relative cursor-pointer" @click="role = 'alumno'">
                        <input type="radio" name="role" value="alumno" x-model="role" class="peer sr-only" required>
                        <div class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50/50
                                    border-gray-200 hover:border-gray-300 hover:bg-gray-50/50">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-200"
                                 :class="role === 'alumno' ? 'bg-blue-100' : 'bg-gray-100'">
                                <svg class="w-5 h-5 transition-colors duration-200" :class="role === 'alumno' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Alumno</p>
                                <p class="text-xs text-gray-500">Estudiante de posgrado</p>
                            </div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer" @click="role = 'docente'">
                        <input type="radio" name="role" value="docente" x-model="role" class="peer sr-only">
                        <div class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200
                                    peer-checked:border-violet-500 peer-checked:bg-violet-50/50
                                    border-gray-200 hover:border-gray-300 hover:bg-gray-50/50">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-200"
                                 :class="role === 'docente' ? 'bg-violet-100' : 'bg-gray-100'">
                                <svg class="w-5 h-5 transition-colors duration-200" :class="role === 'docente' ? 'text-violet-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Docente</p>
                                <p class="text-xs text-gray-500">Profesor del programa</p>
                            </div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer" @click="role = 'admin'">
                        <input type="radio" name="role" value="admin" x-model="role" class="peer sr-only">
                        <div class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200
                                    peer-checked:border-red-500 peer-checked:bg-red-50/50
                                    border-gray-200 hover:border-gray-300 hover:bg-gray-50/50">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-200"
                                 :class="role === 'admin' ? 'bg-red-100' : 'bg-gray-100'">
                                <svg class="w-5 h-5 transition-colors duration-200" :class="role === 'admin' ? 'text-red-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Administrador</p>
                                <p class="text-xs text-gray-500">Acceso total al sistema</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Sección 2: Información personal ─────────────────────────────── --}}
        <div class="card animate-fade-in-up delay-2">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Información personal</h2>
                        <p class="text-xs text-gray-500">Datos de identificación del usuario</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                    {{-- Nombre --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="form-input !pl-10 {{ $errors->has('name') ? '!border-red-400 !ring-red-500/20' : '' }}">
                        </div>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="form-label">Correo electrónico <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="form-input !pl-10 {{ $errors->has('email') ? '!border-red-400 !ring-red-500/20' : '' }}">
                        </div>
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- DNI --}}
                    <div>
                        <label for="dni" class="form-label">DNI</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                            </div>
                            <input type="text" id="dni" name="dni" value="{{ old('dni', $user->dni) }}" maxlength="20"
                                   class="form-input !pl-10 {{ $errors->has('dni') ? '!border-red-400 !ring-red-500/20' : '' }}">
                        </div>
                        @error('dni') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label for="phone" class="form-label">Teléfono</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20"
                                   class="form-input !pl-10">
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label for="status" class="form-label">Estado</label>
                        <select id="status" name="status" class="form-select">
                            <option value="1" {{ old('status', $user->status ? '1' : '0') === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('status', $user->status ? '1' : '0') === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sección 3: Cambiar contraseña (opcional) ─────────────────────── --}}
        <div class="card animate-fade-in-up delay-3">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Cambiar contraseña</h2>
                        <p class="text-xs text-gray-500">Deja los campos vacíos para mantener la contraseña actual</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                    <div>
                        <label for="password" class="form-label">Nueva contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" minlength="8"
                                   class="form-input !pl-10 !pr-10 {{ $errors->has('password') ? '!border-red-400 !ring-red-500/20' : '' }}"
                                   placeholder="Dejar en blanco para no cambiar">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input :type="showPasswordConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                                   class="form-input !pl-10 !pr-10"
                                   placeholder="Repite la nueva contraseña">
                            <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!showPasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPasswordConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sección 4: Perfil de Docente (condicional) ──────────────────── --}}
        @php $dp = $user->docenteProfile; @endphp
        <div x-show="role === 'docente'" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-2">
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Perfil profesional</h2>
                            <p class="text-xs text-gray-500">Datos académicos y profesionales del docente</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        <div>
                            <label for="title" class="form-label">Título</label>
                            <select id="title" name="title" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach(['Dr.', 'Dra.', 'Mg.', 'Lic.', 'Ing.', 'Prof.'] as $t)
                                    <option value="{{ $t }}" {{ old('title', $dp?->title) === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="degree" class="form-label">Grado académico</label>
                            <input type="text" id="degree" name="degree" value="{{ old('degree', $dp?->degree) }}"
                                   class="form-input" placeholder="Ej. Doctor en Educación">
                        </div>

                        <div>
                            <label for="specialty" class="form-label">Especialidad</label>
                            <input type="text" id="specialty" name="specialty" value="{{ old('specialty', $dp?->specialty) }}"
                                   class="form-input" placeholder="Ej. Didáctica y Currículum">
                        </div>

                        <div>
                            <label for="category" class="form-label">Categoría</label>
                            <select id="category" name="category" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach(['Principal', 'Asociado', 'Auxiliar', 'Contratado'] as $c)
                                    <option value="{{ $c }}" {{ old('category', $dp?->category) === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="years_of_service" class="form-label">Años de servicio</label>
                            <input type="number" id="years_of_service" name="years_of_service"
                                   value="{{ old('years_of_service', $dp?->years_of_service) }}"
                                   min="0" max="60" class="form-input" placeholder="0">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="bio" class="form-label">Biografía breve</label>
                            <textarea id="bio" name="bio" rows="3"
                                      class="form-input resize-none"
                                      placeholder="Descripción profesional del docente...">{{ old('bio', $dp?->bio) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sección 4: Perfil de Alumno (condicional) ───────────────────── --}}
        @php $ap = $user->alumnoProfile; @endphp
        <div x-show="role === 'alumno'" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-2">
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Datos académicos</h2>
                            <p class="text-xs text-gray-500">Información de matrícula y programa del alumno</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        <div>
                            <label for="code" class="form-label">Código de matrícula</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                </div>
                                <input type="text" id="code" name="code" value="{{ old('code', $ap?->code) }}" maxlength="50"
                                       class="form-input !pl-10 {{ $errors->has('code') ? '!border-red-400 !ring-red-500/20' : '' }}"
                                       placeholder="Ej. 2025-001">
                            </div>
                            @error('code') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="promotion_year" class="form-label">Año de promoción</label>
                            <input type="number" id="promotion_year" name="promotion_year"
                                   value="{{ old('promotion_year', $ap?->promotion_year) }}"
                                   min="2000" max="2099"
                                   class="form-input">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="program" class="form-label">Programa académico</label>
                            <select id="program" name="program" class="form-select">
                                <option value="">Seleccionar programa...</option>
                                @foreach([
                                    'Maestría en Ciencias de la Educación',
                                    'Maestría en Gestión Educativa',
                                    'Maestría en Docencia Universitaria',
                                    'Doctorado en Ciencias de la Educación',
                                    'Doctorado en Educación',
                                ] as $p)
                                    <option value="{{ $p }}" {{ old('program', $ap?->program) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Aviso para Administrador ────────────────────────────────────── --}}
        <div x-show="role === 'admin'" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-2">
            <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200/60">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-red-800">Cuenta de administrador</p>
                    <p class="text-xs text-red-600 mt-0.5">Este usuario tiene acceso completo al sistema: gestión de usuarios, cursos, matrículas, configuración y todos los módulos administrativos.</p>
                </div>
            </div>
        </div>

        {{-- ── Botones de acción ───────────────────────────────────────────── --}}
        <div class="flex items-center justify-between pt-2 animate-fade-in-up delay-4">
            <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancelar
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection
