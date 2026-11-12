<?php

namespace App\Filament\Resources\VaultDocuments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class VaultDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Step 1: Secure upload (Zero-Knowledge AES-GCM 256)
                Section::make('Langkah 1: Unggah & Kunci Berkas')
                    ->description('Pilih dokumen dari perangkat Anda dan buat kata sandi rahasia untuk menguncinya.')
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->schema([
                        ViewField::make('crypto_uploader')
                            ->view('filament.forms.crypto-uploader')
                            ->columnSpanFull(),
                    ]),

                // Technical cryptographic fields stored behind the scenes
                Hidden::make('encrypted_file_path')
                    ->required(),
                Hidden::make('file_mime')
                    ->default('application/pdf')
                    ->required(),
                Hidden::make('file_size_bytes')
                    ->default(0)
                    ->required(),
                Hidden::make('iv_salt')
                    ->required(),

                // Step 2: Human-readable metadata
                Section::make('Langkah 2: Keterangan Dokumen')
                    ->description('Beri judul dan pilih kategori agar dokumen mudah dicari oleh anggota keluarga.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Dokumen')
                            ->placeholder('Contoh: KTP Ayah, Kartu Keluarga, Sertifikat Tanah Rumah')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')
                            ->label('Kategori Dokumen')
                            ->options([
                                'identity' => 'Identitas (KTP, SIM, Paspor, Kartu Keluarga)',
                                'vehicle' => 'Kendaraan (STNK, BPKB)',
                                'property' => 'Properti (Sertifikat Rumah, Akta Tanah)',
                                'education' => 'Pendidikan (Ijazah, Rapot, Piagam)',
                                'health' => 'Kesehatan (Kartu BPJS, Asuransi)',
                                'other' => 'Lainnya / Arsip Penting',
                            ])
                            ->required(),
                        Select::make('family_member_id')
                            ->label('Pemilik Dokumen')
                            ->placeholder('Pilih nama anggota keluarga (kosongkan jika milik bersama keluarga)')
                            ->relationship('familyMember', 'full_name')
                            ->searchable()
                            ->preload(),
                        DatePicker::make('expiry_date')
                            ->label('Tanggal Masa Berlaku / Kedaluwarsa')
                            ->helperText('Kosongkan jika berlaku seumur hidup (misal Ijazah atau Akta Lahir)')
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Catatan Tempat Penyimpanan Fisik (Opsional)')
                            ->placeholder('Contoh: Berkas asli fisik disimpan di lemari kamar utama rak no. 2')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
