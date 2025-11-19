<x-filament-widgets::widget>
    <x-filament::section class="mini-calendar-section">
        <div class="self-panel space-y-6" x-data="{
            selectedSalaDate: null,
            selectedTvDate: null,
            selectedBibDate: null,
        }"
            @mini-calendar-select.window="
                if ($event.detail.scope === 'sala') {
                    selectedSalaDate = $event.detail.date
                } else if ($event.detail.scope === 'tv') {
                    selectedTvDate = $event.detail.date
                } else if ($event.detail.scope === 'bib') {
                    selectedBibDate = $event.detail.date
                }
            ">
            {{-- Encabezado --}}
            <div class="widget-header">
                <h2>Bienvenido, {{ auth()->user()->name }}</h2>
                <p>Desde aquí podés gestionar tus turnos de sala, televisores y préstamos de biblioteca.</p>
            </div>

            {{-- Grid de tarjetas (1 → 3 columnas responsivo) --}}
            <div class="grid-dashboard">
                {{-- Turnos de Sala --}}
                <div class="dashboard-card card-amber">
                    <h3 class="card-title">
                        📅 Próximos turnos de sala
                        <span x-show="selectedSalaDate" class="text-xs text-slate-200 font-normal">
                            · Día <span x-text="selectedSalaDate"></span>
                        </span>
                    </h3>

                    @if ($turnosSala->isEmpty())
                        <p class="text-muted">No tenés turnos próximos.</p>
                    @else
                        <ul class="list-compact" x-ref="listaSala">
                            @foreach ($turnosSala as $turno)
                                @php
                                    $fechaTurno =
                                        $turno->fecha_turno instanceof \Carbon\Carbon
                                            ? $turno->fecha_turno->toDateString()
                                            : $turno->fecha_turno;
                                @endphp

                                <li x-show="!selectedSalaDate || selectedSalaDate === '{{ $fechaTurno }}'">
                                    <span>📅</span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($turno->fecha_turno)->format('d/m') }} •
                                        {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}‑{{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        <p class="mt-1 text-x" style="color: black"
                            x-show="selectedSalaDate && !$refs.listaSala.querySelector('li[style*=\"display: none\"]') && !$refs.listaSala.querySelector('li:not([style*=\"display: none\"])')">
                            No hay turnos para la fecha seleccionada.
                        </p>
                    @endif

                    {{-- Mini calendario de turnos de sala --}}
                    <div class="mt-4">
                        <x-calendar-mini title="Resumen mensual" :events="$eventosSala" scope="sala" class="mt-3" />
                    </div>

                    <a class="btn-dashboard mt-3" href="{{ route('filament.docentes.resources.turnos-salas.index') }}">
                        Ver todos
                    </a>
                </div>

                {{-- Turnos TV --}}
                <div class="dashboard-card card-emerald">
                    <h3 class="card-title">
                        📺 Próximos turnos de TV
                        <span x-show="selectedTvDate" class="text-xs text-slate-200 font-normal">
                            · Día <span x-text="selectedTvDate"></span>
                        </span>
                    </h3>

                    @if ($turnosTv->isEmpty())
                        <p class="text-muted">No tenés turnos próximos.</p>
                    @else
                        <ul class="list-compact">
                            @foreach ($turnosTv as $tv)
                                @php
                                    $fechaTv =
                                        $tv->fecha_turno instanceof \Carbon\Carbon
                                            ? $tv->fecha_turno->toDateString()
                                            : $tv->fecha_turno;
                                @endphp

                                <li x-show="!selectedTvDate || selectedTvDate === '{{ $fechaTv }}'">
                                    <span>📺</span>
                                    <span>
                                        {{ $tv->inventario->nombre_equipo ?? 'TV' }} •
                                        {{ \Carbon\Carbon::parse($tv->fecha_turno)->format('d/m H:i') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Mini calendario de turnos de TV --}}
                    <div class="mt-4">
                        <x-calendar-mini title="Resumen mensual" :events="$eventosTv" scope="tv" class="mt-3" />
                    </div>

                    <a class="btn-dashboard mt-3" href="{{ route('filament.docentes.resources.turnos-tvs.index') }}">
                        Ver todos
                    </a>
                </div>

                {{-- Préstamos Biblioteca --}}
                <div class="dashboard-card card-slate">
                    <h3 class="card-title">
                        📚 Últimos préstamos de biblioteca
                        <span x-show="selectedBibDate" class="text-xs text-slate-200 font-normal">
                            · Día <span x-text="selectedBibDate"></span>
                        </span>
                    </h3>

                    @if ($prestamosBiblioteca->isEmpty())
                        <p class="text-muted">No registrás préstamos recientes.</p>
                    @else
                        <ul class="list-compact">
                            @foreach ($prestamosBiblioteca as $prestamo)
                                @php
                                    $fechaBib =
                                        $prestamo->fecha_prestamo instanceof \Carbon\Carbon
                                            ? $prestamo->fecha_prestamo->toDateString()
                                            : $prestamo->fecha_prestamo;
                                @endphp

                                <li x-show="!selectedBibDate || selectedBibDate === '{{ $fechaBib }}'">
                                    <span>📚</span>
                                    <span>
                                        {{ $prestamo->inventario?->titulo ?? 'Libro' }}
                                        <span class="text-xs" style="color: var(--scarso-muted);">
                                            ({{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m') }})
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Mini calendario de préstamos --}}
                    <div class="mt-4">
                        <x-calendar-mini title="Resumen mensual" :events="$eventosPrestamosDocente" scope="bib" class="mt-3" />
                    </div>

                    <a class="btn-dashboard mt-3"
                        href="{{ route('filament.docentes.resources.prestamo_biblioteca.index') }}">
                        Ver todos
                    </a>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
