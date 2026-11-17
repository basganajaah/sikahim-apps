@extends('adminlte::page')

@section('title', 'Sikahim | Dashboard')

@section('content_header')
    <x-header title="Dashboard" breadcrumb="Dashboard" />
@stop

@section('plugins.Chartjs', true)

@section('content')
    <x-page-hero
        eyebrow="Panel Pengelola Masjid"
        title="Dashboard SIKAHIM"
        description="Pantau agenda, pendaftaran, dan partisipasi jamaah dari satu tempat."
        :action="route('create-kegiatan')"
        action-text="Buat Kegiatan"
    />

    <div class="mb-3 text-right">
        <a href="{{ route('laporan.export-csv') }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-csv mr-1"></i> Export Laporan (CSV/Excel)
        </a>
    </div>

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
                <a href="{{ route('kelola-pendaftaran') }}" class="small-box-footer">Kelola peserta <i class="fas fa-arrow-right"></i></a>
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

    {{-- Grafik Kegiatan & Pendaftaran --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title mb-0">Grafik Jumlah Kegiatan per Bulan</h3></div>
                <div class="card-body">
                    <canvas id="chartKegiatanBulanan" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title mb-0">Grafik Jumlah Pendaftar per Kegiatan</h3></div>
                <div class="card-body">
                    <canvas id="chartPendaftarKegiatan" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Rekap Kategori & Evaluasi --}}
    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title mb-0">Rekap Kegiatan Berdasarkan Kategori</h3></div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Jumlah Kegiatan</th>
                                <th>Total Pendaftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapKategori ?? [] as $rk)
                                <tr>
                                    <td>{{ $rk['category_name'] }}</td>
                                    <td>{{ $rk['total_kegiatan'] }}</td>
                                    <td>{{ $rk['total_pendaftar'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title mb-0">Rekap Evaluasi dan Rating Kegiatan</h3></div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama Kegiatan</th>
                                <th>Status</th>
                                <th>Pendaftar</th>
                                <th>Ulasan</th>
                                <th>Rata-rata Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapEvaluasi ?? [] as $re)
                                <tr>
                                    <td>{{ $re['activity_name'] }}</td>
                                    <td><span class="badge badge-info">{{ $re['status'] }}</span></td>
                                    <td>{{ $re['total_pendaftar'] }}</td>
                                    <td>{{ $re['total_evaluasi'] }}</td>
                                    <td>{{ $re['avg_rating'] !== null ? $re['avg_rating'] . ' / 5' : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            var ctxMonthly = document.getElementById('chartKegiatanBulanan');
            if (ctxMonthly) {
                new Chart(ctxMonthly.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($monthlyLabels ?? []),
                        datasets: [{
                            label: 'Jumlah Kegiatan',
                            backgroundColor: '#0f766e',
                            data: @json($monthlyData ?? [])
                        }]
                    }
                });
            }

            var ctxReg = document.getElementById('chartPendaftarKegiatan');
            if (ctxReg) {
                new Chart(ctxReg.getContext('2d'), {
                    type: 'horizontalBar',
                    data: {
                        labels: @json($pendaftaranLabels ?? []),
                        datasets: [{
                            label: 'Jumlah Pendaftar',
                            backgroundColor: '#1d4ed8',
                            data: @json($pendaftaranData ?? [])
                        }]
                    }
                });
            }
        });
    </script>
@stop

@section('footer')
    @include('components.footer')
@endsection
