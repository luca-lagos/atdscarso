@props([
    'title' => 'Calendario',
    'events' => [],
    'scope' => null, // 'sala', 'tv', 'bib', null (alumnos)
    'selectedVar' => null, // nombre de la variable Alpine del padre
])

@php
    use Carbon\Carbon;

    $today = Carbon::today();
    $start = $today->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
    $end = $today->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

    $period = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
@endphp

<div {{ $attributes->merge(['class' => 'mini-cal']) }}>
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
                class="mini-cal__cell {{ $isOtherMonth ? 'is-other' : '' }} {{ $isToday ? 'is-today' : '' }}"
                data-date="{{ $iso }}" data-scope="{{ $scope }}"
                @click="$dispatch('mini-calendar-select', { date: '{{ $iso }}', scope: '{{ $scope }}' })"
                x-bind:class="[
                    'mini-cal__cell',
                    '{{ $isOtherMonth ? 'is-other' : '' }}',
                    '{{ $isToday ? 'is-today' : '' }}',
                    ({{ $selectedVar ?: 'null' }} === '{{ $iso }}') ?
                    (
                        '{{ $scope }}'
                        === 'sala' ?
                        'mini-cal__cell--selected-sala' :
                        ('{{ $scope }}'
                            === 'tv' ?
                            'mini-cal__cell--selected-tv' :
                            ('{{ $scope }}'
                                === 'bib' ?
                                'mini-cal__cell--selected-bib' :
                                'mini-cal__cell--selected'
                            )
                        )
                    ) :
                    ''
                ].join(' ')">
                <div class="mini-cal__day">{{ $cDate->day }}</div>
                @if ($count > 0)
                    <span class="mini-cal__badge">{{ $count }}</span>
                @endif
            </button>
        @endforeach
    </div>
</div>
