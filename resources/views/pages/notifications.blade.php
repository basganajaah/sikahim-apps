@extends('adminlte::page')

@section('title', 'Sikahim | Daftar Notifikasi')

@section('content_header')
    <x-header title="Notifikasi Saya" breadcrumb="Notifikasi" />
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Semua Notifikasi ({{ $notifications->count() }})</h3>
            <form method="POST" action="{{ route('notifications.read') }}" class="ml-auto">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-check-double mr-1"></i> Tandai Semua Sudah Dibaca
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse ($notifications as $item)
                    @php
                        $isRead = (bool) ($item->users->first()?->pivot?->is_read ?? $item->is_read);
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center {{ $isRead ? 'bg-light text-muted' : '' }}">
                        <div>
                            <div class="{{ $isRead ? '' : 'font-weight-bold' }}">
                                {{ $item->title }}
                                <span class="badge badge-{{ $isRead ? 'secondary' : 'warning' }} ml-1">
                                    {{ $isRead ? 'Sudah dibaca' : 'Belum dibaca' }}
                                </span>
                            </div>
                            <div class="small">{{ $item->message }}</div>
                            <small class="text-secondary">{{ $item->sent_at?->format('d M Y H:i') }} ({{ $item->sent_at?->diffForHumans() }})</small>
                        </div>
                        <form method="POST" action="{{ route('notifications.toggle', $item) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-xs btn-outline-secondary">
                                {{ $isRead ? 'Tandai Belum Dibaca' : 'Tandai Dibaca' }}
                            </button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">Belum ada notifikasi.</li>
                @endforelse
            </ul>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
