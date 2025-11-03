<?php

namespace App\Filament\Resources\PrestamoBibliotecas;

use App\Filament\Resources\PrestamoBibliotecas\Pages\CreatePrestamoBiblioteca;
use App\Filament\Resources\PrestamoBibliotecas\Pages\EditPrestamoBiblioteca;
use App\Filament\Resources\PrestamoBibliotecas\Pages\ListPrestamoBibliotecas;
use App\Filament\Resources\PrestamoBibliotecas\Pages\ViewPrestamoBiblioteca;
use App\Filament\Resources\PrestamoBibliotecas\Schemas\PrestamoBibliotecaForm;
use App\Filament\Resources\PrestamoBibliotecas\Schemas\PrestamoBibliotecaInfolist;
use App\Filament\Resources\PrestamoBibliotecas\Tables\PrestamoBibliotecasTable;
use App\Models\PrestamoBiblioteca;
use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PrestamoBibliotecaResource extends Resource
{
    protected static ?string $model = PrestamoBiblioteca::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Biblioteca';
    protected static ?string $navigationLabel = 'Préstamos';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Préstamo';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'prestamo_biblioteca';
    }

    public static function form(Schema $schema): Schema
    {
        return PrestamoBibliotecaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrestamoBibliotecaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrestamoBibliotecasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrestamoBibliotecas::route('/'),
            'create' => CreatePrestamoBiblioteca::route('/create'),
            'view' => ViewPrestamoBiblioteca::route('/{record}'),
            'edit' => EditPrestamoBiblioteca::route('/{record}/edit'),
        ];
    }
}
