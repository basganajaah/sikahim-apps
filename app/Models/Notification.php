<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';

    protected $primaryKey = 'notification_id';

    protected $fillable = ['title', 'message', 'sent_at', 'is_read'];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'mendapat_notifikasi', 'notification_id', 'id');
    }

    // shortcut: is_read lives on notification table; move is_read to mendapat_notifikasi pivot if per-user read state is needed
    public static function notifyUsers(iterable $userIds, string $title, string $message): void
    {
        $notification = self::firstOrCreate(
            ['title' => $title],
            ['message' => $message, 'sent_at' => now(), 'is_read' => false]
        );

        $notification->users()->syncWithoutDetaching($userIds);
    }

    public static function syncForUser(?int $userId = null): void
    {
        $registrations = Pendaftaran::with('kegiatan')
            ->when($userId, fn ($q) => $q->where('id', $userId))
            ->get();

        foreach ($registrations as $reg) {
            $kegiatan = $reg->kegiatan;
            if (! $kegiatan) {
                continue;
            }

            if ($kegiatan->datetime_started->isFuture() && $kegiatan->datetime_started->lte(now()->addDay())) {
                self::notifyUsers(
                    [$reg->id],
                    "Pengingat H-1: {$kegiatan->activity_name}",
                    "Kegiatan {$kegiatan->activity_name} yang Anda ikuti akan dimulai pada {$kegiatan->datetime_started->format('d M Y H:i')} di {$kegiatan->location}."
                );
            }

            if (($kegiatan->datetime_started->isPast() && $kegiatan->datetime_finished->isFuture()) || $kegiatan->status === 'Berlangsung') {
                self::notifyUsers(
                    [$reg->id],
                    "Kegiatan Dimulai: {$kegiatan->activity_name}",
                    "Kegiatan {$kegiatan->activity_name} yang Anda ikuti sedang berlangsung di {$kegiatan->location}."
                );
            }
        }
    }
}
