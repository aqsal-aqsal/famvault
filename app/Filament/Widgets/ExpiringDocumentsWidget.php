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

    protected static ?string $heading = '⚠️ Documents Approaching Expiry (Next 60 Days)';

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
                    ->label('Document')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('familyMember.full_name')
                    ->label('Family Member')
                    ->placeholder('Shared Family Document')
                    ->searchable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'identity' => 'info',
                        'vehicle' => 'warning',
                        'property' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
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
                        if ($diff < 0) return abs($diff) . ' days overdue';
                        if ($diff === 0) return 'Expires today!';
                        return $diff . ' days remaining';
                    }),
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
            ]);
    }
}
