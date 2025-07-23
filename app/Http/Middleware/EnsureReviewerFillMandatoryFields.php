<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureReviewerFillMandatoryFields
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->route()->getName() == 'livewire.update') {
            return $next($request);
        }
        $user = Auth::user();
        if ($user && $user->hasRole(User::REVIEWER_ROLE)) {
            if (!($user->terms_of_service && $user->confidentiality)) {
                $next($request);
                $user->refresh();
                if ($user->terms_of_service && $user->confidentiality) {
                    return $next($request);
                } else {
                    return redirect()->to(route('reviewer-terms-and-conditions'));
                }

            }
        }
        return $next($request);
    }
}
