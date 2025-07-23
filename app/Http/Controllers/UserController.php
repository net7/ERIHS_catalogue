<?php

namespace App\Http\Controllers;

use App\Models\ConnectedAccount;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function resetPassword(Request $request) {
        if(count(ConnectedAccount::where('email', $request->email)->get()) > 0) {
            return redirect()->back()->with('message', 'warning');
        } else if(count(User::where('email', $request->email)->get()) > 0) {
            return redirect()->back()->with('message', 'success');
        } else {
            return redirect()->back()->with('message', 'danger');
        }
    }
}
