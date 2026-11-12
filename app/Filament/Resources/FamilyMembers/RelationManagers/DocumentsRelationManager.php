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
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'identity' => 'info',
                        'vehicle' => 'warning',
                        'property' => 'success',
                        'education' => 'primary',
                        'health' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('file_mime')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
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
                        if ($diff < 0) return abs($diff) . ' days overdue';
                        if ($diff === 0) return 'Expires today!';
                        return $diff . ' days left';
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('decrypt')
                    ->label('Decrypt')
                    ->icon(Heroicon::OutlinedLockOpen)
                    ->color('warning')
                    ->modalHeading(fn (VaultDocument $record) => 'Zero-Knowledge Decrypt: ' . $record->title)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (VaultDocument $record) => view('filament.resources.vault-documents.decrypt-modal', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
