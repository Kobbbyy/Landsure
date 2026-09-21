<?php

use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ParcelController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
|
| These routes are reachable without logging in. The landing page and the
| methodology page are informational, so they stay public.
|
*/

Route::get('/', function () {
    return view('landsure.home');
})->name('home');

Route::get('/methodology', function () {
    return view('landsure.methodology');
})->name('methodology');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
|
| Parcel checking, saving, and viewing require a logged in user. The map
| page is where a new parcel is drawn, so it also requires login.
|
*/

Route::middleware('auth')->group(function () {

    Route::get('/map', function () {
        return view('landsure.map');
    })->name('map');

    Route::get('/parcels', [ParcelController::class, 'index'])
        ->name('parcels.index');

    Route::get('/parcels/{parcel}', [ParcelController::class, 'show'])
        ->name('parcels.show');

    Route::post('/parcels', [ParcelController::class, 'store'])
        ->name('parcels.store');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
|
| Breeze sends the user to /dashboard after login and after registration.
| LandSure does not have a separate dashboard, so we redirect to the
| user's parcel list instead.
|
*/

Route::get('/dashboard', function () {
    return redirect()->route('parcels.index');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
|
| These routes sit outside the auth middleware, because a logged-out
| visitor is exactly who needs to use them.
|
| /auth/google          -> redirect the user to Google to sign in
| /auth/google/callback -> receive the user back from Google
|
| The callback URL must exactly match the "Authorized redirect URI"
| configured in Google Cloud. For local development that is:
|   http://127.0.0.1:8000/auth/google/callback
|
*/

Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->name('google.login');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('google.callback');

/*
|--------------------------------------------------------------------------
| Profile routes (Breeze)
|--------------------------------------------------------------------------
|
| These are provided by Breeze and let the logged in user update their
| name, email, and password.
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth routes (Breeze)
|--------------------------------------------------------------------------
|
| Login, registration, password reset, email verification. These come
| from the file published by Breeze.
|
*/

require __DIR__.'/auth.php';