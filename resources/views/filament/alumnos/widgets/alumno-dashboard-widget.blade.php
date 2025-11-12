<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-amber-700">
                Bienvenido/a, {{ auth()->user()->name }}
            </h2>

            <p class="text-slate-600">
                Desde este panel podés consultar tus préstamos de biblioteca y mirar los calendarios de uso.
            </p>

            {{-- Resumen de préstamos --}}
            <div class="bg-white shadow rounded-lg p-4 border border-slate-200">
                <h3 class="font-semibold text-slate-700 mb-2">Tus últimos préstamos</h3>
                @if ($this->data['prestamos']->isEmpty())
                    <p class="text-sm text-slate-500">No tenés préstamos activos ni recientes.</p>
                @else
                    <ul class="text-sm text-slate-700 space-y-1">
                        @foreach ($this->data['prestamos'] as $prestamo)
                            <li>
                                📚 {{ $prestamo->inventario?->titulo ?? 'Libro' }}
                                <span class="text-xs text-slate-500">
                                    ({{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m') }})
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <x-filament::button color="primary" tag="a" class="mt-3 w-full justify-center"
                    href="{{ route('filament.alumnos.resources.prestamo-bibliotecas.index') }}">
                    Ver todos
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
