<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HomeController extends Controller
{
    public function index()
    {
        $allKegiatan = Kegiatan::with(['kategori', 'pendaftaran', 'evaluasi'])->get();

        $monthlyCounts = $allKegiatan
            ->groupBy(fn (Kegiatan $k) => $k->datetime_started->format('Y-m'))
            ->sortKeys()
            ->map->count();

        $pendaftaranLabels = $allKegiatan->pluck('activity_name')->values();
        $pendaftaranData = $allKegiatan->map(fn (Kegiatan $k) => $k->pendaftaran->count())->values();

        $rekapKategori = Kategori::all()->map(function (Kategori $kat) use ($allKegiatan) {
            $items = $allKegiatan->where('category_id', $kat->category_id);

            return [
                'category_name' => $kat->category_name,
                'total_kegiatan' => $items->count(),
                'total_pendaftar' => $items->sum(fn (Kegiatan $k) => $k->pendaftaran->count()),
            ];
        });

        $rekapEvaluasi = $allKegiatan->map(function (Kegiatan $k) {
            return [
                'activity_id' => $k->activity_id,
                'activity_name' => $k->activity_name,
                'status' => $k->status,
                'total_pendaftar' => $k->pendaftaran->count(),
                'total_evaluasi' => $k->evaluasi->count(),
                'avg_rating' => $k->evaluasi->count() > 0 ? round($k->evaluasi->avg('rating'), 2) : null,
            ];
        });

        return view('pages.admin.home', [
            'totalKegiatan' => $allKegiatan->count(),
            'pendingKegiatan' => $allKegiatan->where('verification', 'Menunggu verifikasi')->count(),
            'totalPendaftaran' => Pendaftaran::count(),
            'totalPengguna' => User::where('role', 'user')->count(),
            'monthlyLabels' => $monthlyCounts->keys()->values(),
            'monthlyData' => $monthlyCounts->values(),
            'pendaftaranLabels' => $pendaftaranLabels,
            'pendaftaranData' => $pendaftaranData,
            'rekapKategori' => $rekapKategori,
            'rekapEvaluasi' => $rekapEvaluasi,
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $kegiatanList = Kegiatan::with(['kategori', 'pendaftaran', 'evaluasi'])
            ->orderBy('datetime_started')
            ->get();

        return response()->streamDownload(function () use ($kegiatanList) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'ID Kegiatan',
                'Nama Kegiatan',
                'Kategori',
                'Penyelenggara',
                'Waktu Mulai',
                'Waktu Selesai',
                'Lokasi',
                'Status',
                'Verifikasi',
                'Jumlah Pendaftar',
                'Kuota',
                'Jumlah Evaluasi',
                'Rata-rata Rating',
            ]);

            foreach ($kegiatanList as $k) {
                $avg = $k->evaluasi->count() > 0 ? round($k->evaluasi->avg('rating'), 2) : '-';
                fputcsv($out, [
                    $k->activity_id,
                    $k->activity_name,
                    $k->kategori?->category_name ?? '-',
                    $k->penyelenggara,
                    $k->datetime_started->format('Y-m-d H:i'),
                    $k->datetime_finished->format('Y-m-d H:i'),
                    $k->location,
                    $k->status,
                    $k->verification,
                    $k->pendaftaran->count(),
                    $k->max_participant ?? 'Tanpa batas',
                    $k->evaluasi->count(),
                    $avg,
                ]);
            }

            fclose($out);
        }, 'laporan-kegiatan-sikahim.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function auditLogs()
    {
        $logs = AuditLog::with('user')->latest()->take(200)->get();

        return view('pages.admin.audit-logs', compact('logs'));
    }
}
