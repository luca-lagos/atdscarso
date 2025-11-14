{{-- 
    Componente Mini Calendario
    Props:
    - title: string (default: 'Calendario')
    - events: array (formato: ['2025-11-14' => 3, '2025-11-20' => 1])
--}}

@props([
    'title' => 'Calendario',
    'events' => [],
])

@php
    use Carbon\Carbon;

    $today = Carbon::today();
    $start = $today->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
    $end = $today->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

    $period = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
@endphp

<div {{ $attributes->merge(['class' => 'mini-cal']) }} style="--mini-cal-accent: var(--scarso-primary);">
    <div class="mini-cal__header">
        <span class="mini-cal__title">{{ $title }}</span>
        <span class="mini-cal__month">{{ $today->translatedFormat('F Y') }}</span>
    </div>

    <div class="mini-cal__grid">
        @foreach (['D', 'L', 'M', 'M', 'J', 'V', 'S'] as $d)
            <div class="mini-cal__dow">{{ $d }}</div>
        @endforeach

        @foreach ($period as $date)
            @php
                // Convertir DateTime a Carbon
                $cDate = Carbon::instance($date);
                $iso = $cDate->format('Y-m-d');
                $count = $events[$iso] ?? 0;
                $isToday = $cDate->isToday();
                $isOtherMonth = $cDate->month !== $today->month;
            @endphp

            <div class="mini-cal__cell {{ $isOtherMonth ? 'is-other' : '' }} {{ $isToday ? 'is-today' : '' }}">
                <div class="mini-cal__day">{{ $cDate->day }}</div>
                @if ($count > 0)
                    <span class="mini-cal__badge">{{ $count }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
