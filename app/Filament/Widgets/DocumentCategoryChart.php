<?php

namespace App\Filament\Widgets;

use App\Models\VaultDocument;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DocumentCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Secured Documents by Category';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $categories = VaultDocument::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $labels = array_map('ucfirst', array_keys($categories));
        $data = array_values($categories);

        $colorMap = [
            'Identity' => '#0ea5e9',
            'Vehicle' => '#f59e0b',
            'Property' => '#10b981',
            'Education' => '#6366f1',
            'Health' => '#f43f5e',
            'Other' => '#71717a',
        ];

        $backgroundColors = array_map(function ($cat) use ($colorMap) {
            return $colorMap[$cat] ?? '#a1a1aa';
        }, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Documents',
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
