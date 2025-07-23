<?php

namespace App\Http\Middleware;

use App\Enums\ProposalStatus;
use App\Models\Call;
use App\Models\Proposal;
use App\Models\ProposalService;
use App\Services\ProposalService as ServicesProposalService;
use Carbon\Carbon;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Actions\Action;

class EnsureUserCanSubmitProposals
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user->can('submit proposals') || !$user->hasVerifiedEmail()) {


            if (!$user->hasVerifiedEmail()){
                $body = 'You haven\'t verified your email!';

                Notification::make()
                ->title('Cannot create proposal!')
                ->body($body)
                ->actions([
                    Action::make('Verify your email')
                        ->button()
                        ->url('/email/verify', shouldOpenInNewTab: false)
                ])
                ->send();

            } else {
                $body = 'Please contact the Help Desk';
                Notification::make()
                ->title('You are not allowed to create proposals!')
                ->body($body)
                ->send();
            }


            // abort(403);
            return redirect('/dashboard')->with('notification','You cannot create new proposals!');;
        }
        $can_open_proposal = ServicesProposalService::canSubmitProposal();
        if (!$can_open_proposal['can_open']) {
            switch ($can_open_proposal['motivation']) {
                case 'no_open_calls':
                    Notification::make()
                        ->title('No open calls found!')
                        ->body('Sorry, you cannot submit a new proposal because there are no open calls at the moment.')
                        ->send();
                    break;
                case 'proposal_already_opened':
                    Notification::make()
                        ->title('A proposal has already been submitted!')
                        ->body('Sorry, you cannot submit a new proposal because you already have one under evaluation.')
                        ->actions([
                            Action::make('Check your proposals')
                                ->button()
                                ->url('/dashboard/my-proposals', shouldOpenInNewTab: false)
                        ])
                        ->send();
                    break;
                case 'proposal_in_draft':
                    return $next($request);
                    break;
            }
            return redirect('/dashboard')->with('error', 'You cannot create new proposals!');
        }

        if (!$user->complete_profile) {
            return redirect(route('wizard'))->with('was_creating_proposal', 'true');
        }

        return $next($request);
    }
}
