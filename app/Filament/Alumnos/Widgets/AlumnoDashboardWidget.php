<?php

namespace App\Filament\Alumnos\Widgets;

use Filament\Widgets\Widget;
use App\Models\PrestamoBiblioteca;
use Illuminate\Support\Facades\Auth;

class AlumnoDashboardWidget extends Widget
{
    protected string $view = 'filament.alumnos.widgets.alumno-dashboard-widget';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();

        return [
            'prestamos' => PrestamoBiblioteca::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}
