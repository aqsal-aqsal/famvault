<?php

namespace App\Filament\Resources\FamilyMembers\RelationManagers;

use App\Filament\Resources\VaultDocuments\Schemas\VaultDocumentForm;
use App\Models\VaultDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen Terenkripsi';

    public function form(Schema $schema): Schema
    {
        return VaultDocumentForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Dokumen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

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
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Dokumen'),
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
