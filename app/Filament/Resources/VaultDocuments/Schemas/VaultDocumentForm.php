<?php

namespace App\Filament\Resources\VaultDocuments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;

class VaultDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ViewField::make('crypto_uploader')
                    ->view('filament.forms.crypto-uploader')
                    ->columnSpanFull(),
                Select::make('family_member_id')
                    ->label('Document Owner')
                    ->relationship('familyMember', 'full_name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->label('Document Title')
                    ->placeholder('e.g. Passport, National ID, Land Certificate')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('Category')
                    ->options([
                        'identity' => 'Identity (National ID, Passport, Driver License)',
                        'vehicle' => 'Vehicle (Registration, Title)',
                        'property' => 'Property (Certificate, Deed, Lease)',
                        'education' => 'Education (Diploma, Transcript)',
                        'health' => 'Health (Insurance, Medical Record)',
                        'other' => 'Other / Miscellaneous',
                    ])
                    ->required(),
                FileUpload::make('encrypted_file_path')
                    ->label('Encrypted File (.enc)')
                    ->directory('vault-documents')
                    ->disk('local')
                    ->required(),
                TextInput::make('file_mime')
                    ->label('Original MIME Type')
                    ->placeholder('e.g. application/pdf, image/jpeg')
                    ->default('application/pdf')
                    ->required()
                    ->maxLength(100),
                TextInput::make('file_size_bytes')
                    ->label('File Size (Bytes)')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('iv_salt')
                    ->label('IV & Salt (Base64)')
                    ->placeholder('saltBase64:ivBase64')
                    ->helperText('Client-generated initialization vector and PBKDF2 salt')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->native(false),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }
}
