<?php

namespace App\Filament\Resources\UtilityRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UtilityRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Nama Layanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Electricity' => 'warning',
                        'Water' => 'info',
                        'Internet' => 'primary',
                        'Insurance' => 'success',
                        'Property Tax' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Electricity' => 'Listrik',
                        'Water' => 'Air PDAM',
                        'Internet' => 'Internet',
                        'Insurance' => 'Asuransi',
                        'Property Tax' => 'PBB',
                        'Gas' => 'Gas',
                        default => 'Lainnya',
                    }),
                TextColumn::make('account_number')
                    ->label('Nomor Akun / Meter')
                    ->copyable()
                    ->copyMessage('Nomor akun berhasil disalin ke clipboard')
                    ->searchable(),
                TextColumn::make('account_holder_name')
                    ->label('Atas Nama')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Didaftarkan Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori Layanan')
                    ->options([
                        'Electricity' => 'Listrik (PLN)',
                        'Water' => 'Air (PDAM)',
                        'Internet' => 'Internet & WiFi',
                        'Insurance' => 'Asuransi',
                        'Property Tax' => 'PBB / Pajak',
                        'Gas' => 'Gas Rumah Tangga',
                        'Other' => 'Lainnya',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang Dipilih'),
                ]),
            ]);
    }
}
