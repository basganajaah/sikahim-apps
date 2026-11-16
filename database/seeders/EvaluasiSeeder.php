<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluasiSeeder extends Seeder
{
    public function run(): void
    {
        $registration = DB::table('pendaftaran')->orderBy('registration_id')->first();

        if (! $registration) {
            return;
        }

        DB::table('evaluasi')->updateOrInsert(
            ['id' => $registration->id, 'activity_id' => $registration->activity_id],
            [
                'rating' => 5,
                'review' => 'Kegiatan berjalan dengan baik dan bermanfaat bagi jamaah.',
                'updated_at' => now(),
            ]
        );
    }
}
