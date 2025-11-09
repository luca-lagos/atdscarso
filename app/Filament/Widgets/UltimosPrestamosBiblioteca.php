<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use App\Models\PrestamoBiblioteca;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosPrestamosBibliotecaWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimos préstamos (Biblioteca)';
    protected static ?int $sort = 20;
    protected static string $color = 'primary';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super-admin')
            || (auth()->user()?->can('view_any_prestamos_biblioteca') ?? false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PrestamoBiblioteca::query()
                    ->with(['libro:id,titulo,autor', 'usuario:id,name'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('libro.titulo')->label('Libro')->wrap()->limit(30)
                    ->sortable(),
                TextColumn::make('usuario.name')->label('Usuario')->limit(22)
                    ->sortable(),
                TextColumn::make('fecha_prestamo')->label('Inicio')->date('d/m')
                    ->sortable(),
                TextColumn::make('fecha_vencimiento')->label('Vence')->date('d/m')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'warning' => ['pendiente', 'vencido'],
                        'success' => 'devuelto',
                        'danger'  => 'perdido',
                        'primary' => 'activo',
                    ])
                    ->sortable()
                    ->label('Estado'),
            ])
            ->headerActions([
                Action::make('ver_todos')
                    ->label('Ver todos')
                    ->icon('heroicon-m-arrow-right')
                    ->url(PrestamoBibliotecaResource::getUrl('index'))
                    ->color('gray'),
            ])
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('Sin préstamos recientes');
    }
}
