<?php

namespace App\Filament\Pages;

use App\Models\FamilyMember;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class FamilyTree extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Pohon Silsilah';
    protected static ?string $title = 'Pohon Silsilah Keluarga';
    protected static UnitEnum|string|null $navigationGroup = 'Silsilah & Keluarga';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.family-tree';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_json')
                ->label('Ekspor Cadangan (JSON)')
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

            Action::make('import_json')
                ->label('Impor Cadangan')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('warning')
                ->modalHeading('Impor Cadangan Pohon Silsilah')
                ->modalDescription('Unggah berkas JSON cadangan Family Vault yang sebelumnya diekspor.')
                ->modalSubmitActionLabel('Mulai Pemulihan')
                ->modalCancelActionLabel('Batal')
                ->form([
                    FileUpload::make('backup_file')
                        ->label('Berkas Cadangan JSON')
                        ->acceptedFileTypes(['application/json', 'text/plain'])
                        ->disk('local')
                        ->directory('temp-imports')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['backup_file']);
                    if (! file_exists($path)) {
                        Notification::make()->title('Berkas yang diunggah tidak ditemukan')->danger()->send();
                        return;
                    }

                    $raw = file_get_contents($path);
                    $json = json_decode($raw, true);
                    Storage::disk('local')->delete($data['backup_file']);

                    $members = $json['family_members'] ?? (is_array($json) ? $json : null);
                    if (! $members || ! is_array($members)) {
                        Notification::make()->title('Struktur berkas JSON tidak valid')->danger()->send();
                        return;
                    }

                    DB::transaction(function () use ($members) {
                        // First pass: create/update member records
                        foreach ($members as $m) {
                            FamilyMember::updateOrCreate(
                                ['id' => $m['id']],
                                [
                                    'full_name' => $m['full_name'] ?? $m['name'] ?? 'Unknown',
                                    'nickname' => $m['nickname'] ?? null,
                                    'gender' => $m['gender'] ?? 'male',
                                    'birth_date' => $m['birth_date'] ?? null,
                                    'death_date' => $m['death_date'] ?? null,
                                    'phone' => $m['phone'] ?? null,
                                    'notes' => $m['notes'] ?? null,
                                ]
                            );
                        }

                        // Second pass: wire relationships
                        foreach ($members as $m) {
                            $update = [];
                            if (array_key_exists('father_id', $m) || array_key_exists('father', $m)) {
                                $update['father_id'] = $m['father_id'] ?? $m['father'] ?? null;
                            }
                            if (array_key_exists('mother_id', $m) || array_key_exists('mother', $m)) {
                                $update['mother_id'] = $m['mother_id'] ?? $m['mother'] ?? null;
                            }
                            if (array_key_exists('spouse_id', $m) || array_key_exists('spouse', $m)) {
                                $update['spouse_id'] = $m['spouse_id'] ?? $m['spouse'] ?? null;
                            }
                            if (! empty($update)) {
                                FamilyMember::where('id', $m['id'])->update($update);
                            }
                        }
                    });

                    Notification::make()
                        ->title('Cadangan silsilah keluarga berhasil dipulihkan!')
                        ->success()
                        ->send();
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
