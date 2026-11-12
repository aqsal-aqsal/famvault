<?php

namespace App\Filament\Widgets;

use App\Models\FamilyMember;
use App\Models\UtilityRecord;
use App\Models\VaultDocument;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VaultStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $expiringCount = VaultDocument::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();

        return [
            Stat::make('Anggota Keluarga', FamilyMember::count())
                ->description('Total terdaftar dalam silsilah')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('info'),

            Stat::make('Dokumen Terenkripsi', VaultDocument::count())
                ->description('Enkripsi AES-GCM 256 zero-knowledge')
                ->descriptionIcon(Heroicon::OutlinedLockClosed)
                ->color('success'),

            Stat::make('Mendekati Kedaluwarsa', $expiringCount)
                ->description($expiringCount > 0 ? "Perlu perpanjangan dalam 30 hari" : 'Semua dokumen aman & berlaku')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color($expiringCount > 0 ? 'danger' : 'success'),

            Stat::make('Akun Utilitas Rumah', UtilityRecord::count())
                ->description('Meteran & polis aktif terdata')
                ->descriptionIcon(Heroicon::OutlinedBolt)
                ->color('warning'),
        ];
    }
}
