<?php

namespace App\Filament\Widgets;

use App\Models\VaultDocument;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpiringDocumentsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '⚠️ Dokumen Mendekati Masa Kedaluwarsa (60 Hari ke Depan)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => VaultDocument::query()
                    ->with('familyMember')
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addDays(60))
                    ->orderBy('expiry_date', 'asc')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Dokumen')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('familyMember.full_name')
                    ->label('Pemilik Dokumen')
                    ->placeholder('Dokumen Keluarga Bersama')
                    ->searchable(),

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

                TextColumn::make('expiry_date')
                    ->label('Masa Berlaku')
                    ->date('d M Y')
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
            ]);
    }
}
