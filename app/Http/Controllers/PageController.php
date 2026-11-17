<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Notification;
use App\Models\Pendaftaran;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function userHome()
    {
        $kegiatan = Kegiatan::with(['kategori', 'tags'])
            ->withCount('pendaftaran')
            ->where('verification', 'Terverifikasi')
            ->whereNotIn('status', ['Ditunda', 'Selesai'])
            ->where('datetime_finished', '>=', now())
            ->orderBy('datetime_started')
            ->get();

        return view('pages.user.users-home', compact('kegiatan'));
    }

    public function dashboardKegiatan()
    {
        return view('pages.dashboard-kegiatan', [
            'newCount' => Kegiatan::where('verification', 'Menunggu verifikasi')->count(),
            'ongoingCount' => Kegiatan::where('status', 'Berlangsung')->count(),
            'completedCount' => Kegiatan::where('status', 'Selesai')->count(),
        ]);
    }

    public function createKegiatan(Request $request)
    {
        $categories = Kategori::all();
        $tags = Tag::all();
        $startAt = $request->query('start')
            ? $request->query('start').' 09:00'
            : null;
        $endAt = $request->query('end')
            ? $request->query('end').' 11:00'
            : null;

        return view('pages.create-kegiatan', compact('categories', 'tags', 'startAt', 'endAt'));
    }

    public function kelolaPendaftaran()
    {
        $pendaftaran = Pendaftaran::with(['kegiatan', 'user'])
            ->latest('registration_date')
            ->get();

        return view('pages.admin.kelola-pendaftaran', compact('pendaftaran'));
    }

    public function kelolaPengguna()
    {
        return view('pages.admin.kelola-pengguna');
    }

    public function calendar()
    {
        $events = Kegiatan::where('verification', 'Terverifikasi')->get()
            ->map(fn (Kegiatan $kegiatan) => [
                'id' => $kegiatan->activity_id,
                'title' => $kegiatan->activity_name,
                'start' => $kegiatan->datetime_started->toIso8601String(),
                'end' => $kegiatan->datetime_finished->toIso8601String(),
                'url' => route('detail-kegiatan', $kegiatan),
            ]);

        return view('pages.calendar', compact('events'));
    }

    public function detailKegiatan(Request $request, Kegiatan $kegiatan)
    {
        abort_if($request->user()?->role !== 'admin' && $kegiatan->verification !== 'Terverifikasi', 404);

        $kegiatan->load(['kategori', 'tags', 'pendaftaran.user', 'evaluasi.user']);

        return view('pages.detail-kegiatan', compact('kegiatan'));
    }

    public function evaluation(Request $request, Kegiatan $kegiatan)
    {
        abort_if($request->user()?->role !== 'admin' && $kegiatan->verification !== 'Terverifikasi', 404);

        return view('pages.evaluation', [
            'kegiatan' => $kegiatan->load('evaluasi.user'),
        ]);
    }

    public function storeEvaluation(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        abort_if($kegiatan->verification !== 'Terverifikasi', 422, 'Kegiatan belum diverifikasi.');

        $validated = $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'max:1000'],
        ]);

        Evaluasi::updateOrCreate(
            [
                'id' => $request->user()->id,
                'activity_id' => $kegiatan->activity_id,
            ],
            [
                'rating' => $validated['rating'],
                'review' => $validated['review'],
            ]
        );

        return redirect()->route('evaluation', $kegiatan)->with('success', 'Evaluasi kegiatan berhasil disimpan.');
    }

    public function notifications(Request $request)
    {
        $userId = $request->user()->id;
        Notification::syncForUser($userId);

        $items = Notification::with(['users' => fn ($q) => $q->where('users.id', $userId)])
            ->whereHas('users', fn ($q) => $q->where('users.id', $userId))
            ->latest('sent_at')
            ->take(8)
            ->get();

        $unreadCount = $items->filter(fn (Notification $n) => ! (bool) ($n->users->first()?->pivot?->is_read ?? $n->is_read))->count();

        $dropdown = $items->isEmpty()
            ? '<span class="dropdown-item text-muted text-center small py-3">Belum ada notifikasi</span>'
            : $items->map(function (Notification $n) {
                $isRead = (bool) ($n->users->first()?->pivot?->is_read ?? $n->is_read);
                $weight = $isRead ? 'text-muted' : 'font-weight-bold';

                return '<div class="dropdown-item text-wrap border-bottom py-2">'
                    .'<div class="small '.$weight.'">'.e($n->title).'</div>'
                    .'<div class="text-xs text-muted">'.e($n->message).'</div>'
                    .'<div class="text-xs text-secondary mt-1">'.$n->sent_at?->diffForHumans().'</div>'
                    .'</div>';
            })->implode('');

        return response()->json([
            'label' => $unreadCount,
            'label_color' => $unreadCount > 0 ? 'warning' : 'secondary',
            'icon_color' => $unreadCount > 0 ? 'warning' : 'dark',
            'dropdown' => $dropdown,
        ]);
    }

    public function markNotificationsRead(Request $request)
    {
        DB::table('mendapat_notifikasi')
            ->where('id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }
}
