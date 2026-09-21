<x-guest-layout>

    <div class="ls-auth-heading">

        <div class="ls-auth-eyebrow">
            Welcome back
        </div>

        <h1 class="ls-auth-title">
            Log in to LandSure
        </h1>

        <p class="ls-auth-subtitle">
            Check land, review saved parcels, and continue your
            due diligence work.
        </p>

    </div>

    <x-auth-session-status :status="session('status')" />

    {{--
        Continue with Google
    --}}
    <a
        href="{{ route('google.login') }}"
        class="ls-google-button"
    >
        <span class="ls-google-icon" aria-hidden="true">

            <svg
                width="18"
                height="18"
                viewBox="0 0 18 18"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill="#4285F4"
                    d="M17.64 9.205c0-.639-.057-1.252-.164-1.841H9v3.481h4.844a4.14 4.14 0 0 1-1.796 2.716v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.614Z"
                />
                <path
                    fill="#34A853"
                    d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18Z"
                />
                <path
                    fill="#FBBC05"
                    d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332Z"
                />
                <path
                    fill="#EA4335"
                    d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58Z"
                />
            </svg>

        </span>

        <span>
            Continue with Google
        </span>
    </a>

    <div class="ls-auth-divider">

        <span class="ls-auth-divider-line"></span>

        <span class="ls-auth-divider-text">
            or
        </span>

        <span class="ls-auth-divider-line"></span>

    </div>

    <form method="POST" action="{{ route('login') }}">

        @csrf

        <div>

            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />

            <x-input-error :messages="$errors->get('email')" />

        </div>

        <div>

            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" />

        </div>

        <div class="ls-auth-inline">

            <label for="remember_me">

                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                >

                <span>
                    {{ __('Remember me') }}
                </span>

            </label>

            @if (Route::has('password.request'))

                <a href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>

            @endif

        </div>

        <x-primary-button>
            {{ __('Log in') }}
        </x-primary-button>

    </form>

    @if (Route::has('register'))

        <div class="ls-auth-footer">

            Don't have an account?

            <a href="{{ route('register') }}">
                Create one
            </a>

        </div>

    @endif

</x-guest-layout>