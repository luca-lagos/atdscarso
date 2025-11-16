<x-filament-widgets::widget>
    <x-filament::section class="admin-calendar-section">
        <div class="admin-calendar-header">
            <div>
                <h2>{{ $this->getHeading() }}</h2>
                <p>Visualizá y gestioná todos los turnos de las salas de informática.</p>
            </div>
            <div class="admin-calendar-meta">
                <span class="admin-calendar-chip">Salas de informática</span>
            </div>
        </div>

        <div class="admin-calendar-body">
            {{ $this->calendar }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
