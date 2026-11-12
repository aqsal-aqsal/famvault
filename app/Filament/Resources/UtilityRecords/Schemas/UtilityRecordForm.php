<?php

namespace App\Filament\Resources\UtilityRecords\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UtilityRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nama Layanan / Akun')
                    ->placeholder('Contoh: Listrik Rumah Utama, Internet Fiber 100Mbps, PDAM')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('Kategori Layanan')
                    ->options([
                        'Electricity' => 'Listrik (PLN)',
                        'Water' => 'Air Bersih (PDAM)',
                        'Internet' => 'Internet & Telekomunikasi',
                        'Insurance' => 'Polis Asuransi',
                        'Property Tax' => 'Pajak Bumi & Bangunan (PBB)',
                        'Gas' => 'Gas Rumah Tangga (PGN/LPG)',
                        'Other' => 'Akun Rumah Tangga Lainnya',
                    ])
                    ->required(),
                TextInput::make('account_number')
                    ->label('Nomor Akun / ID Pelanggan / Nomor Meter')
                    ->placeholder('Contoh: 530012345678')
                    ->required()
                    ->maxLength(255),
                TextInput::make('account_holder_name')
                    ->label('Atas Nama Pemilik Akun')
                    ->placeholder('Contoh: Ridwan Rahman')
                    ->maxLength(255),
                KeyValue::make('metadata')
                    ->label('Rincian & Metadata Tambahan')
                    ->keyLabel('Parameter (Daya VA, Alamat, Paket, dll)')
                    ->valueLabel('Nilai')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan & Petunjuk Pembayaran')
                    ->placeholder('Contoh: Bayar sebelum tanggal 20 setiap bulan melalui m-Banking.')
                    ->columnSpanFull(),
            ]);
    }
}
