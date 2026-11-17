@extends('adminlte::page')

@section('title', 'Sikahim | Kelola Pendaftaran')

@section('content_header')
    <x-header title="Kelola Pendaftaran" breadcrumb="Kelola Pendaftaran" />
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Peserta</th>
                        <th>Kegiatan</th>
                        <th>Usia</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftaran ?? [] as $item)
                        <tr>
                            <td>{{ $item->registration_id }}</td>
                            <td>{{ $item->registrant_name }}</td>
                            <td>{{ $item->kegiatan?->activity_name ?? '-' }}</td>
                            <td>{{ $item->age }}</td>
                            <td>{{ $item->gender ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ $item->registration_date?->format('d M Y') }}</td>
                            <td><span class="badge badge-info">{{ $item->registration_status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data pendaftaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
