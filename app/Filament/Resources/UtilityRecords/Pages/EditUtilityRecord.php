<?php

namespace App\Filament\Resources\UtilityRecords\Pages;

use App\Filament\Resources\UtilityRecords\UtilityRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUtilityRecord extends EditRecord
{
    protected static string $resource = UtilityRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
