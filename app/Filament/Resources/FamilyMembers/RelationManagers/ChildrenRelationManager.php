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

    protected static ?string $title = 'Anak-anak / Keturunan';

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
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=18181b&color=f59e0b'),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('nickname')
                    ->label('Panggilan')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                        default => $state,
                    }),

                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Anak')
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
                EditAction::make()->label('Ubah'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang Dipilih'),
                ]),
            ]);
    }
}
