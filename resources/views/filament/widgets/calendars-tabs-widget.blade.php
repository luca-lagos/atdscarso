<x-filament-widgets::widget>
    <x-filament::section>
        <div x-data="{ tab: 'tv' }" class="space-y-4">

            <!-- Barra de Tabs -->
            <div class="flex gap-4 border-b border-gray-200 dark:border-gray-700 mb-2.5">
                <!-- Botón Turnos TV -->
                <button type="button" class="px-4 py-2 text-sm font-medium rounded-t-md transition-colors p-4"
                    x-bind:class="tab === 'tv'
                        ?
                        'bg-primary-600 text-white shadow-md' :
                        'text-gray-600 dark:text-gray-300 hover:text-primary-500'"
                    x-on:click="tab = 'tv'">
                    📺 Turnos TV
                </button>

                <!-- Botón Turnos Sala -->
                <button type="button" class="px-4 py-2 text-sm font-medium rounded-t-md transition-colors"
                    x-bind:class="tab === 'sala'
                        ?
                        'bg-primary-600 text-white shadow-md' :
                        'text-gray-600 dark:text-gray-300 hover:text-primary-500'"
                    x-on:click="tab = 'sala'">
                    🖥️ Turnos Sala
                </button>
            </div>

            <!-- Contenido de la pestaña TV -->
            <div x-show="tab === 'tv'" class="w-full">
                @livewire(\App\Filament\Resources\TurnosTvs\Widgets\TurnosTvCalendarWidget::class)
            </div>

            <!-- Contenido de la pestaña Sala -->
            <div x-show="tab === 'sala'" class="w-full">
                @livewire(\App\Filament\Resources\TurnosSalas\Widgets\TurnosSalaCalendarWidget::class)
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
