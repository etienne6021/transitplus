<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SydoniaQuickImport extends Widget
{
    protected static string $view = 'filament.widgets.sydonia-quick-import';
    
    protected static ?int $sort = -5; // En haut du dashboard

    protected int | string | array $columnSpan = 'full';
}
