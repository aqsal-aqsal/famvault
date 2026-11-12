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
                    ->label('Pemilik Dokumen')
                    ->placeholder('Pilih anggota keluarga (kosongkan jika dokumen keluarga umum)')
                    ->relationship('familyMember', 'full_name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->label('Judul Dokumen')
                    ->placeholder('Contoh: KTP Ayah, SIM A, Sertifikat Tanah, Ijazah S1')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('Kategori Dokumen')
                    ->options([
                        'identity' => 'Identitas (KTP, SIM, Paspor, Kartu Keluarga)',
                        'vehicle' => 'Kendaraan (STNK, BPKB)',
                        'property' => 'Properti (Sertifikat Hak Milik, Akta Tanah)',
                        'education' => 'Pendidikan (Ijazah, Sertifikat Kursus)',
                        'health' => 'Kesehatan (Kartu BPJS, Asuransi Kesehatan)',
                        'other' => 'Lainnya / Arsip Berharga',
                    ])
                    ->required(),
                FileUpload::make('encrypted_file_path')
                    ->label('Berkas Terenkripsi (.enc)')
                    ->directory('vault-documents')
                    ->disk('local')
                    ->required(),
                TextInput::make('file_mime')
                    ->label('Tipe MIME Asli')
                    ->placeholder('Contoh: application/pdf, image/jpeg')
                    ->default('application/pdf')
                    ->required()
                    ->maxLength(100),
                TextInput::make('file_size_bytes')
                    ->label('Ukuran Berkas (Bytes)')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('iv_salt')
                    ->label('Payload IV & Salt (Base64)')
                    ->placeholder('saltBase64:ivBase64')
                    ->helperText('Dibuat otomatis oleh enkriptor browser sisi klien')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('expiry_date')
                    ->label('Masa Berlaku (kosongkan jika seumur hidup)')
                    ->native(false),
                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }
}
