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
                    ->label('Nama Lengkap')
                    ->placeholder('Contoh: Abdullah Rahman')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nickname')
                    ->label('Nama Panggilan')
                    ->placeholder('Contoh: Kakek / Dul')
                    ->maxLength(255),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ])
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->native(false),
                DatePicker::make('death_date')
                    ->label('Tanggal Wafat (jika telah meninggal)')
                    ->native(false),
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->maxLength(30),
                Select::make('father_id')
                    ->label('Ayah')
                    ->relationship('father', 'full_name')
                    ->searchable()
                    ->preload(),
                Select::make('mother_id')
                    ->label('Ibu')
                    ->relationship('mother', 'full_name')
                    ->searchable()
                    ->preload(),
                Select::make('spouse_id')
                    ->label('Pasangan')
                    ->relationship('spouse', 'full_name')
                    ->searchable()
                    ->preload(),
                FileUpload::make('avatar_path')
                    ->label('Foto Profil')
                    ->image()
                    ->directory('avatars')
                    ->disk('public')
                    ->avatar(),
                Textarea::make('notes')
                    ->label('Catatan / Biografi Singkat')
                    ->columnSpanFull(),
            ]);
    }
}
