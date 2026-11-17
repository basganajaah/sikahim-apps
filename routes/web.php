<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriTagController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => auth()->user()?->role === 'admin'
        ? redirect()->route('home')
        : redirect()->route('users-home')
    )->name('dashboard');
    Route::get('/users/home', [PageController::class, 'userHome'])->name('users-home');
    Route::get('/users/pendaftaran', [PendaftaranController::class, 'index'])->name('user.pendaftaran');
    Route::post('/users/kegiatan/{kegiatan}/pendaftaran', [PendaftaranController::class, 'store'])
        ->name('user.pendaftaran.store');
    Route::patch('/users/pendaftaran/{pendaftaran}/cancel', [PendaftaranController::class, 'cancel'])
        ->name('user.pendaftaran.cancel');

    Route::get('/kegiatan/{kegiatan}/materi/{index}', [KegiatanController::class, 'downloadMateri'])
        ->whereNumber('index')
        ->name('kegiatan.materi.download');

    Route::get('/notifications', [PageController::class, 'notificationsList'])->name('notifications.index');
    Route::get('/notifications/get', [PageController::class, 'notifications'])->name('notifications.get');
    Route::post('/notifications/read', [PageController::class, 'markNotificationsRead'])->name('notifications.read');
    Route::patch('/notifications/{notification}/toggle', [PageController::class, 'toggleNotificationRead'])->name('notifications.toggle');

    Route::prefix('admin')->group(function () {
        Route::get('/calendar', [PageController::class, 'calendar'])->name('calendar');
        Route::get('/list-kegiatan', [KegiatanController::class, 'index'])->name('list-kegiatan');
        Route::get('/detail-kegiatan/{kegiatan}', [PageController::class, 'detailKegiatan'])->name('detail-kegiatan');
        Route::get('/evaluation/{kegiatan}', [PageController::class, 'evaluation'])->name('evaluation');
        Route::post('/evaluation/{kegiatan}', [PageController::class, 'storeEvaluation'])->name('evaluation.store');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/laporan/export-csv', [HomeController::class, 'exportCsv'])->name('laporan.export-csv');
    Route::get('/audit-logs', [HomeController::class, 'auditLogs'])->name('audit-logs.index');

    Route::get('/dashboard-kegiatan', [PageController::class, 'dashboardKegiatan'])->name('dashboard-kegiatan');
    Route::get('/create-kegiatan', [PageController::class, 'createKegiatan'])->name('create-kegiatan');
    Route::get('/kelola-pendaftaran', [PageController::class, 'kelolaPendaftaran'])->name('kelola-pendaftaran');
    Route::patch('/pendaftaran/{pendaftaran}/status', [PendaftaranController::class, 'updateStatus'])->name('admin.pendaftaran.update-status');

    Route::get('/kategori-tag', [KategoriTagController::class, 'index'])->name('kategori-tag.index');
    Route::post('/kategori', [KategoriTagController::class, 'storeKategori'])->name('kategori.store');
    Route::put('/kategori/{kategori}', [KategoriTagController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriTagController::class, 'destroyKategori'])->name('kategori.destroy');
    Route::post('/tag', [KategoriTagController::class, 'storeTag'])->name('tag.store');
    Route::put('/tag/{tag}', [KategoriTagController::class, 'updateTag'])->name('tag.update');
    Route::delete('/tag/{tag}', [KategoriTagController::class, 'destroyTag'])->name('tag.destroy');

    Route::get('/kelola-pengguna', [PageController::class, 'kelolaPengguna'])->name('kelola-pengguna');
    Route::post('/list-kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::get('/kegiatan/{kegiatan}/edit', [KegiatanController::class, 'edit'])->name('kegiatan.edit');
    Route::put('/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::patch('/kegiatan/{kegiatan}/status', [KegiatanController::class, 'updateStatus'])->name('kegiatan.update-status');
    Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
