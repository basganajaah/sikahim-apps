<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('users-home'))->name('dashboard');
    Route::get('/users/home', [PageController::class, 'userHome'])->name('users-home');
    Route::get('/users/pendaftaran', [PendaftaranController::class, 'index'])->name('user.pendaftaran');
    Route::post('/users/kegiatan/{kegiatan}/pendaftaran', [PendaftaranController::class, 'store'])
        ->name('user.pendaftaran.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard-kegiatan', [PageController::class, 'dashboardKegiatan'])->name('dashboard-kegiatan');
    Route::get('/create-kegiatan', [PageController::class, 'createKegiatan'])->name('create-kegiatan');
    Route::get('/calendar', [PageController::class, 'calendar'])->name('calendar');
    Route::get('/detail-kegiatan/{kegiatan}', [PageController::class, 'detailKegiatan'])->name('detail-kegiatan');
    Route::get('/evaluation/{kegiatan}', [PageController::class, 'evaluation'])->name('evaluation');
    Route::get('/kelola-pengguna', [PageController::class, 'kelolaPengguna'])->name('kelola-pengguna');
    Route::get('/list-kegiatan', [KegiatanController::class, 'index'])->name('list-kegiatan');
    Route::post('/list-kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
