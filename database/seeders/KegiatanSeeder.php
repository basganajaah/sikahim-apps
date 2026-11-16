<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('role', 'admin')->value('id');

        if (! $adminId) {
            $this->command->warn('Tidak ada admin. Kegiatan tidak dapat dibuat.');

            return;
        }

        $activities = [
            [
                'activity_name' => 'Kajian Islam dan Teknologi',
                'category_name' => 'Kajian',
                'penyelenggara' => 'DKM Masjid SIKAHIM',
                'start' => '2026-12-05 09:00:00',
                'finish' => '2026-12-05 12:00:00',
                'location' => 'Masjid Raya',
                'max_participant' => 100,
                'description' => 'Mendiskusikan peran Islam dalam perkembangan teknologi dan kehidupan jamaah.',
                'status' => 'Belum Mulai',
                'verification' => 'Terverifikasi',
                'tags' => ['Islam', 'Teknologi', 'Islami'],
            ],
            [
                'activity_name' => 'Lomba Baca dan Tulis Al-Qur\'an',
                'category_name' => 'Kompetisi',
                'penyelenggara' => 'Remaja Masjid SIKAHIM',
                'start' => '2026-12-12 08:00:00',
                'finish' => '2026-12-12 17:00:00',
                'location' => 'Lapangan Masjid',
                'max_participant' => 200,
                'description' => 'Kompetisi untuk mengembangkan kecintaan generasi muda kepada Al-Qur\'an.',
                'status' => 'Belum Mulai',
                'verification' => 'Menunggu verifikasi',
                'tags' => ['Al-Qur\'an', 'Lomba', 'Belajar'],
            ],
            [
                'activity_name' => 'Pelatihan Manajemen Waktu Islami',
                'category_name' => 'Pelatihan',
                'penyelenggara' => 'Biro Pendidikan Masjid',
                'start' => '2026-11-28 10:00:00',
                'finish' => '2026-11-28 15:00:00',
                'location' => 'Ruang Serbaguna',
                'max_participant' => 50,
                'description' => 'Pelatihan mengelola waktu dengan prinsip disiplin dan amanah dalam Islam.',
                'status' => 'Belum Mulai',
                'verification' => 'Terverifikasi',
                'tags' => ['Belajar', 'Mengaji', 'Dakwah'],
            ],
            [
                'activity_name' => 'Aksi Sosial Solidaritas Umat',
                'category_name' => 'Sosial',
                'penyelenggara' => 'Relawan Peduli Masjid',
                'start' => '2026-12-20 08:00:00',
                'finish' => '2026-12-20 14:00:00',
                'location' => 'Kampung Harapan',
                'max_participant' => 150,
                'description' => 'Kegiatan sosial untuk memperkuat kepedulian dan solidaritas antarjamaah.',
                'status' => 'Belum Mulai',
                'verification' => 'Terverifikasi',
                'tags' => ['Solidaritas', 'Kebersamaan', 'Sosial'],
            ],
        ];

        foreach ($activities as $activity) {
            $categoryId = DB::table('kategori')
                ->where('category_name', $activity['category_name'])
                ->value('category_id');

            DB::table('kegiatan')->updateOrInsert(
                ['activity_name' => $activity['activity_name']],
                [
                    'id' => $adminId,
                    'category_id' => $categoryId,
                    'penyelenggara' => $activity['penyelenggara'],
                    'datetime_started' => Carbon::parse($activity['start']),
                    'datetime_finished' => Carbon::parse($activity['finish']),
                    'location' => $activity['location'],
                    'max_participant' => $activity['max_participant'],
                    'description' => $activity['description'],
                    'status' => $activity['status'],
                    'verification' => $activity['verification'],
                    'updated_at' => now(),
                ]
            );

            $activityId = DB::table('kegiatan')
                ->where('activity_name', $activity['activity_name'])
                ->value('activity_id');

            $tagIds = DB::table('tag')
                ->whereIn('tag_name', $activity['tags'])
                ->pluck('tag_id');

            DB::table('memiliki_tag')->where('activity_id', $activityId)->delete();
            foreach ($tagIds as $tagId) {
                DB::table('memiliki_tag')->insert([
                    'activity_id' => $activityId,
                    'tag_id' => $tagId,
                ]);
            }
        }
    }
}
