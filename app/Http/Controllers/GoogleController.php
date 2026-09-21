<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Send the user to Google to sign in.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the user returning from Google.
     *
     * Account-linking rules, applied in this order:
     *
     * 1. If a user already exists with this google_id, log them in.
     * 2. Otherwise, if a user exists with this email, link the two
     *    accounts by saving the google_id, then log them in.
     * 3. Otherwise, create a new user with no local password.
     *
     * An existing password is never overwritten.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::warning('Google OAuth callback failed.', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Google sign-in could not be completed. Please try again.',
                ]);
        }

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName() ?: 'LandSure User';
        $avatar = $googleUser->getAvatar();

        /*
         * Rule 1: existing Google user.
         */
        if ($googleId) {
            $existingByGoogleId = User::where('google_id', $googleId)->first();

            if ($existingByGoogleId) {
                Auth::login($existingByGoogleId, true);

                request()->session()->regenerate();

                return redirect()->intended(route('parcels.index'));
            }
        }

        /*
         * Rule 2: existing local user with the same email.
         *
         * Google has already verified this email. We link the
         * accounts by saving the google_id. We do not touch the
         * existing password.
         */
        if ($email) {
            $existingByEmail = User::where('email', $email)->first();

            if ($existingByEmail) {
                $existingByEmail->google_id = $googleId;
                $existingByEmail->save();

                Auth::login($existingByEmail, true);

                request()->session()->regenerate();

                return redirect()->intended(route('parcels.index'));
            }
        }

        /*
         * Rule 3: brand new user via Google.
         *
         * Password is intentionally left null. The User model's
         * password accessor stores null as null, so this user has
         * no local password until they set one.
         */
        $newUser = User::create([
            'name' => $name,
            'email' => $email,
            'password' => null,
            'google_id' => $googleId,
        ]);

        /*
         * Google already verified this email, so mark it verified
         * locally as well. This keeps Breeze's "verified" checks
         * happy if you ever enable email verification.
         */
        if ($email) {
            $newUser->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        Auth::login($newUser, true);

        request()->session()->regenerate();

        return redirect()->intended(route('parcels.index'));
    }
}