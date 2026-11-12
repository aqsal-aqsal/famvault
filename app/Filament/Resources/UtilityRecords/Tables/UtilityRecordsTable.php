<?php

namespace App\Filament\Resources\UtilityRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UtilityRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Utility Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Electricity' => 'warning',
                        'Water' => 'info',
                        'Internet' => 'primary',
                        'Insurance' => 'success',
                        'Property Tax' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('account_number')
                    ->label('Account / Meter #')
                    ->copyable()
                    ->copyMessage('Account number copied')
                    ->searchable(),
                TextColumn::make('account_holder_name')
                    ->label('Account Holder')
                    ->searchable()
                    ->placeholder('-'),
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
