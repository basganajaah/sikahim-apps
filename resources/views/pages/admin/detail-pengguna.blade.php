@extends('adminlte::page')

@section('title', 'Sikahim | Detail & Edit Pengguna')

@section('content_header')
    <x-header title="Detail & Kelola Pengguna" breadcrumb="Detail Pengguna" />
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Data Pengguna #{{ $user->id }}</h3>
                    <form method="POST" action="{{ route('users.toggle-active', $user) }}" class="ml-auto">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-xs btn-{{ $user->is_active !== false ? 'warning' : 'success' }}">
                            {{ $user->is_active !== false ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Username / Nama</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="user" @selected(old('role', $user->role) === 'user')>User (Jamaah)</option>
                                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_active">Status Akun</label>
                            <select name="is_active" id="is_active" class="form-control">
                                <option value="1" @selected($user->is_active !== false)>Aktif</option>
                                <option value="0" @selected($user->is_active === false)>Nonaktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                        <a href="{{ route('kelola-pengguna') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Reset Password Pengguna</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.reset-password', $user) }}">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" name="password" id="password" class="form-control" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm">Reset Password</button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">Riwayat Pendaftaran ({{ $user->pendaftaran->count() }})</h3>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->pendaftaran as $reg)
                                <tr>
                                    <td>{{ $reg->kegiatan?->activity_name ?? '-' }}</td>
                                    <td>{{ $reg->registration_date?->format('d M Y') }}</td>
                                    <td><span class="badge badge-info">{{ $reg->registration_status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Belum ada pendaftaran.</td></tr>
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
