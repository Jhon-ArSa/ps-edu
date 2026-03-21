@extends('layouts.app')

@section('title', 'Soporte Técnico')

@section('breadcrumb')
    <span class="font-semibold text-gray-700">Soporte Técnico</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="text-center">
        <div class="mx-auto w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mb-3">
            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Centro de Soporte Técnico</h1>
        <p class="text-sm text-gray-500 mt-1">¿Tienes algún problema o consulta? Estamos aquí para ayudarte.</p>
    </div>

    {{-- Formulario --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-white border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Enviar solicitud de soporte</h2>
            <p class="text-xs text-gray-500 mt-0.5">Completa el formulario y te responderemos lo antes posible</p>
        </div>

        <form method="POST" action="{{ route('alumno.soporte.send') }}" class="p-6 space-y-5">
            @csrf

            {{-- Categoría --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de solicitud <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        'tecnico'   => ['label' => 'Problema técnico', 'icon' => 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085', 'desc' => 'Errores, fallos del sistema'],
                        'acceso'    => ['label' => 'Acceso / Contraseña', 'icon' => 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z', 'desc' => 'No puedo ingresar'],
                        'academico' => ['label' => 'Consulta académica', 'icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5', 'desc' => 'Cursos, notas, materiales'],
                        'otro'      => ['label' => 'Otro', 'icon' => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z', 'desc' => 'Otras consultas'],
                    ] as $value => $cat)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="category" value="{{ $value }}" {{ old('category') === $value ? 'checked' : '' }}
                               class="peer sr-only" required>
                        <div class="border border-gray-200 rounded-xl p-3 hover:border-primary-300 hover:bg-primary-50/30 transition-all
                                    peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:ring-2 peer-checked:ring-primary-200">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400 peer-checked:text-primary-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cat['icon'] }}"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">{{ $cat['label'] }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 pl-7">{{ $cat['desc'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Asunto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Asunto <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('subject') ? 'border-red-400 ring-1 ring-red-200' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-colors"
                       placeholder="Ej: No puedo descargar los materiales del curso...">
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Mensaje --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Descripción del problema <span class="text-red-500">*</span>
                </label>
                <textarea name="message" rows="5" required maxlength="2000"
                          class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('message') ? 'border-red-400 ring-1 ring-red-200' : 'border-gray-300' }} text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-colors resize-none"
                          placeholder="Describe tu problema o consulta con el mayor detalle posible. Incluye qué estabas haciendo cuando ocurrió el error, qué mensaje de error viste, etc.">{{ old('message') }}</textarea>
                <div class="flex justify-between mt-1">
                    @error('message') <p class="text-xs text-red-600">{{ $message }}</p> @else <span></span> @enderror
                    <p class="text-xs text-gray-400">Máximo 2000 caracteres</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">
                    <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    Campos obligatorios marcados con *
                </p>
                <button type="submit"
                        class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition-all">
                    <svg class="w-4 h-4 inline -mt-0.5 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                    </svg>
                    Enviar solicitud
                </button>
            </div>
        </form>
    </div>

    {{-- Info box --}}
    <div class="bg-gradient-to-br from-primary-50 to-blue-50 border border-primary-100 rounded-xl p-5">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-primary-800 mb-1">Tiempo de respuesta</h3>
                <p class="text-sm text-primary-700 leading-relaxed">
                    Nuestro equipo de soporte revisará tu solicitud y te responderá a través de tu correo institucional
                    <strong class="font-medium">{{ auth()->user()->email }}</strong> en un plazo máximo de 24-48 horas hábiles.
                </p>
            </div>
        </div>
    </div>

    {{-- FAQ rápido --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800 text-sm">Preguntas frecuentes</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach([
                ['q' => '¿Cómo recupero mi contraseña?', 'a' => 'En la pantalla de inicio de sesión, haz clic en "¿Olvidaste tu contraseña?" y sigue las instrucciones.'],
                ['q' => '¿Por qué no puedo ver mis cursos?', 'a' => 'Verifica que estés matriculado en el semestre actual. Si el problema persiste, envía una solicitud de soporte.'],
                ['q' => '¿Cómo descargo los materiales del curso?', 'a' => 'Ingresa al curso, selecciona la semana correspondiente y haz clic en el material que deseas descargar.'],
            ] as $faq)
            <details class="group">
                <summary class="px-5 py-3 cursor-pointer text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center justify-between">
                    {{ $faq['q'] }}
                    <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <p class="px-5 pb-3 text-sm text-gray-500">{{ $faq['a'] }}</p>
            </details>
            @endforeach
        </div>
    </div>

</div>
@endsection
