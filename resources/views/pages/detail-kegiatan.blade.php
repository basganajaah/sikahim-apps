@extends('adminlte::page')

@section('title', 'Detail Kegiatan')

@section('content_header')
    <x-header title="Detail Kegiatan" breadcrumb="Detail Kegiatan" />
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h3 class="card-title mb-0">{{ $kegiatan->activity_name }}</h3>
            <div class="ml-auto">
                <a href="{{ $kegiatan->google_calendar_url }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm mr-1">
                    <i class="fab fa-google mr-1"></i> Tambah ke Google Calendar
                </a>
                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('kegiatan.edit', $kegiatan) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-pen mr-1"></i> Edit Kegiatan
                    </a>
                @endif
            </div>
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

                <dt class="col-sm-3">Peserta & Sisa Kuota</dt>
                <dd class="col-sm-9">
                    {{ $kegiatan->pendaftaran->whereNotIn('registration_status', ['Dibatalkan', 'Ditolak'])->count() }} / {{ $kegiatan->max_participant ?? 'Tanpa batas' }}
                    <span class="badge badge-secondary ml-2">
                        {{ $kegiatan->remaining_slots === null ? 'Tanpa batas kuota' : $kegiatan->remaining_slots . ' slot tersisa' }}
                    </span>
                </dd>

                <dt class="col-sm-3">Status / Verifikasi</dt>
                <dd class="col-sm-9">
                    <span class="badge badge-info">{{ $kegiatan->status }}</span> /
                    <span class="badge badge-{{ $kegiatan->verification === 'Terverifikasi' ? 'success' : ($kegiatan->verification === 'Ditolak' ? 'danger' : 'warning') }}">{{ $kegiatan->verification }}</span>
                    @if ($kegiatan->verification === 'Ditolak' && $kegiatan->rejection_reason)
                        <div class="text-danger small mt-1">
                            <strong>Alasan Penolakan:</strong> {{ $kegiatan->rejection_reason }}
                        </div>
                    @endif
                </dd>

                <dt class="col-sm-3">Status Pendaftaran Anda</dt>
                <dd class="col-sm-9">
                    @if (isset($userRegistration) && $userRegistration)
                        <span class="badge badge-primary">{{ $userRegistration->registration_status }}</span>
                        @if ($userRegistration->notes)
                            <small class="text-muted ml-2">({{ $userRegistration->notes }})</small>
                        @endif
                    @else
                        <span class="badge badge-light border">Belum Terdaftar</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $kegiatan->description }}</dd>

                <dt class="col-sm-3">Materi Kegiatan</dt>
                <dd class="col-sm-9">
                    @if (!empty($kegiatan->materi))
                        <ul class="list-unstyled mb-0">
                            @foreach ($kegiatan->materi as $index => $materiPath)
                                <li>
                                    <a href="{{ route('kegiatan.materi.download', [$kegiatan, $index]) }}" data-file="{{ $materiPath }}">
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

            {{-- Tombol Daftar atau Batal Daftar untuk User --}}
            <div class="border-top pt-3 mt-3">
                @if (isset($userRegistration) && $userRegistration && $userRegistration->registration_status !== 'Dibatalkan')
                    @if ($kegiatan->status !== 'Selesai')
                        <form method="POST" action="{{ route('user.pendaftaran.cancel', $userRegistration) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times-circle mr-1"></i> Batalkan Pendaftaran Saya
                            </button>
                        </form>
                    @endif
                @elseif ($kegiatan->verification === 'Terverifikasi' && !in_array($kegiatan->status, ['Ditunda', 'Selesai']) && !$kegiatan->datetime_started->isPast() && ($kegiatan->remaining_slots === null || $kegiatan->remaining_slots > 0))
                    <form method="POST" action="{{ route('user.pendaftaran.store', $kegiatan) }}" class="form-inline">
                        @csrf
                        <input type="text" name="registrant_name" class="form-control form-control-sm mr-2 mb-2" value="{{ old('registrant_name', auth()->user()->name) }}" placeholder="Nama peserta" required>
                        <input type="number" name="age" min="1" max="120" class="form-control form-control-sm mr-2 mb-2" placeholder="Usia" required style="width: 90px;">
                        <select name="gender" class="form-control form-control-sm mr-2 mb-2" required>
                            <option value="">Jenis kelamin</option>
                            <option value="1">Laki-laki</option>
                            <option value="0">Perempuan</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm mb-2">
                            <i class="fas fa-hand-holding-heart mr-1"></i> Daftar Kegiatan Ini
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (auth()->user()?->role === 'admin')
            <div class="card-footer">
                <form method="POST" action="{{ route('kegiatan.update-status', $kegiatan) }}" class="form-inline">
                    @csrf
                    @method('PATCH')
                    <select name="verification" class="form-control form-control-sm mr-2 mb-2">
                        @foreach (['Menunggu verifikasi', 'Terverifikasi', 'Ditolak'] as $v)
                            <option value="{{ $v }}" @selected($kegiatan->verification === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-control form-control-sm mr-2 mb-2">
                        @foreach (['Belum Mulai', 'Berlangsung', 'Selesai', 'Ditunda'] as $s)
                            <option value="{{ $s }}" @selected($kegiatan->status === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="rejection_reason" class="form-control form-control-sm mr-2 mb-2" value="{{ old('rejection_reason', $kegiatan->rejection_reason) }}" placeholder="Alasan penolakan (wajib jika Ditolak)" style="min-width: 240px;">
                    <button type="submit" class="btn btn-primary btn-sm mb-2">Simpan Status</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Ringkasan Evaluasi & Komentar Peserta --}}
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                Evaluasi & Rating Peserta
                @if ($kegiatan->evaluasi->count() > 0)
                    <span class="badge badge-warning ml-2">{{ round($kegiatan->evaluasi->avg('rating'), 1) }} / 5 ({{ $kegiatan->evaluasi->count() }} ulasan)</span>
                @endif
            </h3>
            <a href="{{ route('evaluation', $kegiatan) }}" class="btn btn-outline-primary btn-sm ml-auto">
                <i class="fas fa-star mr-1"></i> Halaman Evaluasi
            </a>
        </div>
        <div class="card-body">
            @forelse ($kegiatan->evaluasi as $evaluasi)
                <div class="border-bottom mb-2 pb-2">
                    <strong>{{ $evaluasi->user?->name ?? 'Peserta' }}</strong>
                    <span class="badge badge-primary ml-1">{{ $evaluasi->rating }}/5</span>
                    <p class="mb-0 small text-muted">{{ $evaluasi->review }}</p>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada evaluasi untuk kegiatan ini.</p>
            @endforelse
        </div>
    </div>

    {{-- Daftar Peserta --}}
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
                            <td><span class="badge badge-info">{{ $peserta->registration_status }}</span></td>
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
