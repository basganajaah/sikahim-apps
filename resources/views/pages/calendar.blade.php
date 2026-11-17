@extends('adminlte::page')

@section('title', 'Sikahim | Kalender')

@section('content_header')
    <x-header title="Kalender" breadcrumb="Kalender" />
@stop

@section('content')
    @php $isAdmin = auth()->user()?->role === 'admin'; @endphp
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
        <div>
            <h2 class="h4 mb-1">Agenda Masjid</h2>
            <p class="text-muted mb-0">
                {{ $isAdmin ? 'Klik tanggal kosong untuk membuat kegiatan pada hari tersebut.' : 'Jadwal kegiatan masjid yang terverifikasi.' }}
            </p>
        </div>
        @if ($isAdmin)
            <a class="btn btn-primary mt-3 mt-md-0" href="{{ route('create-kegiatan') }}">
                <i class="fas fa-plus mr-1"></i> Buat Kegiatan
            </a>
        @endif
    </div>
    <div class="row">
        @if ($isAdmin)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h6">Petunjuk</h3>
                        <div class="small text-muted mb-3">
                            <i class="fas fa-mouse-pointer text-success mr-2"></i>
                            Pilih tanggal pada kalender untuk mengatur waktu mulai kegiatan.
                        </div>
                        <div class="small text-muted">
                            <i class="fas fa-info-circle text-success mr-2"></i>
                            Kegiatan baru akan berstatus menunggu verifikasi.
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="{{ $isAdmin ? 'col-md-9 mt-3 mt-md-0' : 'col-12' }}">
            <div class="card">
                <div class="card-body p-0">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')

@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var isAdmin = @json($isAdmin);
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            initialView: 'dayGridMonth',
            events: @json($events),
            selectable: isAdmin,
            dateClick: function(info) {
                if (!isAdmin) return;
                var url = @json($isAdmin ? route('create-kegiatan') : null);
                if (url) window.location.href = url + '?start=' + encodeURIComponent(info.dateStr);
            },
        });
        calendar.render();
    });
</script>
@endsection
