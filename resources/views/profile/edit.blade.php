@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('breadcrumb')
    <span class="text-gray-400">Mi Perfil</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Avatar Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Foto de perfil</h3>
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-full overflow-hidden bg-primary-100 shrink-0 border-2 border-primary-200">
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover" id="avatar-preview">
                @else
                    <div id="avatar-preview" class="w-full h-full flex items-center justify-center text-primary-600 text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data"
                      x-data="{ file: null }" @change.prevent="
                            const f = $event.target.files[0];
                            if(f){ file = f; $refs.form.submit(); }
                      " x-ref="form">
                    @csrf
                    <label class="cursor-pointer inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Cambiar foto
                        <input type="file" name="avatar" accept="image/*" class="sr-only">
                    </label>
                </form>
                <p class="text-xs text-gray-400 mt-1.5">JPG, PNG o GIF. Máximo 2MB.</p>
            </div>
        </div>
    </div>

    {{-- Personal Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Información personal</h3>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">El correo no se puede modificar.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                    <input type="text" name="dni" value="{{ old('dni', $user->dni) }}" maxlength="20"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
                           placeholder="12345678">
                    @error('dni') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
                           placeholder="999 999 999">
                </div>
            </div>

            {{-- Docente extra fields --}}
            @if($user->isDocente())
                @php $dp = $user->docenteProfile; @endphp
                <hr class="border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700">Perfil profesional</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título (Ej: Dr., Mg.)</label>
                        <input type="text" name="title" value="{{ old('title', $dp?->title) }}" maxlength="20"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grado académico</label>
                        <input type="text" name="degree" value="{{ old('degree', $dp?->degree) }}"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                        <input type="text" name="specialty" value="{{ old('specialty', $dp?->specialty) }}"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <input type="text" name="category" value="{{ old('category', $dp?->category) }}"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Años de servicio</label>
                        <input type="number" name="years_of_service" value="{{ old('years_of_service', $dp?->years_of_service) }}" min="0" max="60"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biografía</label>
                    <textarea name="bio" rows="3" maxlength="2000"
                              class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent resize-none">{{ old('bio', $dp?->bio) }}</textarea>
                </div>
            @endif

            {{-- Alumno extra fields --}}
            @if($user->isAlumno())
                @php $ap = $user->alumnoProfile; @endphp
                <hr class="border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700">Datos académicos</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de alumno</label>
                        <input type="text" name="code" value="{{ old('code', $ap?->code) }}" maxlength="30"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Año de promoción</label>
                        <input type="number" name="promotion_year" value="{{ old('promotion_year', $ap?->promotion_year) }}"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programa</label>
                        <input type="text" name="program" value="{{ old('program', $ap?->program) }}"
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Change password --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Cambiar contraseña</h3>
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                <input type="password" name="current_password"
                       class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2.5 rounded-lg border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent"
                           placeholder="Mínimo 8 caracteres">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
