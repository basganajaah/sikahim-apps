<x-guest-layout>
    <x-slot name="heading">Daftar akun jamaah</x-slot>
    <x-slot name="lead">Akun ini dipakai untuk mendaftar kegiatan dan memberi evaluasi.</x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="sk-field">
            <x-input-label for="name" :value="__('Username')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="sk-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="sk-field" style="display: grid; grid-template-columns: 1fr 1.6fr; gap: .75rem;">
            <div>
                <x-input-label for="age" :value="__('Usia')" />
                <x-text-input id="age" type="number" min="1" max="120" name="age" :value="old('age')" required />
                <x-input-error :messages="$errors->get('age')" />
            </div>
            <div>
                <x-input-label for="gender" :value="__('Jenis kelamin')" />
                <select id="gender" name="gender" class="sk-input" required>
                    <option value="">{{ __('Pilih') }}</option>
                    <option value="1" @selected(old('gender') === '1')>{{ __('Laki-laki') }}</option>
                    <option value="0" @selected(old('gender') === '0')>{{ __('Perempuan') }}</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" />
            </div>
        </div>

        <div class="sk-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="sk-field">
            <x-input-label for="password_confirmation" :value="__('Ulangi password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="sk-btn-block">{{ __('Buat akun') }}</x-primary-button>
    </form>

    <p class="sk-auth-alt">
        {{ __('Sudah punya akun?') }} <a href="{{ route('login') }}">{{ __('Masuk') }}</a>
    </p>
</x-guest-layout>
