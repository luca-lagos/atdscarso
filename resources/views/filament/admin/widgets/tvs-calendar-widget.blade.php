<x-filament-widgets::widget>
    <x-filament::section class="admin-calendar-section">
        <div class="admin-calendar-header">
            <div>
                <h2>Calendario de TVs</h2>
                <p>Administrá los turnos de televisores para las aulas.</p>
            </div>
            <div class="admin-calendar-meta">
                <span class="admin-calendar-chip admin-calendar-chip--tv">Televisores</span>
            </div>
        </div>

        <div class="admin-calendar-body">
            {{ $this->calendar }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
