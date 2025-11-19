@props([
    'title' => 'Calendario',
    'events' => [],
    'scope' => null, // 'sala', 'tv', 'bib', etc.
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
                $cDate = Carbon::instance($date);
                $iso = $cDate->format('Y-m-d');
                $count = $events[$iso] ?? 0;
                $isToday = $cDate->isToday();
                $isOtherMonth = $cDate->month !== $today->month;
            @endphp

            <button type="button"
                class="mini-cal__cell mini-cal__cell--scope-{{ $scope }} {{ $isOtherMonth ? 'is-other' : '' }} {{ $isToday ? 'is-today' : '' }}"
                data-date="{{ $iso }}" data-scope="{{ $scope }}"
                @click="$dispatch('mini-calendar-select', { date: '{{ $iso }}', scope: '{{ $scope }}' })"
                x-data="{
                    get isSelected() {
                        const data = this.$root.closest('[x-data]')?.__x?.$data
                        if (!data) return false
                        if ('{{ $scope }}' === 'sala') return data.selectedSalaDate === '{{ $iso }}'
                        if ('{{ $scope }}' === 'tv') return data.selectedTvDate === '{{ $iso }}'
                        if ('{{ $scope }}' === 'bib') return data.selectedBibDate === '{{ $iso }}'
                        return false
                    }
                }" :class="{ 'is-selected': isSelected }">
                <div class="mini-cal__day">{{ $cDate->day }}</div>
                @if ($count > 0)
                    <span class="mini-cal__badge">{{ $count }}</span>
                @endif
            </button>
        @endforeach
    </div>
</div>
