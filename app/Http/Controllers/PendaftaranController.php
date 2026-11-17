<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kegiatan;
use App\Models\Notification;
use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $pendaftaran = Pendaftaran::with('kegiatan')
            ->where('id', $request->user()->id)
            ->latest('registration_date')
            ->get();

        return view('pages.user.pendaftaran', compact('pendaftaran'));
    }

    public function store(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'registrant_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $kegiatan, $validated) {
            $lockedActivity = Kegiatan::query()->lockForUpdate()->findOrFail($kegiatan->activity_id);

            abort_if($lockedActivity->verification !== 'Terverifikasi', 422, 'Kegiatan belum diverifikasi.');
            abort_if(in_array($lockedActivity->status, ['Ditunda', 'Selesai'], true), 422, 'Pendaftaran kegiatan tidak dibuka.');
            abort_if($lockedActivity->datetime_started->isPast(), 422, 'Pendaftaran kegiatan sudah ditutup.');
            abort_if(
                Pendaftaran::where('activity_id', $lockedActivity->activity_id)
                    ->where('id', $request->user()->id)
                    ->whereNotIn('registration_status', ['Dibatalkan'])
                    ->exists(),
                422,
                'Anda sudah terdaftar pada kegiatan ini.'
            );

            $activeCount = $lockedActivity->pendaftaran()
                ->whereNotIn('registration_status', ['Dibatalkan', 'Ditolak'])
                ->count();

            abort_if(
                $lockedActivity->max_participant !== null && $activeCount >= $lockedActivity->max_participant,
                422,
                'Kuota kegiatan sudah penuh.'
            );

            Pendaftaran::updateOrCreate(
                [
                    'id' => $request->user()->id,
                    'activity_id' => $lockedActivity->activity_id,
                ],
                [
                    'registrant_name' => $validated['registrant_name'],
                    'age' => $validated['age'],
                    'gender' => $validated['gender'],
                    'registration_date' => now()->toDateString(),
                    'registration_status' => 'Terdaftar',
                    'notes' => null,
                ]
            );

            Notification::notifyUsers(
                [$request->user()->id],
                "Pendaftaran Berhasil: {$lockedActivity->activity_name}",
                "Anda berhasil terdaftar pada kegiatan {$lockedActivity->activity_name} yang dijadwalkan pada {$lockedActivity->datetime_started->format('d M Y H:i')} di {$lockedActivity->location}."
            );
        });

        return redirect()->route('user.pendaftaran')->with('success', 'Pendaftaran kegiatan berhasil.');
    }

    public function cancel(Request $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        abort_if((int) $pendaftaran->id !== (int) $request->user()->id, 403, 'Anda tidak berhak membatalkan pendaftaran ini.');

        $pendaftaran->load('kegiatan');
        abort_if($pendaftaran->kegiatan?->status === 'Selesai', 422, 'Kegiatan yang sudah selesai tidak dapat dibatalkan.');
        abort_if($pendaftaran->registration_status === 'Dibatalkan', 422, 'Pendaftaran sudah dibatalkan sebelumnya.');

        $pendaftaran->update([
            'registration_status' => 'Dibatalkan',
            'notes' => $request->input('notes', 'Dibatalkan oleh peserta.'),
        ]);

        if ($pendaftaran->kegiatan) {
            Notification::notifyUsers(
                [$request->user()->id],
                "Pendaftaran Dibatalkan: {$pendaftaran->kegiatan->activity_name}",
                "Pendaftaran Anda pada kegiatan {$pendaftaran->kegiatan->activity_name} telah dibatalkan."
            );
        }

        return back()->with('success', 'Pendaftaran berhasil dibatalkan.');
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $validated = $request->validate([
            'registration_status' => ['required', 'in:Terdaftar,Diterima,Ditolak,Dibatalkan'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $pendaftaran->load('kegiatan');
        $pendaftaran->update([
            'registration_status' => $validated['registration_status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $activityName = $pendaftaran->kegiatan?->activity_name ?? 'Kegiatan';
        Notification::notifyUsers(
            [$pendaftaran->id],
            "Status Pendaftaran: {$activityName}",
            "Status pendaftaran Anda untuk {$activityName} telah diperbarui menjadi: {$validated['registration_status']}."
        );

        AuditLog::record(
            'update_pendaftaran_status',
            "Mengubah status pendaftaran #{$pendaftaran->registration_id} ({$pendaftaran->registrant_name}) menjadi {$validated['registration_status']}",
            'Pendaftaran',
            $pendaftaran->registration_id
        );

        return back()->with('success', 'Status pendaftaran peserta berhasil diperbarui.');
    }
}
