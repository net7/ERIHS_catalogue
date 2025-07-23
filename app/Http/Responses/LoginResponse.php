<?php

namespace App\Http\Responses;

use App\Models\User;
use App\Services\ERIHSLocalCartService;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();
        if ($user->hasRole(User::REVIEWER_ROLE)) {
            if ($user->terms_of_service) {
                $route = $user->first_login ? 'wizard' : 'dashboard';
            } else {
                $route = 'reviewer-terms-and-conditions';
            }
        } elseif ($user->hasRole(User::SERVICE_MANAGER)) {
            $organizations = $user->organizations()->get();
            foreach ($organizations as $organization) {
                if (!isset($organization->first_active_date)) {
                    $organization->first_active_date = now();
                }
                $organization->last_active_date = now();
                $organization->save();
            }
            $route = $user->first_login ? 'wizard' : 'dashboard';
        } else {
            $route = $user->first_login ? 'wizard' : 'dashboard';
        }

        ERIHSLocalCartService::transferToDbCart();

        $intendedUrl = session('url.intended');

        // Se la route è 'dashboard', uso prima la URL intended se esiste
        if ($route === 'dashboard' && $intendedUrl) {
            return redirect()->to($intendedUrl);
        }

        // Reindirizza alla route specifica, oppure alla URL intended se non esiste una route specifica
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->route($route);
    }
}
