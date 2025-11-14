<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            {{-- Encabezado --}}
            <div class="widget-header">
                <h2>Bienvenido/a, {{ auth()->user()->name }}</h2>
                <p>Desde este panel podés consultar tus préstamos de biblioteca y mirar los calendarios de uso.</p>
            </div>

            {{-- Tarjeta única --}}
            <div class="dashboard-card card-slate">
                <h3 class="card-title">📚 Tus últimos préstamos</h3>
                @if ($prestamos->isEmpty())
                    <p class="text-muted">No tenés préstamos activos ni recientes.</p>
                @else
                    <ul class="list-compact">
                        @foreach ($prestamos as $prestamo)
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
                <x-calendar-mini title="Tus préstamos del mes" :events="$eventosPrestamosAlumno" class="mt-3" />

                <a class="btn-dashboard" href="{{ route('filament.alumnos.resources.prestamo_biblioteca.index') }}">
                    Ver todos
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
