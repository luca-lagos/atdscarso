{{-- resources/views/filament/admin/widgets/salas-calendar-widget.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section class="admin-calendar-section">
        <div class="admin-calendar-header">
            <div>
                <h2>Calendario de salas</h2>
                <p>Visualizá todos los turnos de las salas de informática.</p>
            </div>
            <div class="admin-calendar-meta">
                <span class="admin-calendar-chip">Salas de Informática</span>
            </div>
        </div>

        <div class="admin-calendar-body">
            {{-- Acá va tu Guava Calendar / FullCalendar / lo que uses --}}
            {{ $this->calendar }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
