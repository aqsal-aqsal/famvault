<?php

namespace App\Filament\Resources\VaultDocuments\Tables;

use App\Models\VaultDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VaultDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Dokumen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('familyMember.full_name')
                    ->label('Pemilik')
                    ->placeholder('Dokumen Bersama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'identity' => 'info',
                        'vehicle' => 'warning',
                        'property' => 'success',
                        'education' => 'primary',
                        'health' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'identity' => 'Identitas',
                        'vehicle' => 'Kendaraan',
                        'property' => 'Properti',
                        'education' => 'Pendidikan',
                        'health' => 'Kesehatan',
                        default => 'Lainnya',
                    }),
                TextColumn::make('file_mime')
                    ->label('Tipe Berkas')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('file_size_bytes')
                    ->label('Ukuran')
                    ->formatStateUsing(function ($state) {
                        if ($state >= 1048576) {
                            return round($state / 1048576, 2) . ' MB';
                        }
                        if ($state >= 1024) {
                            return round($state / 1024, 2) . ' KB';
                        }
                        return $state . ' B';
                    })
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label('Masa Berlaku')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        empty($state) => 'gray',
                        now()->diffInDays($state, false) < 0 => 'danger',
                        now()->diffInDays($state, false) <= 30 => 'warning',
                        default => 'success',
                    })
                    ->description(function ($record) {
                        if (! $record->expiry_date) return null;
                        $diff = (int) now()->diffInDays($record->expiry_date, false);
                        if ($diff < 0) return abs($diff) . ' hari lewat';
                        if ($diff === 0) return 'Hari ini kedaluwarsa!';
                        return $diff . ' hari tersisa';
                    }),
                TextColumn::make('created_at')
                    ->label('Diupload Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'identity' => 'Identitas',
                        'vehicle' => 'Kendaraan',
                        'property' => 'Properti',
                        'education' => 'Pendidikan',
                        'health' => 'Kesehatan',
                        'other' => 'Lainnya',
                    ]),
                SelectFilter::make('family_member_id')
                    ->label('Pemilik')
                    ->relationship('familyMember', 'full_name'),
                Filter::make('expiring_soon')
                    ->label('Mendekati Kedaluwarsa (<= 30 Hari)')
                    ->query(fn ($query) => $query->whereNotNull('expiry_date')->whereBetween('expiry_date', [now(), now()->addDays(30)])),
                Filter::make('expired')
                    ->label('Sudah Kedaluwarsa')
                    ->query(fn ($query) => $query->whereNotNull('expiry_date')->where('expiry_date', '<', now())),
            ])
            ->recordActions([
                Action::make('decrypt')
                    ->label('Dekripsi')
                    ->icon(Heroicon::OutlinedLockOpen)
                    ->color('warning')
                    ->modalHeading(fn (VaultDocument $record) => 'Dekripsi Zero-Knowledge: ' . $record->title)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (VaultDocument $record) => view('filament.resources.vault-documents.decrypt-modal', ['record' => $record])),
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
