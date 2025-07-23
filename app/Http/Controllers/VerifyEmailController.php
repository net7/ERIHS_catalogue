<?php

namespace App\Http\Controllers;

use App\Http\Responses\VerifyEmailResponse;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
//use Laravel\Fortify\Contracts\VerifyEmailResponse;

use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            throw new AuthorizationException();
        }

        if ($user->hasVerifiedEmail()) {
            Auth::loginUsingId($user->id, $remember = true);
            return app(VerifyEmailResponse::class);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        Auth::loginUsingId($user->id, $remember = true);

        return app(VerifyEmailResponse::class);
    }
}
