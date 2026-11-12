<?php

namespace App\Filament\Resources\UtilityRecords\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UtilityRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('1. Data Tagihan & Nomor Pelanggan')
                    ->description('Catat nomor meteran atau ID akun agar siapapun di keluarga mudah membayar tagihan.')
                    ->icon(Heroicon::OutlinedBolt)
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Tagihan / Rekening')
                            ->placeholder('Contoh: Listrik Rumah Utama, WiFi Ruang Tengah, PDAM')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')
                            ->label('Kategori Tagihan')
                            ->options([
                                'Electricity' => 'Listrik (PLN Pascabayar / Token)',
                                'Water' => 'Air Bersih (PDAM)',
                                'Internet' => 'Internet & WiFi (Indihome, Biznet, dll)',
                                'Insurance' => 'Asuransi & BPJS',
                                'Property Tax' => 'Pajak Bumi & Bangunan (PBB)',
                                'Gas' => 'Gas Rumah Tangga (PGN/LPG)',
                                'Other' => 'Tagihan Rumah Tangga Lainnya',
                            ])
                            ->required(),
                        TextInput::make('account_number')
                            ->label('Nomor Pelanggan / Nomor Meter / ID Tagihan')
                            ->placeholder('Contoh: 531234567890')
                            ->helperText('Nomor yang dimasukkan saat membayar di m-Banking atau kasir minimarket')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('account_holder_name')
                            ->label('Atas Nama Rekening')
                            ->placeholder('Contoh: Abdullah Rahman')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('2. Petunjuk Bayar & Keterangan Tambahan')
                    ->description('Jadwal pembayaran rutin dan rincian paket langganan.')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->schema([
                        Textarea::make('notes')
                            ->label('Petunjuk Pembayaran / Tanggal Jatuh Tempo')
                            ->placeholder('Contoh: Bayar sebelum tanggal 20 setiap bulan via BCA Mobile atau Indomaret.')
                            ->columnSpanFull(),
                        KeyValue::make('metadata')
                            ->label('Rincian Spesifikasi (Opsional)')
                            ->keyLabel('Keterangan (Daya VA, Alamat, Paket MBps, dll)')
                            ->valueLabel('Nilai')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
