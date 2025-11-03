<?php

namespace App\Filament\Widgets;

use App\Models\PrestamoBiblioteca;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosPrestamosBibliotecaWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimos préstamos (Biblioteca)';
    protected static ?int $sort = 20; // orden en dashboard
    protected static string $color = 'primary';

    public static function canView(): bool
    {
        //Solo quienes pueden ver préstamos de biblioteca
        return auth()->user()?->can('view_any_prestamo_biblioteca') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PrestamoBiblioteca::query()
                    ->with(['inventario_informatica:id,titulo,autor', 'user:id,name'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('inventario_biblioteca.titulo')->label('Libro')->wrap()->limit(30),
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
