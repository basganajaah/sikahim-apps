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
    public function userHome(Request $request)
    {
        $categories = Kategori::orderBy('category_name')->get();
        $tags = Tag::orderBy('tag_name')->get();

        $kegiatan = Kegiatan::with(['kategori', 'tags'])
            ->withCount('pendaftaran')
            ->where('verification', 'Terverifikasi')
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', $request->string('status')),
                fn ($q) => $q->whereNotIn('status', ['Ditunda', 'Selesai'])->where('datetime_finished', '>=', now())
            )
            ->when($request->filled('search'), fn ($q) => $q->where('activity_name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('tag_id'), fn ($q) => $q->whereHas('tags', fn ($tq) => $tq->where('tag.tag_id', $request->integer('tag_id'))))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('datetime_started', $request->string('date')))
            ->when($request->filled('location'), fn ($q) => $q->where('location', 'like', '%'.$request->string('location').'%'))
            ->orderBy('datetime_started')
            ->get();

        return view('pages.user.users-home', compact('kegiatan', 'categories', 'tags'));
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

    public function kelolaPendaftaran(Request $request)
    {
        $kegiatanList = Kegiatan::orderBy('activity_name')->get();

        $pendaftaran = Pendaftaran::with(['kegiatan', 'user'])
            ->when($request->filled('activity_id'), fn ($q) => $q->where('activity_id', $request->integer('activity_id')))
            ->when($request->filled('registration_status'), fn ($q) => $q->where('registration_status', $request->string('registration_status')))
            ->latest('registration_date')
            ->get();

        return view('pages.admin.kelola-pendaftaran', compact('pendaftaran', 'kegiatanList'));
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
        $userRegistration = Pendaftaran::where('activity_id', $kegiatan->activity_id)
            ->where('id', $request->user()->id)
            ->first();

        return view('pages.detail-kegiatan', compact('kegiatan', 'userRegistration'));
    }

    public function evaluation(Request $request, Kegiatan $kegiatan)
    {
        abort_if($request->user()?->role !== 'admin' && $kegiatan->verification !== 'Terverifikasi', 404);

        $kegiatan->load('evaluasi.user');
        $isRegistered = Pendaftaran::where('activity_id', $kegiatan->activity_id)
            ->where('id', $request->user()->id)
            ->whereIn('registration_status', ['Terdaftar', 'Diterima'])
            ->exists();
        $hasEvaluated = Evaluasi::where('activity_id', $kegiatan->activity_id)
            ->where('id', $request->user()->id)
            ->exists();

        return view('pages.evaluation', compact('kegiatan', 'isRegistered', 'hasEvaluated'));
    }

    public function storeEvaluation(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        abort_if($kegiatan->verification !== 'Terverifikasi', 422, 'Kegiatan belum diverifikasi.');
        abort_if($kegiatan->status !== 'Selesai', 422, 'Evaluasi hanya dapat diisi untuk kegiatan yang sudah selesai.');

        $isRegistered = Pendaftaran::where('activity_id', $kegiatan->activity_id)
            ->where('id', $request->user()->id)
            ->whereIn('registration_status', ['Terdaftar', 'Diterima'])
            ->exists();

        abort_if(! $isRegistered, 422, 'Anda harus terdaftar sebagai peserta pada kegiatan ini untuk memberikan evaluasi.');

        $alreadyEvaluated = Evaluasi::where('activity_id', $kegiatan->activity_id)
            ->where('id', $request->user()->id)
            ->exists();

        abort_if($alreadyEvaluated, 422, 'Anda sudah pernah memberikan evaluasi untuk kegiatan ini.');

        $validated = $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'max:1000'],
        ]);

        Evaluasi::create([
            'id' => $request->user()->id,
            'activity_id' => $kegiatan->activity_id,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return redirect()->route('evaluation', $kegiatan)->with('success', 'Evaluasi kegiatan berhasil disimpan.');
    }

    public function notificationsList(Request $request)
    {
        $userId = $request->user()->id;
        Notification::syncForUser($userId);

        $notifications = Notification::with(['users' => fn ($q) => $q->where('users.id', $userId)])
            ->whereHas('users', fn ($q) => $q->where('users.id', $userId))
            ->latest('sent_at')
            ->get();

        return view('pages.notifications', compact('notifications'));
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

                return '<a href="'.route('notifications.index').'" class="dropdown-item text-wrap border-bottom py-2">'
                    .'<div class="small '.$weight.'">'.e($n->title).'</div>'
                    .'<div class="text-xs text-muted">'.e($n->message).'</div>'
                    .'<div class="text-xs text-secondary mt-1">'.$n->sent_at?->diffForHumans().'</div>'
                    .'</a>';
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

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function toggleNotificationRead(Request $request, Notification $notification): RedirectResponse
    {
        $userId = $request->user()->id;
        $pivot = DB::table('mendapat_notifikasi')
            ->where('notification_id', $notification->notification_id)
            ->where('id', $userId)
            ->first();

        abort_if(! $pivot, 404);

        DB::table('mendapat_notifikasi')
            ->where('notification_id', $notification->notification_id)
            ->where('id', $userId)
            ->update(['is_read' => ! $pivot->is_read]);

        return back()->with('success', 'Status baca notifikasi diperbarui.');
    }
}
