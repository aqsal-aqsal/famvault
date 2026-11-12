<?php

namespace App\Filament\Widgets;

use App\Models\VaultDocument;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DocumentCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Dokumen Brankas';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $categories = VaultDocument::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $categoryTranslations = [
            'identity' => 'Identitas',
            'vehicle' => 'Kendaraan',
            'property' => 'Properti',
            'education' => 'Pendidikan',
            'health' => 'Kesehatan',
            'other' => 'Lainnya',
        ];

        $labels = array_map(function ($key) use ($categoryTranslations) {
            return $categoryTranslations[$key] ?? ucfirst($key);
        }, array_keys($categories));

        $data = array_values($categories);

        $colorMap = [
            'Identitas' => '#0ea5e9',
            'Kendaraan' => '#f59e0b',
            'Properti' => '#10b981',
            'Pendidikan' => '#6366f1',
            'Kesehatan' => '#f43f5e',
            'Lainnya' => '#71717a',
        ];

        $backgroundColors = array_map(function ($cat) use ($colorMap) {
            return $colorMap[$cat] ?? '#a1a1aa';
        }, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Dokumen',
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
