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