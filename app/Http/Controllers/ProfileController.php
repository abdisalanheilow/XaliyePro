<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldPhoto = $user->photo; // Capture old photo before filling

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle Photo Upload
        if ($request->file('photo')) {
            $file = $request->file('photo');

            // Delete old photo if it exists
            if ($oldPhoto && file_exists(public_path('upload/admin_images/'.$oldPhoto))) {
                @unlink(public_path('upload/admin_images/'.$oldPhoto));
            }

            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $user->photo = $filename;
        } else {
            // If no new photo, keep the old one (fill might have set it to null if validated)
            $user->photo = $oldPhoto;
        }

        $user->save();

        return Redirect::route('profile.edit')->with([
            'status' => 'profile-updated',
            'message' => 'Profile updated successfully',
            'title' => 'Profile Updated',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
