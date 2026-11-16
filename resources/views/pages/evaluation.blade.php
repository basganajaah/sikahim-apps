@extends('adminlte::page')

@section('title', 'Evaluasi Kegiatan')

@section('content_header')
    <x-header title="Evaluasi Kegiatan" breadcrumb="Evaluasi" />
@stop

@section('content')
    <div class="card">
        <div class="card-header">{{ $kegiatan->activity_name }}</div>
        <div class="card-body">
            @forelse($kegiatan->evaluasi as $evaluasi)
                <div class="border-bottom mb-3 pb-3">
                    <strong>{{ $evaluasi->user?->name ?? 'Pengguna' }}</strong>
                    <span class="badge badge-primary">{{ $evaluasi->rating }}/5</span>
                    <p class="mb-0">{{ $evaluasi->review }}</p>
                </div>
            @empty
                <p class="mb-0">Belum ada evaluasi.</p>
            @endforelse
        </div>
    </div>
@stop