@extends('adminlte::page')

@section('title', 'Sikahim | Audit Log Aktivitas Admin')

@section('content_header')
    <x-header title="Audit Log Aktivitas Admin" breadcrumb="Audit Log" />
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Waktu</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                        <th>Target</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td><code>{{ $log->action }}</code></td>
                            <td>{{ $log->target_type ? "{$log->target_type} #{$log->target_id}" : '-' }}</td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada catatan aktivitas admin.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
