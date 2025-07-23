<?php

namespace App\Filament\Widgets;

use App\Models\Proposal;
use App\Models\User;
use App\Services\CallService;
use App\Services\ProposalService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProposalsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;
    protected function getStats(): array
    {

        if (auth()->user()->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE])) {
            // TODO: return all proposals
            $proposals = Proposal::all();
            $title = "Total Proposals";
        } else {
            $proposals = ProposalService::getMySubmittedProposalQuery();
            $title = "Applications";
        }

        $proposalsInCall = [];
        for ($i = 6; $i >= 0; $i--) {
            $proposalsInCall[] = Proposal::where('published_at', '>=', Carbon::now()->subDays($i)->startOfDay()->toDateTimeString())
                ->where('published_at', '<=', Carbon::now()->subDays($i)->endOfDay()->toDateTimeString())
                ->count();
        }

        $call = CallService::getOpenCall();

        $res = [
            Stat::make($title, $proposals->count()),

        ];
        if (auth()->user()->hasAnyRole([User::ADMIN_ROLE, User::HELP_DESK_ROLE])) {
            if ($call) {
                $res[] =  Stat::make('Proposals in active call', $call->proposals->count());
            }
            $res[] = Stat::make('Proposals created', '', $proposalsInCall[count($proposalsInCall) - 1])
                ->description('today: ' .  $proposalsInCall[count($proposalsInCall) - 1])
                ->chart($proposalsInCall)->color('primary');
        }

        return $res;
    }
}
