<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.admin.home', [
            'totalKegiatan' => Kegiatan::count(),
            'pendingKegiatan' => Kegiatan::where('verification', 'Menunggu verifikasi')->count(),
            'totalPendaftaran' => Pendaftaran::count(),
            'totalPengguna' => User::where('role', 'user')->count(),
        ]);
    }
}
