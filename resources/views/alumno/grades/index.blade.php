@extends('layouts.app')

@section('title', 'Mis calificaciones – ' . $course->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Cabecera --}}
    <div>
        <a href="{{ route('alumno.courses.show', $course) }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 mb-1 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            {{ $course->name }}
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Mis calificaciones</h1>
    </div>

    @if($items->isEmpty())
        {{-- Sin ítems todavía --}}
        <div class="card text-center py-16">
            <div class="mx-auto w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm">Aún no hay ítems de calificación para este curso.</p>
        </div>

    @else
        {{-- Tarjeta de promedio --}}
        @php
            $totalWeight = $items->sum('weight');
            $useWeighted = $totalWeight > 0;
            $wSum = 0; $wW = 0; $sSum = 0; $sC = 0;
            foreach ($items as $itm) {
                $g = $grades->get($itm->id);
                if (! $g || $g->score === null) continue;
                $norm = (min((float) $g->score, (float) $itm->max_score) / $itm->max_score) * 20.0;
                if ($useWeighted && $itm->weight > 0) { $wSum += $norm * $itm->weight; $wW += $itm->weight; }
                $sSum += $norm; $sC++;
            }
            $viewAvg = null;
            if ($useWeighted && $wW > 0) $viewAvg = round($wSum / $wW, 1);
            elseif ($sC > 0)             $viewAvg = round($sSum / $sC, 1);

            $avgBg  = 'bg-gray-100';
            $avgTxt = 'text-gray-500';
            $avgLbl = 'Sin notas aún';
            if ($viewAvg !== null) {
                if ($viewAvg < 11)      { $avgBg = 'bg-red-50';     $avgTxt = 'text-red-600';     $avgLbl = 'Desaprobado'; }
                elseif ($viewAvg < 14)  { $avgBg = 'bg-amber-50';   $avgTxt = 'text-amber-600';   $avgLbl = 'Regular'; }
                else                    { $avgBg = 'bg-emerald-50';  $avgTxt = 'text-emerald-700'; $avgLbl = 'Aprobado'; }
            }
        @endphp

        <div class="{{ $avgBg }} rounded-2xl p-6 flex items-center gap-6 border border-opacity-50 shadow-sm">
            <div class="text-center">
                <div class="text-5xl font-extrabold {{ $avgTxt }} tabular-nums leading-none">
                    {{ $viewAvg !== null ? number_format($viewAvg, 1) : '—' }}
                </div>
                <div class="text-xs text-gray-500 mt-1">/ 20</div>
            </div>
            <div>
                <div class="text-lg font-bold {{ $avgTxt }}">{{ $avgLbl }}</div>
                <div class="text-sm text-gray-500 mt-0.5">
                    Promedio {{ $useWeighted ? 'ponderado' : 'simple' }} de
                    {{ $sC }} nota{{ $sC !== 1 ? 's' : '' }} registrada{{ $sC !== 1 ? 's' : '' }}
                    / {{ $items->count() }} total
                </div>
                <div class="text-xs text-gray-400 mt-1">
                    Curso: <span class="font-medium text-gray-600">{{ $course->name }}</span>
                </div>
            </div>
        </div>

        {{-- Lista de ítems y notas --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">
                            Ítem de evaluación
                        </th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide w-28">
                            Nota
                        </th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide w-28 hidden sm:table-cell">
                            Máximo
                        </th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide w-28 hidden sm:table-cell">
                            Peso
                        </th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide w-36 hidden md:table-cell">
                            Fecha
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($items as $item)
                    @php $grade = $grades->get($item->id); @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        {{-- Nombre del ítem --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $item->type_badge_class }} shrink-0">
                                    {{ $item->type_label }}
                                </span>
                                <span class="font-medium text-gray-800">{{ $item->name }}</span>
                            </div>
                            @if($grade?->comments)
                                <p class="text-xs text-gray-400 mt-0.5 ml-14">{{ $grade->comments }}</p>
                            @endif
                        </td>

                        {{-- Nota --}}
                        <td class="px-4 py-3.5 text-center">
                            @if($grade?->score !== null)
                                @php
                                    $norm = (min((float) $grade->score, (float) $item->max_score) / $item->max_score) * 20;
                                    $cls  = $norm < 11 ? 'bg-red-100 text-red-700'
                                          : ($norm < 14 ? 'bg-amber-100 text-amber-700'
                                          : 'bg-emerald-100 text-emerald-700');
                                @endphp
                                <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg text-sm font-bold {{ $cls }}">
                                    {{ number_format($grade->score, 1) }}
                                </span>
                            @else
                                <span class="text-gray-300 text-lg font-light">—</span>
                            @endif
                        </td>

                        {{-- Máximo --}}
                        <td class="px-4 py-3.5 text-center text-gray-400 text-xs hidden sm:table-cell">
                            {{ number_format($item->max_score, 0) }}
                        </td>

                        {{-- Peso --}}
                        <td class="px-4 py-3.5 text-center text-gray-400 text-xs hidden sm:table-cell">
                            {{ $item->weight > 0 ? $item->weight . '%' : '—' }}
                        </td>

                        {{-- Fecha calificación --}}
                        <td class="px-5 py-3.5 text-right text-xs text-gray-400 hidden md:table-cell">
                            @if($grade?->graded_at)
                                <time datetime="{{ $grade->graded_at->toDateString() }}"
                                      title="{{ $grade->graded_at->format('d/m/Y H:i') }}">
                                    {{ $grade->graded_at->diffForHumans() }}
                                </time>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
