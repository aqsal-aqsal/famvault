<?php

namespace App\Filament\Resources\VaultDocuments;

use App\Filament\Resources\VaultDocuments\Pages\CreateVaultDocument;
use App\Filament\Resources\VaultDocuments\Pages\EditVaultDocument;
use App\Filament\Resources\VaultDocuments\Pages\ListVaultDocuments;
use App\Filament\Resources\VaultDocuments\Schemas\VaultDocumentForm;
use App\Filament\Resources\VaultDocuments\Tables\VaultDocumentsTable;
use App\Models\VaultDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class VaultDocumentResource extends Resource
{
    protected static ?string $model = VaultDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static UnitEnum|string|null $navigationGroup = 'Vault & Security';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'category'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Owner' => $record->familyMember?->full_name ?? 'Shared Family',
            'Category' => ucfirst($record->category),
            'Expires' => $record->expiry_date?->format('d M Y') ?? 'No Expiry',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return VaultDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VaultDocumentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVaultDocuments::route('/'),
            'create' => CreateVaultDocument::route('/create'),
            'edit' => EditVaultDocument::route('/{record}/edit'),
        ];
    }
}
