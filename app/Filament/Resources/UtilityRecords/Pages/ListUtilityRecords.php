<?php

namespace App\Filament\Resources\UtilityRecords\Pages;

use App\Filament\Resources\UtilityRecords\UtilityRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilityRecords extends ListRecords
{
    protected static string $resource = UtilityRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
