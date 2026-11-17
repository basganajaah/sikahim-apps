@extends('adminlte::page')

@section('title', 'Sikahim | Kelola Kategori & Tag')

@section('content_header')
    <x-header title="Kelola Kategori & Tag" breadcrumb="Kategori & Tag" />
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

    <div class="row">
        {{-- KATEGORI --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Kelola Kategori Kegiatan</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('kategori.store') }}" class="form-inline mb-3">
                        @csrf
                        <input type="text" name="category_name" class="form-control form-control-sm mr-2" placeholder="Nama kategori baru" required>
                        <button type="submit" class="btn btn-primary btn-sm">Tambah Kategori</button>
                    </form>

                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $kat)
                                <tr>
                                    <td>{{ $kat->category_id }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('kategori.update', $kat) }}" class="form-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="category_name" value="{{ $kat->category_name }}" class="form-control form-control-sm mr-2" required>
                                            <button type="submit" class="btn btn-xs btn-warning">Simpan</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('kategori.destroy', $kat) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada kategori.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAG --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Kelola Tag Kegiatan</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tag.store') }}" class="form-inline mb-3">
                        @csrf
                        <input type="text" name="tag_name" class="form-control form-control-sm mr-2" placeholder="Nama tag baru" required>
                        <button type="submit" class="btn btn-primary btn-sm">Tambah Tag</button>
                    </form>

                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Tag</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tags as $tag)
                                <tr>
                                    <td>{{ $tag->tag_id }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('tag.update', $tag) }}" class="form-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="tag_name" value="{{ $tag->tag_name }}" class="form-control form-control-sm mr-2" required>
                                            <button type="submit" class="btn btn-xs btn-warning">Simpan</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('tag.destroy', $tag) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada tag.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    @include('components.footer')
@endsection
