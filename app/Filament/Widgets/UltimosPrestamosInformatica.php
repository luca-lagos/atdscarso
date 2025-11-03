<?php

namespace App\Filament\Widgets;

use App\Models\Prestamo; // tu modelo de informática
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosPrestamosInformaticaWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimos préstamos (Informática)';
    protected static ?int $sort = 21;
    protected static string $color = 'success';

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_prestamo') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Prestamo::query()
                    ->with(['inventario:id,nombre', 'user:id,name']) // ajusta relaciones
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('inventario.nombre')->label('Equipo')->wrap()->limit(30),
                TextColumn::make('user.name')->label('Usuario')->limit(22),
                TextColumn::make('fecha_prestamo')->label('Inicio')->date('d/m'),
                TextColumn::make('fecha_vencimiento')->label('Vence')->date('d/m'),
                TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'warning' => ['pendiente', 'vencido'],
                        'success' => 'devuelto',
                        'danger'  => 'perdido',
                        'primary' => 'activo',
                    ])
                    ->label('Estado'),
            ])
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('Sin préstamos recientes');
    }
}
