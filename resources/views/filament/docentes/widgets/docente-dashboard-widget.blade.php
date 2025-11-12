<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-amber-700">
                Bienvenido, {{ auth()->user()->name }}
            </h2>

            <p class="text-slate-600">
                Desde aquí podés gestionar tus turnos de sala, televisores y préstamos de biblioteca.
            </p>

            {{-- Bloques de resumen --}}
            <div class="grid md:grid-cols-3 gap-6">

                {{-- Turnos de Sala --}}
                <div class="bg-white shadow rounded-lg p-4 border border-amber-100">
                    <h3 class="font-semibold text-amber-700 mb-2">Próximos turnos de sala</h3>
                    @if ($this->data['turnosSala']->isEmpty())
                        <p class="text-sm text-slate-500">No tenés turnos próximos.</p>
                    @else
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach ($this->data['turnosSala'] as $turno)
                                <li>
                                    📅 {{ \Carbon\Carbon::parse($turno->fecha_turno)->format('d/m') }} •
                                    {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}‑{{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <x-filament::button color="primary" tag="a" class="mt-3 w-full justify-center"
                        href="{{ route('filament.docentes.resources.turnos-salas.index') }}">
                        Ver todos
                    </x-filament::button>
                </div>

                {{-- Turnos TV --}}
                <div class="bg-white shadow rounded-lg p-4 border border-emerald-100">
                    <h3 class="font-semibold text-emerald-700 mb-2">Próximos turnos de TV</h3>
                    @if ($this->data['turnosTv']->isEmpty())
                        <p class="text-sm text-slate-500">No tenés turnos próximos.</p>
                    @else
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach ($this->data['turnosTv'] as $tv)
                                <li>
                                    📺 {{ $tv->inventario->nombre_equipo ?? 'TV' }}
                                    • {{ \Carbon\Carbon::parse($tv->fecha_turno)->format('d/m H:i') }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <x-filament::button color="success" tag="a" class="mt-3 w-full justify-center"
                        href="{{ route('filament.docentes.resources.turnos-tvs.index') }}">
                        Ver todos
                    </x-filament::button>
                </div>

                {{-- Préstamos Biblioteca --}}
                <div class="bg-white shadow rounded-lg p-4 border border-slate-200">
                    <h3 class="font-semibold text-slate-700 mb-2">Últimos préstamos de biblioteca</h3>
                    @if ($this->data['prestamosBiblioteca']->isEmpty())
                        <p class="text-sm text-slate-500">No registrás préstamos recientes.</p>
                    @else
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach ($this->data['prestamosBiblioteca'] as $prestamo)
                                <li>
                                    📚 {{ $prestamo->inventario?->titulo ?? 'Libro' }}
                                    <span class="text-xs text-slate-500">
                                        ({{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m') }})
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <x-filament::button color="gray" tag="a" class="mt-3 w-full justify-center"
                        href="{{ route('filament.docentes.resources.prestamo-bibliotecas.index') }}">
                        Ver todos
                    </x-filament::button>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
