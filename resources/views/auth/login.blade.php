<x-guest-layout>
    <x-slot name="heading">Masuk ke SIKAHIM</x-slot>
    <x-slot name="lead">Gunakan email yang Anda daftarkan.</x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="sk-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="sk-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="sk-auth-row">
            <label for="remember_me" class="sk-check">
                <input id="remember_me" type="checkbox" name="remember">
                <span>{{ __('Ingat saya') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">{{ __('Lupa password?') }}</a>
            @endif
        </div>

        <x-primary-button class="sk-btn-block">{{ __('Masuk') }}</x-primary-button>
    </form>

    @if (Route::has('register'))
        <p class="sk-auth-alt">
            {{ __('Belum punya akun?') }} <a href="{{ route('register') }}">{{ __('Daftar sebagai jamaah') }}</a>
        </p>
    @endif
</x-guest-layout>
