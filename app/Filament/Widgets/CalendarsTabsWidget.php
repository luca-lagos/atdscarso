<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CalendarsTabsWidget extends Widget
{
    protected string $view = 'filament.widgets.calendars-tabs-widget';

    protected int|string|array $columnSpan = 'full';
}
