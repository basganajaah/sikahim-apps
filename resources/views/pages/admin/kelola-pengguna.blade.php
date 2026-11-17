@extends('adminlte::page')

@section('title', 'Sikahim | Kelola Pengguna')

@section('content_header')
    <x-header title="Kelola Pengguna" breadcrumb="Kelola Pengguna" />
@stop

@section('content')
    @section('plugins.Datatables', true)
    @section('plugins.DatatablesPlugins', true)

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('kelola-pengguna') }}" class="form-inline">
                <label for="role" class="mr-2">Filter Role:</label>
                <select name="role" id="role" class="form-control form-control-sm mr-2">
                    <option value="">Semua Role</option>
                    <option value="user" @selected(request('role') === 'user')>User (Jamaah)</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm mr-2">Terapkan</button>
                <a href="{{ route('kelola-pengguna') }}" class="btn btn-secondary btn-sm">Reset</a>
            </form>
        </div>
    </div>

    @php
        $heads = [
            'ID',
            'Username',
            ['label' => 'Email', 'width' => 30],
            'Role',
            'Status',
            ['label' => 'Actions', 'no-export' => true, 'width' => 12],
        ];

        $ajaxUrl = route('users.index', array_filter(['role' => request('role')]));

        $config = [
            'order' => [[1, 'asc']],
            'columns' => [
                ['data' => 'id'],
                ['data' => 'name'],
                ['data' => 'email'],
                ['data' => 'role'],
                ['data' => 'status'],
                ['data' => 'actions', 'orderable' => false],
            ],
            'ajax' => $ajaxUrl,
        ];
    @endphp

    <x-adminlte-datatable id="users-table" :heads="$heads" :config="$config" head-theme="dark" footer-theme="dark" with-footer with-buttons hoverable />

@endsection

@section('footer')
    @include('components.footer')
@endsection

@section('js')
    <script>
        $(document).on('click', '.delete-btn', function() {
            var userId = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Pengguna ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('/admin/users') }}/' + userId,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            Swal.fire('Dihapus!', response.message, 'success');
                            location.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus pengguna.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@stop
