<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Services\CallService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\HtmlString;

class CallsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?string $maxHeight = '300px';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;


    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE]);
    }

    public function getHeading(): ?string
    {
        return 'Number of proposals in the past ' . config('app.number_of_calls_in_chart_widget'). ' calls';
    }
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                // 'title' => [
                //     'display' => true,
                //     'text' => 'Number of proposals in past calls',
                // ],
            ],
            'scales' => [
                'y' => [
                
                    'ticks'=> [
                    // forces step size to be 1 units
                    'stepSize' => 1,
                    ],
                ]
            ],
        ];
    }
    protected function getData(): array
    {

        $data = [];
        $labels = [];
        $closedCalls = CallService::getEndedCalls();
        if ($closedCalls) {
            // for ($i=0; $i < max(5,$closedCalls->count()); $i++){

            $i = 0;

            foreach ($closedCalls as $call) {

                $data[] = $call->proposals->count();
                $labels[] = $call->name . " (ended on " . $call->end_date . ")";
                if (++$i >= config('app.number_of_calls_in_chart_widget')) {
                    break;
                }
            }


            $data = array_reverse($data);
            $labels = array_reverse($labels);
        }
        return [
            'datasets' => [
                [
                    // 'label' => 'Number of proposals in past calls',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
