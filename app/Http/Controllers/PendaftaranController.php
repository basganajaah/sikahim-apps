<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
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
            abort_if($lockedActivity->datetime_started->isPast(), 422, 'Pendaftaran kegiatan sudah ditutup.');
            abort_if(
                Pendaftaran::where('activity_id', $lockedActivity->activity_id)
                    ->where('id', $request->user()->id)
                    ->exists(),
                422,
                'Anda sudah terdaftar pada kegiatan ini.'
            );
            abort_if(
                $lockedActivity->max_participant !== null
                    && $lockedActivity->pendaftaran()->count() >= $lockedActivity->max_participant,
                422,
                'Kuota kegiatan sudah penuh.'
            );

            Pendaftaran::create([
                'id' => $request->user()->id,
                'activity_id' => $lockedActivity->activity_id,
                'registrant_name' => $validated['registrant_name'],
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'registration_date' => now()->toDateString(),
                'registration_status' => 'Terdaftar',
            ]);
        });

        return redirect()->route('user.pendaftaran')->with('success', 'Pendaftaran kegiatan berhasil.');
    }
}
