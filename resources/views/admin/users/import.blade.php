@extends('layouts.app')

@section('title', 'Importar Usuarios')

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}" class="text-primary-600 hover:underline">Usuarios</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="font-semibold text-gray-700">Importar masivo</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900">Importación masiva de usuarios</h1>
        <p class="text-sm text-gray-500 mt-1">Suba un archivo CSV para crear múltiples usuarios de una sola vez.</p>
    </div>

    {{-- Instrucciones --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 space-y-3">
        <div class="flex items-center gap-2 text-blue-800 font-semibold text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Instrucciones
        </div>
        <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
            <li>El archivo debe estar en formato <strong>CSV</strong> (separado por comas).</li>
            <li>La primera fila debe contener los encabezados de columna.</li>
            <li>Los campos <strong>nombre, email, contraseña y rol</strong> son obligatorios.</li>
            <li>El campo <strong>rol</strong> acepta: <code class="bg-blue-100 px-1 rounded">admin</code>, <code class="bg-blue-100 px-1 rounded">docente</code> o <code class="bg-blue-100 px-1 rounded">alumno</code>.</li>
            <li>La contraseña debe tener al menos 8 caracteres.</li>
            <li>Emails o DNIs duplicados serán omitidos automáticamente.</li>
        </ul>
        <a href="{{ route('admin.users.import.template') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-900 underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar plantilla Excel (.xlsx)
        </a>
    </div>

    {{-- Columnas disponibles --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
            <p class="text-sm font-semibold text-gray-700">Columnas del CSV</p>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div class="space-y-2">
                    <p class="font-semibold text-gray-600 text-xs uppercase tracking-wide">Campos generales</p>
                    @foreach([
                        ['nombre','Nombre completo','requerido'],
                        ['email','Correo electrónico','requerido'],
                        ['contrasena','Contraseña (mín. 8 chars)','requerido'],
                        ['rol','Rol: admin / docente / alumno','requerido'],
                        ['dni','DNI o código de identidad','opcional'],
                        ['telefono','Teléfono','opcional'],
                    ] as [$col, $desc, $req])
                    <div class="flex items-start gap-2">
                        <code class="text-xs bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded font-mono flex-shrink-0">{{ $col }}</code>
                        <span class="text-gray-600">{{ $desc }}
                            @if($req === 'requerido')
                                <span class="text-red-500 text-xs">*</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
                <div class="space-y-4">
                    <div class="space-y-2">
                        <p class="font-semibold text-gray-600 text-xs uppercase tracking-wide">Solo para docentes</p>
                        @foreach([
                            ['titulo','Título (Mg., Dr., etc.)'],
                            ['grado','Grado académico'],
                            ['especialidad','Especialidad'],
                            ['categoria','Categoría docente'],
                            ['anios_servicio','Años de servicio'],
                        ] as [$col, $desc])
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded font-mono flex-shrink-0">{{ $col }}</code>
                            <span class="text-gray-600">{{ $desc }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="space-y-2">
                        <p class="font-semibold text-gray-600 text-xs uppercase tracking-wide">Solo para alumnos</p>
                        @foreach([
                            ['codigo_alumno','Código de matrícula'],
                            ['anio_promocion','Año de promoción'],
                            ['programa','Programa / mención'],
                        ] as [$col, $desc])
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-green-50 text-green-700 px-1.5 py-0.5 rounded font-mono flex-shrink-0">{{ $col }}</code>
                            <span class="text-gray-600">{{ $desc }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario de carga --}}
    <form method="POST" action="{{ route('admin.users.import.store') }}" enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        @csrf

        @error('csv_file')
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
            {{ $message }}
        </div>
        @enderror

        {{-- Drop zone --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Archivo CSV</label>
            <label for="csv_file"
                   class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-primary-400 transition-colors group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-primary-500 mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm text-gray-500 group-hover:text-primary-600 transition-colors" id="file-label">
                    <span class="font-semibold">Haga clic para seleccionar</span> o arrastre su CSV aquí
                </p>
                <p class="text-xs text-gray-400 mt-1">Excel (.xlsx) o CSV · máximo 5 MB</p>
                <input id="csv_file" name="csv_file" type="file" accept=".csv,.xlsx,.xls" class="hidden"
                       onchange="document.getElementById('file-label').innerHTML = '<span class=\'font-semibold text-primary-600\'>' + this.files[0].name + '</span>'">
            </label>
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Importar usuarios
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                Cancelar
            </a>
        </div>
    </form>

</div>
@endsection
