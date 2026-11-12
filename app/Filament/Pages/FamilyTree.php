<?php

namespace App\Filament\Pages;

use App\Models\FamilyMember;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class FamilyTree extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Family Tree';
    protected static ?string $title = 'Interactive Family Tree';
    protected static UnitEnum|string|null $navigationGroup = 'Family Heritage';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.family-tree';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_json')
                ->label('Export Tree Backup')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(function (): StreamedResponse {
                    $members = FamilyMember::with(['documents', 'father', 'mother', 'spouse'])->get()->toArray();

                    return response()->streamDownload(function () use ($members) {
                        echo json_encode([
                            'version' => '1.0',
                            'exported_at' => now()->toIso8601String(),
                            'total_members' => count($members),
                            'family_members' => $members,
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }, 'family-vault-tree-backup-' . date('Y-m-d') . '.json', [
                        'Content-Type' => 'application/json',
                    ]);
                }),
        ];
    }

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
