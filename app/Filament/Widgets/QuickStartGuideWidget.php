<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickStartGuideWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-start-guide';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -10; // Placed at the very top of Dashboard
}
