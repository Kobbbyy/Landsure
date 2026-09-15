<x-guest-layout>

    <div class="ls-auth-heading">

        <div class="ls-auth-eyebrow">
            Password reset
        </div>

        <h1 class="ls-auth-title">
            Forgot your password?
        </h1>

        <p class="ls-auth-subtitle">
            Enter the email address you used to register. We will
            send you a link to reset your password.
        </p>

    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">

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
            />

            <x-input-error :messages="$errors->get('email')" />

        </div>

        <x-primary-button>
            {{ __('Send reset link') }}
        </x-primary-button>

    </form>

    <div class="ls-auth-footer">

        Remembered your password?

        <a href="{{ route('login') }}">
            Back to login
        </a>

    </div>

</x-guest-layout>