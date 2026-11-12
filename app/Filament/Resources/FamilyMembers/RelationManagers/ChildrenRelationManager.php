<?php

namespace App\Filament\Resources\FamilyMembers\RelationManagers;

use App\Filament\Resources\FamilyMembers\Schemas\FamilyMemberForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = 'Children';

    public function form(Schema $schema): Schema
    {
        return FamilyMemberForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                ImageColumn::make('avatar_path')
                    ->label('Photo')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=18181b&color=f59e0b'),

                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('nickname')
                    ->label('Nickname')
                    ->searchable(),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $parent = $this->getOwnerRecord();
                        if ($parent->gender === 'female') {
                            $data['mother_id'] = $parent->id;
                            if ($parent->spouse_id) {
                                $data['father_id'] = $parent->spouse_id;
                            }
                        } else {
                            $data['father_id'] = $parent->id;
                            if ($parent->spouse_id) {
                                $data['mother_id'] = $parent->spouse_id;
                            }
                        }
                        return $data;
                    }),
            ])
            ->recordActions([
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

