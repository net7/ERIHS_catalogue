<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class Help extends Widget
{
    protected static string $view = 'filament.widgets.help';
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';
}
