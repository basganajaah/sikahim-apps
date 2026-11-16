@extends('adminlte::page')

@section('title', 'Sikahim | Dashboard')

@section('content_header')
    <x-header title="Dashboard" breadcrumb="Dashboard" />
@stop

@section('content')
    <x-page-hero
        eyebrow="Panel Pengelola Masjid"
        title="Dashboard SIKAHIM"
        description="Pantau agenda, pendaftaran, dan partisipasi jamaah dari satu tempat."
        :action="route('create-kegiatan')"
        action-text="Buat Kegiatan"
    />

    <div class="row mb-4 dashboard-metrics">
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-white">
                <div class="inner"><h3>{{ $totalKegiatan }}</h3><p>Total Kegiatan</p></div>
                <div class="icon metric-icon-primary"><i class="fas fa-calendar-alt"></i></div>
                <a href="{{ route('list-kegiatan') }}" class="small-box-footer">Lihat kegiatan <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-white">
                <div class="inner"><h3>{{ $pendingKegiatan }}</h3><p>Menunggu Verifikasi</p></div>
                <div class="icon metric-icon-warning"><i class="fas fa-hourglass-half"></i></div>
                <a href="{{ route('list-kegiatan') }}" class="small-box-footer">Periksa sekarang <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-white">
                <div class="inner"><h3>{{ $totalPendaftaran }}</h3><p>Total Pendaftaran</p></div>
                <div class="icon metric-icon-success"><i class="fas fa-users"></i></div>
                <a href="{{ route('list-kegiatan') }}" class="small-box-footer">Lihat peserta <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="small-box bg-white">
                <div class="inner"><h3>{{ $totalPengguna }}</h3><p>Jamaah Terdaftar</p></div>
                <div class="icon metric-icon-info"><i class="fas fa-user-friends"></i></div>
                <a href="{{ route('kelola-pengguna') }}" class="small-box-footer">Kelola jamaah <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="h4 mb-1">Akses Cepat</h2>
            <p class="text-muted mb-0">Kelola operasional masjid dengan lebih terarah.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-4">
            <div class="card dashboard-action-card">
                <div class="card-header">
                    <div class="dashboard-action-icon dashboard-action-icon-primary">
                        <i class="fas fa-plus"></i>
                    </div>
                    <p class="card-title">Buat Kegiatan</p>
                </div>
                <div class="card-body">
                    <p class="mb-0">Mulai membuat kegiatan baru di aplikasi dengan jadwal, kategori, kuota, dan materi.</p>
                </div>
                <a href="{{ route('create-kegiatan') }}" class="card-footer dashboard-action-link">
                    Buat kegiatan sekarang <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card dashboard-action-card">
                <div class="card-header">
                    <div class="dashboard-action-icon dashboard-action-icon-warning">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <p class="card-title">List Kegiatan</p>
                </div>
                <div class="card-body">
                    <p class="mb-0">Tinjau status verifikasi, jadwal, dan informasi kegiatan yang terdaftar.</p>
                </div>
                <a href="{{ route('list-kegiatan') }}" class="card-footer dashboard-action-link">
                    Lihat semua kegiatan <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card dashboard-action-card">
                <div class="card-header">
                    <div class="dashboard-action-icon dashboard-action-icon-success">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <p class="card-title">Kelola Pengguna</p>
                </div>
                <div class="card-body">
                    <p class="mb-0">Kelola data jamaah dan pantau pengguna yang terdaftar di SIKAHIM.</p>
                </div>
                <a href="{{ route('kelola-pengguna') }}" class="card-footer dashboard-action-link">
                    Kelola jamaah <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection