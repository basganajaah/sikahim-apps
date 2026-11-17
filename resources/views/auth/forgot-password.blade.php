<x-guest-layout>
    <x-slot name="heading">Atur ulang password</x-slot>
    <x-slot name="lead">Masukkan email akun Anda. Kami kirim tautan untuk membuat password baru.</x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="sk-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="sk-btn-block">{{ __('Kirim tautan reset') }}</x-primary-button>
    </form>

    <p class="sk-auth-alt">
        <a href="{{ route('login') }}">{{ __('Kembali ke halaman masuk') }}</a>
    </p>
</x-guest-layout>
