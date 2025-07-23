<?php


namespace App\Services;

use App\Enums\ProposalStatus;
use App\Models\Call;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CallService
{

    public static function checkIfDatesOverlap($startDate, $endDate, $recordId = null)
    {


        $calls = Call::where(function ($query) use ($startDate, $endDate) {
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })->orWhere(function ($query) use ($startDate, $endDate) {
                $query
                    ->where('start_date', '>=', $startDate)
                    ->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate)
                    ->where('end_date', '<=', $endDate);
            })->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                    ->where('end_date', '>=', $endDate);
            });
        });


        if ($recordId != null) {
            $calls->where(function ($query) use ($recordId) {
                $query->where('id', '<>', $recordId);
            });
        }

        return $calls->count() != 0;
    }

    public static function getClosedCalls(): Collection|null
    {
        $calls = Call::where('end_date', '<', Carbon::today())
            ->where('closing_procedures_carried_out', false)
            ->orderBy('end_date', 'desc')
            ->get();


        if ($calls->isEmpty()) {
            return null;
        }
        return $calls;
    }

    public static function getEndedCalls(): Collection|null
    {
        $calls = Call::where('end_date', '<', Carbon::today())
            ->orderBy('end_date', 'desc')
            ->get();


        if ($calls->isEmpty()) {
            return null;
        }
        return $calls;
    }

    public static function getOpenCalls(): Collection|null
    {
        $calls = Call::whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::today())->get();

        if ($calls->isEmpty()) {
            return null;
        }
        return $calls;
    }

    public static function getOpenCall(): Call|null{
        $calls = self::getOpenCalls();
        return $calls ? $calls->first() : null;
    }

    public static function getNextCalls(): Collection|null
    {
        $calls = Call::whereDate('start_date', '>', Carbon::now())
            ->orderBy('start_date', 'asc')->get();

        if ($calls->isEmpty()) {
            return null;
        }
        return $calls;
    }

    public static function getNextCall(): Call|null {
        $calls = self::getNextCalls();
        return $calls ? $calls->first() : null;
    }

    public static function haveAllProposalsBeenEvaluatedForFeasibility(Call $call): bool
    {
        $hasBeenEvaluated = true;
        foreach ($call->proposals as $proposal) {
            $hasBeenEvaluated &= $proposal->hasBeenInStatus(ProposalStatus::FEASIBLE->value);
        }
        return $hasBeenEvaluated;
    }
}
