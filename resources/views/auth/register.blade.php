<x-guest-layout>

    <div class="ls-auth-heading">

        <div class="ls-auth-eyebrow">
            Create your account
        </div>

        <h1 class="ls-auth-title">
            Get started with LandSure
        </h1>

        <p class="ls-auth-subtitle">
            Save parcels, run risk screening, and keep your land
            due diligence organised in one place.
        </p>

    </div>

    <form method="POST" action="{{ route('register') }}">

        @csrf

        <div>

            <x-input-label for="name" :value="__('Name')" />

            <x-text-input
                id="name"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error :messages="$errors->get('name')" />

        </div>

        <div>

            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
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
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password')" />

        </div>

        <div>

            <x-input-label for="password_confirmation" :value="__('Confirm password')" />

            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password_confirmation')" />

        </div>

        <x-primary-button>
            {{ __('Create account') }}
        </x-primary-button>

    </form>

    <div class="ls-auth-footer">

        Already have an account?

        <a href="{{ route('login') }}">
            Log in
        </a>

    </div>

</x-guest-layout>