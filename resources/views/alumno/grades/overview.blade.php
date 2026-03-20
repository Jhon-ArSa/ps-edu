@extends('layouts.app')

@section('title', 'Mis Notas')

@section('content')
<div class="space-y-6">

    {{-- Cabecera --}}
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Mis Notas</h1>
        <p class="text-gray-400 text-sm mt-1">Resumen de calificaciones por curso</p>
    </div>

    @if($courseData->isEmpty())
        <div class="card">
            <div class="p-16 text-center">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">No tienes cursos matriculados.</p>
            </div>
        </div>
    @else
        <div class="space-y-3">
            @foreach($courseData as $data)
            @php
                $avg     = $data['average'];
                $course  = $data['course'];

                if ($avg === null) {
                    $avgBg  = 'bg-gray-50';
                    $avgRing = 'ring-gray-200';
                    $avgTxt = 'text-gray-400';
                    $avgLbl = 'Sin notas';
                    $barW   = '0%';
                    $barClr = 'bg-gray-200';
                } elseif ($avg < 11) {
                    $avgBg  = 'bg-red-50';
                    $avgRing = 'ring-red-200';
                    $avgTxt = 'text-red-600';
                    $avgLbl = 'Desaprobado';
                    $barW   = ($avg / 20 * 100) . '%';
                    $barClr = 'bg-red-400';
                } elseif ($avg < 14) {
                    $avgBg  = 'bg-amber-50';
                    $avgRing = 'ring-amber-200';
                    $avgTxt = 'text-amber-600';
                    $avgLbl = 'Regular';
                    $barW   = ($avg / 20 * 100) . '%';
                    $barClr = 'bg-amber-400';
                } else {
                    $avgBg  = 'bg-emerald-50';
                    $avgRing = 'ring-emerald-200';
                    $avgTxt = 'text-emerald-700';
                    $avgLbl = 'Aprobado';
                    $barW   = ($avg / 20 * 100) . '%';
                    $barClr = 'bg-emerald-500';
                }
            @endphp

            <div class="card overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center gap-4">

                        {{-- Promedio badge --}}
                        <div class="ring-2 {{ $avgRing }} {{ $avgBg }} w-16 h-16 rounded-2xl flex flex-col items-center justify-center shrink-0">
                            <span class="text-2xl font-extrabold {{ $avgTxt }} tabular-nums leading-none">
                                {{ $avg !== null ? number_format($avg, 1) : '—' }}
                            </span>
                            <span class="text-[10px] text-gray-400 mt-0.5">/&nbsp;20</span>
                        </div>

                        {{-- Info del curso --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-800 truncate leading-snug">{{ $course->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $course->code }}</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                    {{ $avg === null ? 'bg-gray-100 text-gray-500' : ($avg < 11 ? 'bg-red-100 text-red-700' : ($avg < 14 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700')) }}">
                                    {{ $avgLbl }}
                                </span>
                            </div>

                            {{-- Barra de progreso --}}
                            <div class="mt-2.5 mb-2">
                                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $barClr }} rounded-full transition-all" style="width: {{ $barW }}"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex gap-3 text-xs text-gray-400">
                                    <span>{{ $data['graded'] }} / {{ $data['items'] }} notas registradas</span>
                                    @if($course->teacher)
                                        <span class="hidden sm:inline">· {{ $course->teacher->name }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('alumno.grades.show', $course) }}"
                                   class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors inline-flex items-center gap-1">
                                    Ver detalle
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Leyenda --}}
        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 px-1">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-400"></span> Desaprobado (0–10)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Regular (11–13)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Aprobado (14–20)</span>
        </div>
    @endif

</div>
@endsection
