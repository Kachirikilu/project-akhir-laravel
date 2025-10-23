@php
    $appName = env('APP_NAME');
@endphp
<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            {{-- <x-authentication-card-logo /> --}}
        </x-slot>
        @if ($appName == 'Al-Aqobah 1')
            <div class="mb-3">
                <img src="/images/masjid/Logo PT PUSRI.png" alt="PT. PUSRI" class="h-32 mx-auto">
            </div>
        @elseif ($appName == 'PT. Telkominfra')
            <div class="mb-3">
                <img src="/images/telkominfra/Logo PT Telkominfra.png" alt="PT. Telkominfra" class="h-32 mx-auto">
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="/login">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4 gap-6">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('Don\'t have an account yet?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Login') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
