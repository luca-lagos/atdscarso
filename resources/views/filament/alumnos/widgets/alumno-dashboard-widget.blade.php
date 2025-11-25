<x-filament-widgets::widget>
    <x-filament::section class="mini-calendar-section">
        <div class="space-y-6" x-data="{
            selectedDate: null,
        }"
            @mini-calendar-select.window="
                selectedDate = $event.detail.date
            ">
            {{-- Encabezado --}}
            <div class="widget-header">
                <h2>Bienvenido/a, {{ auth()->user()->name }}</h2>
                <p>
                    Desde este panel podés consultar tus préstamos de biblioteca y mirar los calendarios de uso.
                </p>
            </div>

            {{-- Tarjeta única --}}
            <div class="dashboard-card card-slate">
                <h3 class="card-title">
                    📚 Tus últimos préstamos
                    <span x-show="selectedDate" class="text-xs text-slate-200 font-normal">
                        · Día <span x-text="selectedDate"></span>
                    </span>
                </h3>

                @if ($prestamos->isEmpty())
                    <p class="text-muted">No tenés préstamos activos ni recientes.</p>
                @else
                    <ul class="list-compact" x-ref="listaAlu">
                        @foreach ($prestamos as $prestamo)
                            @php
                                $fecha =
                                    $prestamo->fecha_prestamo instanceof \Carbon\Carbon
                                        ? $prestamo->fecha_prestamo->toDateString()
                                        : $prestamo->fecha_prestamo;
                            @endphp
                            <li x-show="!selectedDate || selectedDate === '{{ $fecha }}'">
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

                    {{-- Mensaje si no hay préstamos para la fecha seleccionada --}}
                    <p class="mt-1 text-xs text-slate-500"
                        x-show="selectedDate && !$refs.listaAlu.querySelector('li:not([style*=\"display: none\"])')">
                        No tenés préstamos registrados para la fecha seleccionada.
                    </p>
                @endif

                {{-- Mini calendario de préstamos --}}
                <div class="mt-4">
                    <x-calendar-mini title="Tus préstamos del mes" :events="$eventosPrestamosAlumno" class="mt-3"
                        selected-var="selectedDate" />
                </div>

                {{-- Texto de contexto bajo el calendar --}}
                <p class="mt-2 text-xs text-slate-400">
                    Hacé clic en un día del calendario para filtrar los préstamos de esa fecha.
                    Si no seleccionás ninguna fecha, verás los últimos movimientos.
                </p>

                <a class="btn-dashboard mt-3"
                    href="{{ route('filament.alumnos.resources.prestamo_biblioteca.index') }}">
                    Ver todos
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
