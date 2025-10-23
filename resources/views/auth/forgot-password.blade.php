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



        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
