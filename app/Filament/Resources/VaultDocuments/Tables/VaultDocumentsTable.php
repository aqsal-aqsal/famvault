<?php

namespace App\Filament\Resources\VaultDocuments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VaultDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('familyMember.full_name')
                    ->label('Owner')
                    ->placeholder('Shared / Family')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('file_size_bytes')
                    ->label('Size')
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
                    ->label('Expiry Date')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        empty($state) => 'gray',
                        now()->diffInDays($state, false) < 0 => 'danger',   // Already expired
                        now()->diffInDays($state, false) <= 30 => 'warning', // Less than 30 days remaining
                        default => 'success',                                // Valid / safe
                    })
                    ->description(function ($record) {
                        if (! $record->expiry_date) return null;
                        $diff = (int) now()->diffInDays($record->expiry_date, false);
                        if ($diff < 0) return abs($diff) . ' days overdue';
                        if ($diff === 0) return 'Expires today!';
                        return $diff . ' days left';
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
