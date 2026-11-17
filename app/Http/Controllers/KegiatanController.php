<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Notification;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $kegiatan = Kegiatan::with(['kategori', 'tags'])
            ->withCount('pendaftaran')
            ->when($request->user()?->role !== 'admin', fn ($q) => $q->where('verification', 'Terverifikasi'))
            ->when($request->filled('search'), fn ($q) => $q->where('activity_name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('tag_id'), fn ($q) => $q->whereHas('tags', fn ($tq) => $tq->where('tag.tag_id', $request->integer('tag_id'))))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('datetime_started', $request->string('date')))
            ->when($request->filled('location'), fn ($q) => $q->where('location', 'like', '%'.$request->string('location').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
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

            AuditLog::record('create_kegiatan', "Membuat kegiatan baru: {$kegiatan->activity_name}", 'Kegiatan', $kegiatan->activity_id);

            return $kegiatan;
        });

        return redirect()->route('list-kegiatan')
            ->with('success', "Kegiatan {$kegiatan->activity_name} berhasil dibuat dan menunggu verifikasi.");
    }

    public function edit(Kegiatan $kegiatan)
    {
        $categories = Kategori::all();
        $tags = Tag::all();
        $kegiatan->load('tags');

        return view('pages.edit-kegiatan', compact('kegiatan', 'categories', 'tags'));
    }

    public function update(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'materi' => ['nullable', 'array'],
            'materi.*' => ['file', 'mimes:pdf,doc,docx', 'max:10240'],
            'remove_materi' => ['nullable', 'array'],
            'remove_materi.*' => ['string'],
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

        DB::transaction(function () use ($request, $kegiatan, $validated) {
            $oldStart = $kegiatan->datetime_started?->format('Y-m-d H:i');
            $oldEnd = $kegiatan->datetime_finished?->format('Y-m-d H:i');
            $oldLocation = $kegiatan->location;

            $thumbnailPath = $kegiatan->thumbnail;
            if ($request->hasFile('thumbnail')) {
                if ($thumbnailPath) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $existingMateri = collect($kegiatan->materi ?? []);
            $toRemove = collect($validated['remove_materi'] ?? []);
            foreach ($toRemove as $remPath) {
                if ($existingMateri->contains($remPath)) {
                    Storage::disk('public')->delete($remPath);
                }
            }
            $keptMateri = $existingMateri->reject(fn ($p) => $toRemove->contains($p))->values()->all();

            if ($request->hasFile('materi')) {
                $newMateri = collect($request->file('materi', []))
                    ->map(fn ($file) => $file->store('materi', 'public'))
                    ->values()
                    ->all();
                $materiPaths = array_values(array_merge($keptMateri, $newMateri));
            } else {
                $materiPaths = $keptMateri;
            }

            $kegiatan->update([
                'thumbnail' => $thumbnailPath,
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

            $newStart = $kegiatan->fresh()->datetime_started?->format('Y-m-d H:i');
            $newEnd = $kegiatan->fresh()->datetime_finished?->format('Y-m-d H:i');
            if ($oldStart !== $newStart || $oldEnd !== $newEnd || $oldLocation !== $kegiatan->location) {
                Notification::notifyUsers(
                    User::pluck('id'),
                    "Perubahan Jadwal: {$kegiatan->activity_name}",
                    "Jadwal/lokasi kegiatan {$kegiatan->activity_name} diperbarui menjadi {$kegiatan->datetime_started->format('d M Y H:i')} di {$kegiatan->location}."
                );
            }

            AuditLog::record('update_kegiatan', "Memperbarui kegiatan: {$kegiatan->activity_name}", 'Kegiatan', $kegiatan->activity_id);
        });

        return redirect()->route('detail-kegiatan', $kegiatan)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'verification' => ['required', 'in:Menunggu verifikasi,Terverifikasi,Ditolak'],
            'status' => ['required', 'in:Belum Mulai,Berlangsung,Selesai,Ditunda'],
            'rejection_reason' => ['nullable', 'string', 'max:1000', 'required_if:verification,Ditolak'],
        ]);

        $kegiatan->update([
            'verification' => $validated['verification'],
            'status' => $validated['status'],
            'rejection_reason' => $validated['verification'] === 'Ditolak' ? ($validated['rejection_reason'] ?? null) : null,
        ]);

        if ($validated['verification'] === 'Terverifikasi') {
            Notification::notifyUsers(
                User::pluck('id'),
                "Kegiatan Baru: {$kegiatan->activity_name}",
                "Kegiatan {$kegiatan->activity_name} pada {$kegiatan->datetime_started->format('d M Y H:i')} di {$kegiatan->location} telah dibuka."
            );
        } elseif ($validated['verification'] === 'Ditolak') {
            $reason = $kegiatan->rejection_reason ?: 'Tidak memenuhi kriteria.';
            Notification::notifyUsers(
                User::pluck('id'),
                "Kegiatan Ditolak: {$kegiatan->activity_name}",
                "Kegiatan {$kegiatan->activity_name} ditolak. Alasan: {$reason}"
            );
        }

        if ($validated['status'] === 'Berlangsung') {
            Notification::syncForUser();
        }

        AuditLog::record(
            'verify_kegiatan',
            "Mengubah status/verifikasi kegiatan {$kegiatan->activity_name} menjadi {$kegiatan->status} / {$kegiatan->verification}",
            'Kegiatan',
            $kegiatan->activity_id
        );

        return back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }

    public function downloadMateri(Request $request, Kegiatan $kegiatan, int $index)
    {
        abort_if($request->user()?->role !== 'admin' && $kegiatan->verification !== 'Terverifikasi', 404);

        $materiList = $kegiatan->materi ?? [];
        abort_if(! isset($materiList[$index]), 404, 'File materi tidak ditemukan.');

        $filePath = $materiList[$index];
        abort_if(! Storage::disk('public')->exists($filePath), 404, 'File materi tidak tersedia di storage.');

        return Storage::disk('public')->download($filePath);
    }

    public function destroy(Request $request, Kegiatan $kegiatan)
    {
        $activityName = $kegiatan->activity_name;
        $activityId = $kegiatan->activity_id;

        if ($kegiatan->thumbnail) {
            Storage::disk('public')->delete($kegiatan->thumbnail);
        }

        foreach ($kegiatan->materi ?? [] as $filePath) {
            Storage::disk('public')->delete($filePath);
        }

        $kegiatan->delete();

        AuditLog::record('delete_kegiatan', "Menghapus kegiatan: {$activityName}", 'Kegiatan', $activityId);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Kegiatan berhasil dihapus.']);
        }

        return redirect()->route('list-kegiatan')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
