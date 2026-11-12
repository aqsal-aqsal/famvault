<?php

namespace App\Filament\Widgets;

use App\Models\UtilityRecord;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UtilityCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Household Utilities Distribution';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $categories = UtilityRecord::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $labels = array_keys($categories);
        $data = array_values($categories);

        $colorMap = [
            'Electricity' => '#f59e0b',
            'Water' => '#0ea5e9',
            'Internet' => '#6366f1',
            'Insurance' => '#10b981',
            'Property Tax' => '#f43f5e',
            'Gas' => '#8b5cf6',
            'Other' => '#71717a',
        ];

        $backgroundColors = array_map(function ($cat) use ($colorMap) {
            return $colorMap[$cat] ?? '#a1a1aa';
        }, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Accounts',
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
