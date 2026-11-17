@extends('adminlte::page')

@section('title', 'SIKAHIM | Profil Pengguna')

@section('content_header')
    <x-header title="Profil Pengguna" breadcrumb="Profil" />
@stop

@section('css')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@stop

@section('content')
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg card">
                <div class="max-w-xl card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg card">
                <div class="max-w-xl card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg card">
                <div class="max-w-xl card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
