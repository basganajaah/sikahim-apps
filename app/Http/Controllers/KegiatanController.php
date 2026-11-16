<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::with(['kategori', 'tags'])
            ->withCount('pendaftaran')
            ->latest('datetime_started')
            ->get();

        return view('pages.list-kegiatan', compact('kegiatan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'materi' => ['nullable', 'array'],
            'materi.*' => ['file', 'mimes:pdf,doc,docx', 'max:10240'],
            'activity_name' => ['required', 'string', 'max:255'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:kategori,category_id'],
            'sel2Category' => ['nullable', 'array'],
            'sel2Category.*' => ['integer', 'exists:tag,tag_id'],
            'datetime_started' => ['required', 'date', 'before:datetime_finished'],
            'datetime_finished' => ['required', 'date', 'after:datetime_started'],
            'max_participant' => ['nullable', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $kegiatan = DB::transaction(function () use ($request, $validated) {
            $materiPaths = collect($request->file('materi', []))
                ->map(fn ($file) => $file->store('materi', 'public'))
                ->values()
                ->all();

            $kegiatan = Kegiatan::create([
                'id' => $request->user()->id,
                'thumbnail' => $request->file('thumbnail')?->store('thumbnails', 'public'),
                'materi' => $materiPaths ?: null,
                'activity_name' => $validated['activity_name'],
                'penyelenggara' => $validated['penyelenggara'],
                'category_id' => $validated['category_id'],
                'datetime_started' => $validated['datetime_started'],
                'datetime_finished' => $validated['datetime_finished'],
                'max_participant' => $validated['max_participant'] ?? null,
                'location' => $validated['location'],
                'description' => $validated['description'],
            ]);

            $kegiatan->tags()->sync($validated['sel2Category'] ?? []);

            Notification::notifyUsers(
                User::pluck('id'),
                "Kegiatan Baru: {$kegiatan->activity_name}",
                "Kegiatan baru {$kegiatan->activity_name} pada {$kegiatan->datetime_started->format('d M Y H:i')} di {$kegiatan->location}."
            );

            return $kegiatan;
        });

        return redirect()->route('list-kegiatan')
            ->with('success', "Kegiatan {$kegiatan->activity_name} berhasil dibuat dan menunggu verifikasi.");
    }

    public function updateStatus(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'verification' => ['required', 'in:Menunggu verifikasi,Terverifikasi,Ditolak'],
            'status' => ['required', 'in:Belum Mulai,Berlangsung,Selesai,Ditunda'],
        ]);

        $kegiatan->update($validated);

        if ($validated['verification'] === 'Terverifikasi') {
            Notification::notifyUsers(
                User::pluck('id'),
                "Kegiatan Baru: {$kegiatan->activity_name}",
                "Kegiatan {$kegiatan->activity_name} pada {$kegiatan->datetime_started->format('d M Y H:i')} di {$kegiatan->location} telah dibuka."
            );
        }

        if ($validated['status'] === 'Berlangsung') {
            Notification::syncForUser();
        }

        return back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }

    public function destroy(Request $request, Kegiatan $kegiatan)
    {
        $kegiatan->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Kegiatan berhasil dihapus.']);
        }

        return redirect()->route('list-kegiatan')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
