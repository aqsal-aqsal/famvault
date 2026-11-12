<?php

namespace App\Filament\Resources\FamilyMembers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class FamilyMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('1. Identitas Anggota Keluarga')
                    ->description('Masukkan data diri lengkap dan foto anggota keluarga.')
                    ->icon(Heroicon::OutlinedUser)
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Abdullah Rahman')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nickname')
                            ->label('Nama Panggilan / Sapaan')
                            ->placeholder('Contoh: Kakek Dul / Om Rahman')
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
                            ->placeholder('Pilih tanggal lahir')
                            ->native(false),
                        DatePicker::make('death_date')
                            ->label('Tanggal Wafat (Opsional)')
                            ->helperText('Kosongkan jika anggota keluarga masih hidup')
                            ->native(false),
                        FileUpload::make('avatar_path')
                            ->label('Foto Anggota Keluarga')
                            ->image()
                            ->directory('avatars')
                            ->disk('public')
                            ->avatar()
                            ->imageEditor()
                            ->maxSize(4096)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('2. Hubungan Silsilah (Garis Keturunan)')
                    ->description('Hubungkan dengan orang tua atau pasangan yang sudah terdaftar untuk membentuk pohon keluarga secara otomatis.')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->schema([
                        Select::make('father_id')
                            ->label('Ayah Kandung')
                            ->placeholder('Pilih ayah dari daftar keluarga')
                            ->relationship('father', 'full_name')
                            ->searchable()
                            ->preload(),
                        Select::make('mother_id')
                            ->label('Ibu Kandung')
                            ->placeholder('Pilih ibu dari daftar keluarga')
                            ->relationship('mother', 'full_name')
                            ->searchable()
                            ->preload(),
                        Select::make('spouse_id')
                            ->label('Suami / Istri (Pasangan)')
                            ->placeholder('Pilih pasangan jika sudah terdaftar')
                            ->relationship('spouse', 'full_name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),

                Section::make('3. Kontak & Catatan Tambahan (Opsional)')
                    ->description('Nomor telepon dan informasi penting lainnya.')
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->collapsed()
                    ->schema([
                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->placeholder('Contoh: 081234567890')
                            ->tel()
                            ->maxLength(30),
                        Textarea::make('notes')
                            ->label('Catatan Pribadi / Kisah Singkat')
                            ->placeholder('Contoh: Lahir di Surabaya, pensiunan guru SMP 1.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
