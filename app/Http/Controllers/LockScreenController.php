<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    /**
     * Verify password and unlock the session.
     */
    public function unlock(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            // Password correct — redirect to dashboard
            return redirect()->intended(route('dashboard'));
        }

        // Password incorrect — redirect back with error
        return redirect()->route('lock-screen')
            ->with('lock_error', 'Incorrect password. Please try again.');
    }
}
