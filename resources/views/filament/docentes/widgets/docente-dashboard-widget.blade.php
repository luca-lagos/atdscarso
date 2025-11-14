<x-filament-widgets::widget>
    <x-filament::section class="mini-calendar-section">
        <div class="self-panel space-y-6">
            {{-- Encabezado --}}
            <div class="widget-header">
                <h2>Bienvenido, {{ auth()->user()->name }}</h2>
                <p>Desde aquí podés gestionar tus turnos de sala, televisores y préstamos de biblioteca.</p>
            </div>

            {{-- Grid de tarjetas (1 → 3 columnas responsivo) --}}
            <div class="grid-dashboard">
                {{-- Turnos de Sala --}}
                <div class="dashboard-card card-amber">
                    <h3 class="card-title">📅 Próximos turnos de sala</h3>
                    @if ($turnosSala->isEmpty())
                        <p class="text-muted">No tenés turnos próximos.</p>
                    @else
                        <ul class="list-compact">
                            @foreach ($turnosSala as $turno)
                                <li>
                                    <span>📅</span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($turno->fecha_turno)->format('d/m') }} •
                                        {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}‑{{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Mini calendario de turnos de sala --}}
                    <x-calendar-mini title="Resumen mensual" :events="$eventosSala" class="mt-3" />

                    <a class="btn-dashboard" href="{{ route('filament.docentes.resources.turnos-salas.index') }}">
                        Ver todos
                    </a>
                </div>

                {{-- Turnos TV --}}
                <div class="dashboard-card card-emerald">
                    <h3 class="card-title">📺 Próximos turnos de TV</h3>
                    @if ($turnosTv->isEmpty())
                        <p class="text-muted">No tenés turnos próximos.</p>
                    @else
                        <ul class="list-compact">
                            @foreach ($turnosTv as $tv)
                                <li>
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
                    <x-calendar-mini title="Resumen mensual" :events="$eventosTv" class="mt-3" />

                    <a class="btn-dashboard" href="{{ route('filament.docentes.resources.turnos-tvs.index') }}">
                        Ver todos
                    </a>
                </div>

                {{-- Préstamos Biblioteca --}}
                <div class="dashboard-card card-slate">
                    <h3 class="card-title">📚 Últimos préstamos de biblioteca</h3>
                    @if ($prestamosBiblioteca->isEmpty())
                        <p class="text-muted">No registrás préstamos recientes.</p>
                    @else
                        <ul class="list-compact">
                            @foreach ($prestamosBiblioteca as $prestamo)
                                <li>
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
                    <x-calendar-mini title="Resumen mensual" :events="$eventosPrestamosDocente" class="mt-3" />

                    <a class="btn-dashboard"
                        href="{{ route('filament.docentes.resources.prestamo_biblioteca.index') }}">
                        Ver todos
                    </a>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
