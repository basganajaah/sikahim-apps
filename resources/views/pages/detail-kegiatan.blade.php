@extends('adminlte::page')

@section('title', 'Detail Kegiatan')

@section('content_header')
    <x-header title="Detail Kegiatan" breadcrumb="Detail Kegiatan" />
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">{{ $kegiatan->activity_name }}</h3>
            @if (auth()->user()?->role === 'admin')
                <a href="{{ route('kegiatan.edit', $kegiatan) }}" class="btn btn-warning btn-sm ml-auto">
                    <i class="fas fa-pen mr-1"></i> Edit Kegiatan
                </a>
            @endif
        </div>
        <div class="card-body">
            @if ($kegiatan->thumbnail)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $kegiatan->thumbnail) }}" alt="{{ $kegiatan->activity_name }}" class="img-fluid rounded" style="max-height: 280px; object-fit: cover;">
                </div>
            @endif
            <dl class="row">
                <dt class="col-sm-3">Kategori</dt>
                <dd class="col-sm-9">{{ $kegiatan->kategori?->category_name ?? '-' }}</dd>

                <dt class="col-sm-3">Tags</dt>
                <dd class="col-sm-9">
                    @forelse ($kegiatan->tags as $tag)
                        <span class="badge badge-info mr-1">{{ $tag->tag_name }}</span>
                    @empty
                        <span class="text-muted">-</span>
                    @endforelse
                </dd>

                <dt class="col-sm-3">Penyelenggara</dt>
                <dd class="col-sm-9">{{ $kegiatan->penyelenggara }}</dd>

                <dt class="col-sm-3">Waktu</dt>
                <dd class="col-sm-9">{{ $kegiatan->datetime_started->format('d M Y H:i') }} - {{ $kegiatan->datetime_finished->format('d M Y H:i') }}</dd>

                <dt class="col-sm-3">Lokasi</dt>
                <dd class="col-sm-9">{{ $kegiatan->location }}</dd>

                <dt class="col-sm-3">Peserta</dt>
                <dd class="col-sm-9">{{ $kegiatan->pendaftaran->count() }} / {{ $kegiatan->max_participant ?? 'Tanpa batas' }}</dd>

                <dt class="col-sm-3">Status / Verifikasi</dt>
                <dd class="col-sm-9">{{ $kegiatan->status }} / {{ $kegiatan->verification }}</dd>

                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $kegiatan->description }}</dd>

                <dt class="col-sm-3">Materi Kegiatan</dt>
                <dd class="col-sm-9">
                    @if (!empty($kegiatan->materi))
                        <ul class="list-unstyled mb-0">
                            @foreach ($kegiatan->materi as $index => $materiPath)
                                <li>
                                    <a href="{{ asset('storage/' . $materiPath) }}" target="_blank" rel="noopener">
                                        <i class="fas fa-file-download mr-1"></i> Unduh Materi {{ $index + 1 }} ({{ basename($materiPath) }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">Tidak ada materi terlampir</span>
                    @endif
                </dd>
            </dl>
        </div>
        @if (auth()->user()?->role === 'admin')
            <div class="card-footer">
                <form method="POST" action="{{ route('kegiatan.update-status', $kegiatan) }}" class="form-inline">
                    @csrf
                    @method('PATCH')
                    <select name="verification" class="form-control form-control-sm mr-2">
                        @foreach (['Menunggu verifikasi', 'Terverifikasi', 'Ditolak'] as $v)
                            <option value="{{ $v }}" @selected($kegiatan->verification === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-control form-control-sm mr-2">
                        @foreach (['Belum Mulai', 'Berlangsung', 'Selesai', 'Ditunda'] as $s)
                            <option value="{{ $s }}" @selected($kegiatan->status === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Status</button>
                </form>
            </div>
        @endif
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title mb-0">Daftar Peserta Terdaftar ({{ $kegiatan->pendaftaran->count() }})</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nama Peserta (registrant_name)</th>
                        <th>Usia</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatan->pendaftaran as $peserta)
                        <tr>
                            <td>{{ $peserta->registrant_name }}</td>
                            <td>{{ $peserta->age }} tahun</td>
                            <td>{{ $peserta->gender ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ $peserta->registration_date?->format('d M Y') }}</td>
                            <td><span class="badge badge-success">{{ $peserta->registration_status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada peserta terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
