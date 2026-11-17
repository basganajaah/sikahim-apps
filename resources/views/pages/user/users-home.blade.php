@extends('adminlte::page')

@section('title', 'SIKAHIM | Agenda Jamaah')

@section('content_header')
    <x-header title="Agenda Jamaah" breadcrumb="Beranda" />
@stop

@section('content')
    <x-page-hero
        eyebrow="Sistem Informasi Kegiatan dan Agenda Harian Masjid"
        title="Assalamu'alaikum, {{ auth()->user()->name }}"
        description="Temukan kegiatan masjid yang terverifikasi dan ikut berkontribusi dalam agenda kebaikan bersama."
        :action="route('user.pendaftaran')"
        action-text="Lihat Pendaftaran Saya"
    />

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('users-home') }}">
                <div class="form-row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Cari nama kegiatan...">
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="category_id" class="form-control form-control-sm">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories ?? [] as $cat)
                                <option value="{{ $cat->category_id }}" @selected(request('category_id') == $cat->category_id)>{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="tag_id" class="form-control form-control-sm">
                            <option value="">Semua Tag</option>
                            @foreach ($tags ?? [] as $tag)
                                <option value="{{ $tag->tag_id }}" @selected(request('tag_id') == $tag->tag_id)>{{ $tag->tag_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" name="location" class="form-control form-control-sm" value="{{ request('location') }}" placeholder="Filter lokasi...">
                    </div>
                </div>
                <div class="form-row align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Status Aktif (Default)</option>
                            @foreach (['Belum Mulai', 'Berlangsung', 'Selesai', 'Ditunda'] as $st)
                                <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-9 text-md-right">
                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('users-home') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-1">Kegiatan Terdekat</h2>
            <p class="text-muted mb-0">Pilih kegiatan dan daftarkan diri Anda.</p>
        </div>
        <span class="badge badge-light border">{{ $kegiatan->count() }} kegiatan</span>
    </div>

    <div class="row">
        @forelse ($kegiatan as $item)
            <div class="col-md-6 col-xl-4 mb-4">
                <article class="card sikahim-event-card mb-0">
                    @if ($item->thumbnail)
                        <img class="card-img-top" style="height: 150px; object-fit: cover;" src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->activity_name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 150px;">
                            <i class="fas fa-mosque fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="sikahim-event-date">{{ $item->datetime_started->format('d M Y, H:i') }}</span>
                            <span class="badge badge-success">{{ $item->status }}</span>
                        </div>
                        <h3 class="h5">
                            <a href="{{ route('detail-kegiatan', $item) }}" class="text-dark">{{ $item->activity_name }}</a>
                        </h3>
                        <p class="small text-muted mb-1"><i class="fas fa-tag mr-2 text-info"></i>{{ $item->kategori?->category_name ?? '-' }}</p>
                        <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt mr-2 text-success"></i>{{ $item->location }}</p>
                        <p class="small text-muted mb-3"><i class="fas fa-user-friends mr-2 text-success"></i>{{ $item->remaining_slots === null ? 'Tanpa batas peserta' : $item->remaining_slots . ' slot tersisa' }}</p>
                        <p class="text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($item->description, 110) }}</p>
                        <div class="mb-2">
                            <a href="{{ route('detail-kegiatan', $item) }}" class="btn btn-outline-secondary btn-xs">Detail & Materi</a>
                            <a href="{{ $item->google_calendar_url }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-xs">
                                <i class="fab fa-google"></i> Kalender
                            </a>
                        </div>
                        <form method="POST" action="{{ route('user.pendaftaran.store', $item) }}" class="border-top pt-3">
                            @csrf
                            <div class="form-row">
                                <div class="col-7"><input class="form-control form-control-sm" name="registrant_name" value="{{ old('registrant_name', auth()->user()->name) }}" placeholder="Nama peserta" required></div>
                                <div class="col-5"><input class="form-control form-control-sm" type="number" name="age" min="1" max="120" placeholder="Usia" required></div>
                            </div>
                            <div class="form-row mt-2">
                                <div class="col-7"><select class="form-control form-control-sm" name="gender" required><option value="">Jenis kelamin</option><option value="1">Laki-laki</option><option value="0">Perempuan</option></select></div>
                                <div class="col-5"><button class="btn btn-primary btn-sm btn-block" type="submit"><i class="fas fa-hand-holding-heart mr-1"></i>Daftar</button></div>
                            </div>
                        </form>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h3 class="h5">Belum ada agenda tersedia</h3>
                        <p class="text-muted mb-0">Kegiatan terverifikasi akan muncul di halaman ini.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
