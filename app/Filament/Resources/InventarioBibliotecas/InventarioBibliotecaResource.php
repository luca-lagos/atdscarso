<?php

namespace App\Filament\Resources\InventarioBibliotecas;

use App\Filament\Resources\InventarioBibliotecas\Pages\CreateInventarioBiblioteca;
use App\Filament\Resources\InventarioBibliotecas\Pages\EditInventarioBiblioteca;
use App\Filament\Resources\InventarioBibliotecas\Pages\ListInventarioBibliotecas;
use App\Filament\Resources\InventarioBibliotecas\Pages\ViewInventarioBiblioteca;
use App\Filament\Resources\InventarioBibliotecas\Schemas\InventarioBibliotecaForm;
use App\Filament\Resources\InventarioBibliotecas\Schemas\InventarioBibliotecaInfolist;
use App\Filament\Resources\InventarioBibliotecas\Tables\InventarioBibliotecasTable;
use App\Models\InventarioBiblioteca;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InventarioBibliotecaResource extends Resource
{
    protected static ?string $model = InventarioBiblioteca::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Biblioteca';
    protected static ?string $navigationLabel = 'Libros';
    protected static ?string $modelLabel = 'Libro';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return InventarioBibliotecaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InventarioBibliotecaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventarioBibliotecasTable::configure($table);
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
            'index' => ListInventarioBibliotecas::route('/'),
            'create' => CreateInventarioBiblioteca::route('/create'),
            'view' => ViewInventarioBiblioteca::route('/{record}'),
            'edit' => EditInventarioBiblioteca::route('/{record}/edit'),
        ];
    }
}
