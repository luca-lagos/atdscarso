<x-filament-widgets::widget>
    <x-filament::section>
        <div x-data="{
            tab: localStorage.getItem('scarso_cal_tab') || 'tv',
            setTab(v) {
                this.tab = v;
                localStorage.setItem('scarso_cal_tab', v)
            }
        }" x-cloak class="scarso-cal-widget">

            <!-- Toolbar -->
            <div class="scarso-toolbar">
                <div class="scarso-btn-group" role="tablist" aria-label="Calendarios">
                    <button type="button" :class="tab === 'tv' ? 'active' : ''" @click="setTab('tv')" role="tab"
                        aria-selected="true" aria-controls="tab-tv">
                        <span class="ico">📺</span>
                        <span>Turnos TV</span>
                    </button>
                    <div class="divider" aria-hidden="true"></div>
                    <button type="button" :class="tab === 'sala' ? 'active' : ''" @click="setTab('sala')"
                        role="tab" aria-controls="tab-sala">
                        <span class="ico">🖥️</span>
                        <span>Turnos Sala</span>
                    </button>
                    <div class="divider" aria-hidden="true"></div>
                    <button type="button" :class="tab === 'ambos' ? 'active' : ''" @click="setTab('ambos')"
                        role="tab" aria-controls="tab-ambos">
                        <span class="ico">▦</span>
                        <span>Ambos</span>
                    </button>
                </div>

                <div class="scarso-actions">
                    <a href="{{ \App\Filament\Resources\TurnosTvs\Pages\CreateTurnosTv::getUrl() }}" class="scarso-btn">
                        <span class="ico">＋</span> Nuevo turno TV
                    </a>
                    <a href="{{ \App\Filament\Resources\TurnosSalas\Pages\CreateTurnosSala::getUrl() }}"
                        class="scarso-btn">
                        <span class="ico">＋</span> Nuevo turno Sala
                    </a>
                </div>
            </div>

            <!-- Card calendario -->
            <div class="scarso-card">
                <div id="tab-tv" x-show="tab === 'tv'" x-transition.opacity.duration.120ms>
                    @livewire(\App\Filament\Resources\TurnosTvs\Widgets\TurnosTvCalendarWidget::class, key('cal-tv'))
                </div>
                <div id="tab-sala" x-show="tab === 'sala'" x-transition.opacity.duration.120ms>
                    @livewire(\App\Filament\Resources\TurnosSalas\Widgets\TurnosSalaCalendarWidget::class, key('cal-sala'))
                </div>
                <div id="tab-ambos" x-show="tab === 'ambos'" x-transition.opacity.duration.120ms>
                    <div class="scarso-grid">
                        <div class="scarso-grid-item">
                            <div>Turnos TV</div>
                            @livewire(\App\Filament\Resources\TurnosTvs\Widgets\TurnosTvCalendarWidget::class, key('cal-tv-dual'))
                        </div>
                        <div class="scarso-grid-item">
                            <div>Turnos Sala</div>
                            @livewire(\App\Filament\Resources\TurnosSalas\Widgets\TurnosSalaCalendarWidget::class, key('cal-sala-dual'))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
