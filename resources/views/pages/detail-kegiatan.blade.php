@extends('adminlte::page')

@section('title', 'Detail Kegiatan')

@section('content_header')
    <x-header title="Detail Kegiatan" breadcrumb="Detail Kegiatan" />
@stop

@section('content')
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ $kegiatan->activity_name }}</h3></div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $kegiatan->kategori?->category_name ?? '-' }}</dd>
                <dt class="col-sm-3">Penyelenggara</dt><dd class="col-sm-9">{{ $kegiatan->penyelenggara }}</dd>
                <dt class="col-sm-3">Waktu</dt><dd class="col-sm-9">{{ $kegiatan->datetime_started->format('d M Y H:i') }} - {{ $kegiatan->datetime_finished->format('d M Y H:i') }}</dd>
                <dt class="col-sm-3">Lokasi</dt><dd class="col-sm-9">{{ $kegiatan->location }}</dd>
                <dt class="col-sm-3">Peserta</dt><dd class="col-sm-9">{{ $kegiatan->pendaftaran->count() }} / {{ $kegiatan->max_participant ?? 'Tanpa batas' }}</dd>
                <dt class="col-sm-3">Status / Verifikasi</dt><dd class="col-sm-9">{{ $kegiatan->status }} / {{ $kegiatan->verification }}</dd>
                <dt class="col-sm-3">Deskripsi</dt><dd class="col-sm-9">{{ $kegiatan->description }}</dd>
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
@stop