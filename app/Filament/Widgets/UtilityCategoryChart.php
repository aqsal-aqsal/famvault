<?php

namespace App\Filament\Widgets;

use App\Models\UtilityRecord;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UtilityCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Utilitas Rumah Tangga';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $categories = UtilityRecord::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $categoryTranslations = [
            'Electricity' => 'Listrik (PLN)',
            'Water' => 'Air (PDAM)',
            'Internet' => 'Internet & WiFi',
            'Insurance' => 'Asuransi',
            'Property Tax' => 'PBB',
            'Gas' => 'Gas',
            'Other' => 'Lainnya',
        ];

        $labels = array_map(function ($key) use ($categoryTranslations) {
            return $categoryTranslations[$key] ?? $key;
        }, array_keys($categories));

        $data = array_values($categories);

        $colorMap = [
            'Listrik (PLN)' => '#f59e0b',
            'Air (PDAM)' => '#0ea5e9',
            'Internet & WiFi' => '#6366f1',
            'Asuransi' => '#10b981',
            'PBB' => '#f43f5e',
            'Gas' => '#8b5cf6',
            'Lainnya' => '#71717a',
        ];

        $backgroundColors = array_map(function ($cat) use ($colorMap) {
            return $colorMap[$cat] ?? '#a1a1aa';
        }, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Akun',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
