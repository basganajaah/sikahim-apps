@extends('adminlte::page')

@section('title', 'SIKAHIM | Aktivitas Jamaah')

@section('content_header')
    <x-header title="Aktivitas Jamaah" breadcrumb="Aktivitas" />
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p class="mb-0">Lihat agenda kegiatan masjid terbaru di halaman <a href="{{ route('users-home') }}">Beranda Jamaah</a>.</p>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
