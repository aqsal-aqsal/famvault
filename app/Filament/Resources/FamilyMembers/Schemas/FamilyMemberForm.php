<?php

namespace App\Filament\Resources\FamilyMembers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FamilyMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nickname')
                    ->label('Nickname')
                    ->maxLength(255),
                Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Birth Date')
                    ->native(false),
                DatePicker::make('death_date')
                    ->label('Death Date (if deceased)')
                    ->native(false),
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(30),
                Select::make('father_id')
                    ->label('Father')
                    ->relationship('father', 'full_name')
                    ->searchable()
                    ->preload(),
                Select::make('mother_id')
                    ->label('Mother')
                    ->relationship('mother', 'full_name')
                    ->searchable()
                    ->preload(),
                Select::make('spouse_id')
                    ->label('Spouse')
                    ->relationship('spouse', 'full_name')
                    ->searchable()
                    ->preload(),
                FileUpload::make('avatar_path')
                    ->label('Profile Photo')
                    ->image()
                    ->directory('avatars')
                    ->disk('public')
                    ->avatar(),
                Textarea::make('notes')
                    ->label('Notes / Biography')
                    ->columnSpanFull(),
            ]);
    }
}
