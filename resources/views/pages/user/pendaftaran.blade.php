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
                <thead><tr><th>Kegiatan</th><th>Tanggal Daftar</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($pendaftaran as $item)
                        <tr>
                            <td>{{ $item->kegiatan?->activity_name ?? '-' }}</td>
                            <td>{{ $item->registration_date?->format('d M Y') }}</td>
                            <td><span class="badge badge-info">{{ $item->registration_status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Belum ada pendaftaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop