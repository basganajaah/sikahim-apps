@extends('adminlte::page')

@section('title', 'Sikahim | Kelola Pendaftaran')

@section('content_header')
    <x-header title="Kelola Pendaftaran" breadcrumb="Kelola Pendaftaran" />
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('kelola-pendaftaran') }}" class="form-inline">
                <label class="mr-2" for="activity_id">Filter Kegiatan:</label>
                <select name="activity_id" id="activity_id" class="form-control form-control-sm mr-3">
                    <option value="">Semua Kegiatan</option>
                    @foreach ($kegiatanList ?? [] as $keg)
                        <option value="{{ $keg->activity_id }}" @selected(request('activity_id') == $keg->activity_id)>
                            {{ $keg->activity_name }}
                        </option>
                    @endforeach
                </select>

                <label class="mr-2" for="registration_status">Status:</label>
                <select name="registration_status" id="registration_status" class="form-control form-control-sm mr-3">
                    <option value="">Semua Status</option>
                    @foreach (['Terdaftar', 'Diterima', 'Ditolak', 'Dibatalkan'] as $st)
                        <option value="{{ $st }}" @selected(request('registration_status') === $st)>{{ $st }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary btn-sm mr-2">Terapkan Filter</button>
                <a href="{{ route('kelola-pendaftaran') }}" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table id="table-pendaftaran" class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Peserta</th>
                        <th>Kegiatan</th>
                        <th>Usia</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Ubah Status</th>
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
                            <td>
                                <span class="badge badge-{{ $item->registration_status === 'Diterima' ? 'success' : ($item->registration_status === 'Ditolak' || $item->registration_status === 'Dibatalkan' ? 'danger' : 'info') }}">
                                    {{ $item->registration_status }}
                                </span>
                            </td>
                            <td>{{ $item->notes ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.pendaftaran.update-status', $item) }}" class="form-inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="registration_status" class="form-control form-control-sm mr-1">
                                        @foreach (['Terdaftar', 'Diterima', 'Ditolak', 'Dibatalkan'] as $statusOpt)
                                            <option value="{{ $statusOpt }}" @selected($item->registration_status === $statusOpt)>{{ $statusOpt }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="notes" value="{{ $item->notes }}" class="form-control form-control-sm mr-1" placeholder="Catatan" style="width: 110px;">
                                    <button type="submit" class="btn btn-xs btn-primary">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data pendaftaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function () {
            if ($('#table-pendaftaran tbody tr td').length > 1) {
                $('#table-pendaftaran').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });
            }
        });
    </script>
@stop

@section('footer')
    @include('components.footer')
@endsection
