<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminNames = [
            'Fajar Maulana',
            'Rizky Hidayat',
            'Ahmad Fauzan',
            'Dimas Pratama',
            'Bagus Ramadhan',
            'Yusuf Kurniawan',
            'Ilham Setiawan',
            'Arif Nugroho',
            'Hendra Wijaya',
            'M. Reza Firmansyah',
        ];

        foreach ($adminNames as $index => $name) {
            $number = $index + 1;
            DB::table('users')->updateOrInsert(
                ['email' => "admin{$number}@example.com"],
                [
                    'name' => $name,
                    'role' => 'admin',
                    'password' => Hash::make('password'),
                    'updated_at' => now(),
                ]
            );
        }

        $userNames = [
            'Aditya Saputra',
            'Aisyah Nuraini',
            'Andi Setiawan',
            'Anisa Putri Lestari',
            'Bima Adi Nugraha',
            'Citra Maharani',
            'Dewi Anggraini',
            'Eka Prasetyo',
            'Farhan Akbar',
            'Fitri Handayani',
            'Galih Permana',
            'Hana Salsabila',
            'Indra Gunawan',
            'Intan Permatasari',
            'Jauhar Maulana',
            'Kartika Sari',
            'Lukman Hakim',
            'Maya Puspitasari',
            'Naufal Rizaldi',
            'Nia Kurniati',
            'Oki Setiawan',
            'Putri Amalia',
            'Qomarudin Hadi',
            'Rani Oktaviani',
            'Salsabila Rahma',
            'Taufik Hidayat',
            'Umar Faruq',
            'Vina Melati',
            'Wahyu Ramadhan',
            'Yuni Astuti',
            'Zaki Mubarak',
            'Nabila Khairunnisa',
            'Raka Firmansyah',
            'Siti Aminah',
            'Teguh Santoso',
            'Wulan Safitri',
            'Yoga Pranata',
            'Zahra Aulia',
            'Rendi Kurniawan',
            'Laila Fathimah',
        ];

        foreach ($userNames as $index => $name) {
            $number = $index + 11;
            DB::table('users')->updateOrInsert(
                ['email' => "user{$number}@example.com"],
                [
                    'name' => $name,
                    'role' => 'user',
                    'password' => Hash::make('password'),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
