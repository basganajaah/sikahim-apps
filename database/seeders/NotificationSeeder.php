<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notificationId = DB::table('notification')->updateOrInsert(
            ['title' => 'Selamat datang di SIKAHIM'],
            [
                'message' => 'Temukan dan ikuti agenda harian masjid melalui SIKAHIM.',
                'sent_at' => now(),
                'is_read' => false,
                'updated_at' => now(),
            ]
        );

        $notificationId = DB::table('notification')
            ->where('title', 'Selamat datang di SIKAHIM')
            ->value('notification_id');

        $userIds = DB::table('users')->pluck('id');
        foreach ($userIds as $userId) {
            DB::table('mendapat_notifikasi')->updateOrInsert([
                'notification_id' => $notificationId,
                'id' => $userId,
            ]);
        }
    }
}
