<?php

namespace App\Filament\Pages;

use App\Models\FamilyMember;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class FamilyTree extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Family Tree';
    protected static ?string $title = 'Family Tree';
    protected string $view = 'filament.pages.family-tree';

    public function getTreeData(): array
    {
        return FamilyMember::all()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->full_name,
                'nickname' => $member->nickname,
                'gender' => $member->gender,
                'birth' => $member->birth_date?->format('Y'),
                'death' => $member->death_date?->format('Y'),
                'father' => $member->father_id,
                'mother' => $member->mother_id,
                'spouse' => $member->spouse_id,
                'photo' => $member->avatar_path ? asset('storage/' . $member->avatar_path) : null,
            ];
        })->values()->toArray();
    }
}
