<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->label('Contraseña')
                            ->hint('Dejar en blanco para mantener la contraseña actual')
                            ->required(),
                        FileUpload::make('avatar_path')
                            ->label('Foto de perfil')
                            ->directory('avatars')
                            ->image()
                            ->imageEditor()
                            ->circleCropper() // lo hace redondo
                            ->maxSize(2048)
                            ->columnSpan('full'),
                    ])->columns(2),
                Section::make('Roles y permisos')
                    ->description('Asignar roles y permisos al usuario.')
                    ->schema([
                        /*Select::make('rol')
                            ->options(['admin' => 'Admin', 'profesor' => 'Profesor'])
                            ->default('profesor')
                            ->label('Asignar rol')
                            ->required(),*/
                        Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn() => auth()->user()?->hasRole('super-admin') || auth()->user()?->can('update_user'))
                    ]),
            ]);
    }
}
