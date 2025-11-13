@props([
    'title' => 'Calendario',
    'events' => [],     {{-- ['2025-11-14' => 3, '2025-11-20' => 1] --}}
    'weeks' => 6,       {{-- 5 o 6 semanas visibles --}}
])

@php
  use Carbon\Carbon;

  $today = Carbon::today();
  $start = $today->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
  $end   = $today->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
  $period = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
@endphp

<div class="mini-cal" style="--mini-cal-accent: var(--scarso-primary);">
  <div class="mini-cal__header">
    <span class="mini-cal__title">{{ $title }}</span>
    <span class="mini-cal__month">{{ $today->translatedFormat('F Y') }}</span>
  </div>
  <div class="mini-cal__grid">
    @foreach (['D','L','M','M','J','V','S'] as $d)
      <div class="mini-cal__dow">{{ $d }}</div>
    @endforeach

    @foreach ($period as $date)
      @php
        $iso = $date->format('Y-m-d');
        $count = $events[$iso] ?? 0;
        $isToday = $date->isToday();
        $isOtherMonth = $date->month !== $today->month;
      @endphp
      <div class="mini-cal__cell {{ $isOtherMonth ? 'is-other' : '' }} {{ $isToday ? 'is-today' : '' }}">
        <div class="mini-cal__day">{{ $date->day }}</div>
        @if ($count > 0)
          <span class="mini-cal__badge">{{ $count }}</span>
        @endif
      </div>
    @endforeach
  </div>
</div>