<?php

namespace App\Http\Responses;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    public function toResponse($request)
    {
        $route = auth()->user()->first_login ? 'wizard' : 'dashboard';
        return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : redirect()->route($route);
    }
}
