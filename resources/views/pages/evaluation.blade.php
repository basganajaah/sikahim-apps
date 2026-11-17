@extends('adminlte::page')

@section('title', 'Evaluasi Kegiatan')

@section('content_header')
    <x-header title="Evaluasi Kegiatan" breadcrumb="Evaluasi" />
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

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Beri Evaluasi: {{ $kegiatan->activity_name }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('evaluation.store', $kegiatan) }}">
                @csrf
                <div class="form-group">
                    <label for="rating">Rating (1 - 5)</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="">Pilih rating</option>
                        @foreach ([5, 4, 3, 2, 1] as $score)
                            <option value="{{ $score }}" @selected(old('rating') == $score)>{{ $score }} Bintang</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="review">Ulasan / Masukan</label>
                    <textarea name="review" id="review" rows="3" class="form-control" placeholder="Tulis ulasan Anda mengenai kegiatan ini..." required>{{ old('review') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Kirim Evaluasi</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Evaluasi: {{ $kegiatan->activity_name }}</div>
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
