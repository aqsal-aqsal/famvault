<?php

namespace App\Filament\Resources\UtilityRecords;

use App\Filament\Resources\UtilityRecords\Pages\CreateUtilityRecord;
use App\Filament\Resources\UtilityRecords\Pages\EditUtilityRecord;
use App\Filament\Resources\UtilityRecords\Pages\ListUtilityRecords;
use App\Filament\Resources\UtilityRecords\Schemas\UtilityRecordForm;
use App\Filament\Resources\UtilityRecords\Tables\UtilityRecordsTable;
use App\Models\UtilityRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UtilityRecordResource extends Resource
{
    protected static ?string $model = UtilityRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return UtilityRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilityRecordsTable::configure($table);
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
            'index' => ListUtilityRecords::route('/'),
            'create' => CreateUtilityRecord::route('/create'),
            'edit' => EditUtilityRecord::route('/{record}/edit'),
        ];
    }
}
