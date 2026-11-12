<?php

namespace App\Filament\Resources\UtilityRecords\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UtilityRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Utility Name')
                    ->placeholder('e.g. Main Residence Electricity, Home Fiber 100Mbps')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('Category')
                    ->options([
                        'Electricity' => 'Electricity / Power',
                        'Water' => 'Water / Utility',
                        'Internet' => 'Internet / Telecom',
                        'Insurance' => 'Insurance Policy',
                        'Property Tax' => 'Property Tax / Municipal',
                        'Gas' => 'Gas Utility',
                        'Other' => 'Other Household Account',
                    ])
                    ->required(),
                TextInput::make('account_number')
                    ->label('Account / Meter / Policy Number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('account_holder_name')
                    ->label('Account Holder Name')
                    ->maxLength(255),
                KeyValue::make('metadata')
                    ->label('Account Metadata & Details')
                    ->keyLabel('Field (e.g. Capacity, Address, Plan)')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notes / Instructions')
                    ->columnSpanFull(),
            ]);
    }
}
