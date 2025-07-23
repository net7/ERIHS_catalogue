<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Fortify;

class RegisterResponse implements RegisterResponseContract
{
    private $guard;

    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    public function toResponse($request)
    {
        //$this->guard->logout(); // logs out the session
        //$route = auth()->user()->first_login ? 'wizard' : 'dashboard';
        //return redirect('/email/verify');
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect('/email/verify');
       /* return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(Fortify::redirects('/email/verify'));*/
    }
}
