<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendaftaranSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->where('role', 'user')->orderBy('id')->limit(3)->pluck('id');
        $activities = DB::table('kegiatan')->where('verification', 'Terverifikasi')->orderBy('activity_id')->get();

        foreach ($activities as $activityIndex => $activity) {
            foreach ($userIds as $userIndex => $userId) {
                if (($activityIndex + $userIndex) % 2 !== 0) {
                    continue;
                }

                DB::table('pendaftaran')->updateOrInsert(
                    ['id' => $userId, 'activity_id' => $activity->activity_id],
                    [
                        'registrant_name' => DB::table('users')->where('id', $userId)->value('name'),
                        'age' => 20 + $userIndex,
                        'gender' => $userIndex % 2 === 0,
                        'registration_date' => now()->toDateString(),
                        'registration_status' => 'Terdaftar',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
