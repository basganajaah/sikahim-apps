@extends('adminlte::page')

@section('title', 'Sikahim | Dashboard Kegiatan')

@section('content_header')
    <x-header title="Dashboard Kegiatan" breadcrumb="Dashboard Kegiatan" />
@stop

@section('content')
    <x-page-hero
        eyebrow="Monitoring Agenda"
        title="Ringkasan Kegiatan"
        description="Pantau status agenda masjid dan tindak lanjuti kegiatan yang membutuhkan perhatian."
        :action="route('create-kegiatan')"
        action-text="Tambah Kegiatan"
    />
    <div class="row">
        <div class="col-md-6 col-lg-4">
            <x-adminlte-small-box title="{{ $newCount }}" text="Kegiatan Menunggu Verifikasi" icon="fas fa-search-plus" theme="primary" url="{{ route('list-kegiatan') }}" url-text="Selengkapnya"/>
        </div>
        <div class="col-md-6 col-lg-4">
            <x-adminlte-small-box title="{{ $ongoingCount }}" text="Kegiatan Berlangsung" icon="fas fa-hourglass-half" theme="warning" url="{{ route('list-kegiatan') }}" url-text="Selengkapnya"/>
        </div>
        <div class="col-md-6 col-lg-4">
            <x-adminlte-small-box title="{{ $completedCount }}" text="Kegiatan Selesai" icon="fas fa-check-double" theme="success" url="{{ route('list-kegiatan') }}" url-text="Selengkapnya"/>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
