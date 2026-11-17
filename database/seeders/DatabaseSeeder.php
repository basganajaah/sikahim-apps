<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('production') && ! filter_var(env('FORCE_SEED', false), FILTER_VALIDATE_BOOLEAN) && \App\Models\User::count() > 0) {
            $this->command?->info('Database production sudah berisi data. Melewati seeding otomatis.');

            return;
        }

        $this->call([
            UsersTableSeeder::class,
            KategoriSeeder::class,
            TagSeeder::class,
            KegiatanSeeder::class,
            PendaftaranSeeder::class,
            EvaluasiSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
