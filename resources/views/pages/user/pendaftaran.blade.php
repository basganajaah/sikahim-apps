@extends('adminlte::page')

@section('title', 'Pendaftaran Saya')

@section('content_header')
    <x-header title="Pendaftaran Saya" breadcrumb="Pendaftaran" />
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Kegiatan</th>
                        <th>Jadwal & Lokasi</th>
                        <th>Detail Pendaftar</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $item)
                        <tr>
                            <td>
                                @if ($item->kegiatan)
                                    <a href="{{ route('detail-kegiatan', $item->kegiatan) }}" class="font-weight-bold">
                                        {{ $item->kegiatan->activity_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($item->kegiatan)
                                    <div>{{ $item->kegiatan->datetime_started->format('d M Y, H:i') }}</div>
                                    <small class="text-muted">{{ $item->kegiatan->location }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div>{{ $item->registrant_name }}</div>
                                <small class="text-muted">{{ $item->age }} thn &bull; {{ $item->gender ? 'Laki-laki' : 'Perempuan' }}</small>
                            </td>
                            <td>{{ $item->registration_date?->format('d M Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $item->registration_status === 'Diterima' ? 'success' : ($item->registration_status === 'Ditolak' || $item->registration_status === 'Dibatalkan' ? 'danger' : 'info') }}">
                                    {{ $item->registration_status }}
                                </span>
                            </td>
                            <td>{{ $item->notes ?? '-' }}</td>
                            <td>
                                @if ($item->kegiatan)
                                    <a href="{{ route('detail-kegiatan', $item->kegiatan) }}" class="btn btn-xs btn-outline-primary mr-1">
                                        Detail
                                    </a>
                                    @if ($item->kegiatan->status === 'Selesai' && in_array($item->registration_status, ['Terdaftar', 'Diterima']))
                                        <a href="{{ route('evaluation', $item->kegiatan) }}" class="btn btn-xs btn-warning mr-1">
                                            <i class="fas fa-star mr-1"></i> Evaluasi
                                        </a>
                                    @endif
                                    @if ($item->registration_status !== 'Dibatalkan' && $item->kegiatan->status !== 'Selesai')
                                        <form method="POST" action="{{ route('user.pendaftaran.cancel', $item) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-xs btn-danger">
                                                Batalkan
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada pendaftaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
