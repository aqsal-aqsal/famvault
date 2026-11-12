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
            Stat::make('Family Members', FamilyMember::count())
                ->description('Total indexed in lineage')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('info'),

            Stat::make('Secured Documents', VaultDocument::count())
                ->description('AES-GCM 256 zero-knowledge')
                ->descriptionIcon(Heroicon::OutlinedLockClosed)
                ->color('success'),

            Stat::make('Expiring / Overdue', $expiringCount)
                ->description($expiringCount > 0 ? "Requires review within 30 days" : 'All documents up to date')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color($expiringCount > 0 ? 'danger' : 'success'),

            Stat::make('Utility Accounts', UtilityRecord::count())
                ->description('Active meters & policies')
                ->descriptionIcon(Heroicon::OutlinedBolt)
                ->color('warning'),
        ];
    }
}
