<?php

namespace App\Filament\Widgets;

use App\Models\Proposal;
use App\Models\User;
use App\Services\CallService;
use App\Services\ProposalService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CallsOverview extends BaseWidget
{

    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        $res = [];

        $call = CallService::getOpenCall();
        if ($call) {
            $res[] = Stat::make('Active call ends on', Carbon::parse($call->end_date)->format('j F Y'));
        } else {
            $res [] =  Stat::make('No open calls!', '');
        }
        $nextCalls = CallService::getNextCall();

        if ($nextCalls) {
            $res [] =  Stat::make('Next call opening on ', Carbon::parse($nextCalls->start_date)->format('j F Y'));
        } else {
            $res [] =  Stat::make('No future calls scheduled!','');
        }

        return $res;
    }
}
