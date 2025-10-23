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
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <x-button class="ms-4">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
