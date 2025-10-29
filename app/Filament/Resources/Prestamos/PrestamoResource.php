<?php

namespace App\Filament\Resources\Prestamos;

use App\Filament\Resources\Prestamos\Pages\CreatePrestamo;
use App\Filament\Resources\Prestamos\Pages\EditPrestamo;
use App\Filament\Resources\Prestamos\Pages\ListPrestamos;
use App\Filament\Resources\Prestamos\Schemas\PrestamoForm;
use App\Filament\Resources\Prestamos\Tables\PrestamosTable;
use App\Models\Prestamo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PrestamoResource extends Resource
{
    protected static ?string $model = Prestamo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Informática';

    protected static ?string $recordTitleAttribute = 'Préstamos';

    protected static ?string $navigationLabel = 'Préstamos';

    protected static ?string $modelLabel = 'Préstamo';

    protected static ?string $pluralModelLabel = 'Préstamos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PrestamoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrestamosTable::configure($table);
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
            'index' => ListPrestamos::route('/'),
            'create' => CreatePrestamo::route('/create'),
            'edit' => EditPrestamo::route('/{record}/edit'),
        ];
    }
}
