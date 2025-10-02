<?php

namespace App\Filament\Resources\TurnosTvs;

use App\Filament\Resources\TurnosTvs\Pages\CreateTurnosTv;
use App\Filament\Resources\TurnosTvs\Pages\EditTurnosTv;
use App\Filament\Resources\TurnosTvs\Pages\ListTurnosTvs;
use App\Filament\Resources\TurnosTvs\Schemas\TurnosTvForm;
use App\Filament\Resources\TurnosTvs\Tables\TurnosTvsTable;
use App\Models\Turnos_tv;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TurnosTvResource extends Resource
{
    protected static ?string $model = Turnos_tv::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Turnos tv';

    protected static ?string $navigationLabel = 'Turnos TV';

    protected static ?int $navigationSort = 3;


    public static function form(Schema $schema): Schema
    {
        return TurnosTvForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TurnosTvsTable::configure($table);
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
            'index' => ListTurnosTvs::route('/'),
            'create' => CreateTurnosTv::route('/create'),
            'edit' => EditTurnosTv::route('/{record}/edit'),
        ];
    }
}
