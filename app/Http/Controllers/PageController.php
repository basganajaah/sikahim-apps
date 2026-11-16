<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Kegiatan;
use App\Models\Notification;
use App\Models\Tag;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function userHome()
    {
        $kegiatan = Kegiatan::with(['kategori', 'tags'])
            ->withCount('pendaftaran')
            ->where('verification', 'Terverifikasi')
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

    public function detailKegiatan(Kegiatan $kegiatan)
    {
        $kegiatan->load(['kategori', 'tags', 'pendaftaran.user', 'evaluasi.user']);

        return view('pages.detail-kegiatan', compact('kegiatan'));
    }

    public function evaluation(Kegiatan $kegiatan)
    {
        return view('pages.evaluation', [
            'kegiatan' => $kegiatan->load('evaluasi.user'),
        ]);
    }

    public function notifications(Request $request)
    {
        $userId = $request->user()->id;
        Notification::syncForUser($userId);

        $items = Notification::whereHas('users', fn ($q) => $q->where('users.id', $userId))
            ->latest('sent_at')
            ->take(8)
            ->get();

        $unreadCount = $items->where('is_read', false)->count();

        $dropdown = $items->isEmpty()
            ? '<span class="dropdown-item text-muted text-center small py-3">Belum ada notifikasi</span>'
            : $items->map(function (Notification $n) {
                $weight = $n->is_read ? 'text-muted' : 'font-weight-bold';

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
        Notification::whereHas('users', fn ($q) => $q->where('users.id', $request->user()->id))
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }
}
