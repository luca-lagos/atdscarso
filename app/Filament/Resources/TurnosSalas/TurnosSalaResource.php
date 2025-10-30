<?php

namespace App\Filament\Resources\TurnosSalas;

use App\Filament\Resources\TurnosSalas\Pages\CreateTurnosSala;
use App\Filament\Resources\TurnosSalas\Pages\EditTurnosSala;
use App\Filament\Resources\TurnosSalas\Pages\ListTurnosSalas;
use App\Filament\Resources\TurnosSalas\Schemas\TurnosSalaForm;
use App\Filament\Resources\TurnosSalas\Tables\TurnosSalasTable;
use App\Models\Turnos_sala;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TurnosSalaResource extends Resource
{
    protected static ?string $model = Turnos_sala::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|UnitEnum|null $navigationGroup = 'Informática';

    protected static ?string $recordTitleAttribute = 'Turnos Sala Informática';

    protected static ?string $navigationLabel = 'Turnos Sala Informática';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return TurnosSalaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TurnosSalasTable::configure($table);
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
            'index' => ListTurnosSalas::route('/'),
            'create' => CreateTurnosSala::route('/create'),
            'edit' => EditTurnosSala::route('/{record}/edit'),
        ];
    }
}
