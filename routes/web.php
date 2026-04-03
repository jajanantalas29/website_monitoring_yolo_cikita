<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CekAdmin; // <--- PASTIKAN BARIS INI ADA
use Illuminate\Support\Facades\Route;

// --- HALAMAN PUBLIK ---
Route::get('/', function () { return view('welcome'); })->name('landing');
Route::get('/pendaftaran', function () { return view('pendaftaran.index'); })->name('pendaftaran.index');
Route::get('/pendaftaran/form', function () { return view('pendaftaran.form'); })->name('pendaftaran.form');
Route::get('/pendaftaran/proses', function () { 
    return view('pendaftaran.proses'); 
})->name('pendaftaran.proses');
Route::get('/pendaftaran/berhasil', function () { 
    return view('pendaftaran.berhasil'); 
})->name('pendaftaran.berhasil');
Route::post('/pendaftaran/simpan', [AdminController::class, 'store'])->name('pendaftaran.store');

// Routes Kamera
Route::get('/pendaftaran/kamera', function () { return view('pendaftaran.kamera'); })->name('pendaftaran.kamera');
Route::get('/pendaftaran/kamera-kiri', function () { return view('pendaftaran.kamera-kiri'); })->name('pendaftaran.kamera-kiri');
Route::get('/pendaftaran/kamera-kanan', function () { return view('pendaftaran.kamera-kanan'); })->name('pendaftaran.kamera-kanan');
Route::get('/pendaftaran/kamera-mulut', function () { return view('pendaftaran.kamera-mulut'); })->name('pendaftaran.kamera-mulut');

// --- AUTHENTICATION ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- HALAMAN ADMIN (DILINDUNGI MIDDLEWARE) ---
// PERHATIKAN BARIS DI BAWAH INI:
// Gunakan [CekAdmin::class] (Kurung Siku), JANGAN function() { ... }
Route::middleware([CekAdmin::class])->group(function () {
    
    Route::get('/admin/pelanggan', [AdminController::class, 'index'])->name('admin.pelanggan');
    Route::get('/admin/pelanggan/{id}/edit', [AdminController::class, 'edit'])->name('admin.pelanggan.edit');
    Route::put('/admin/pelanggan/{id}', [AdminController::class, 'update'])->name('admin.pelanggan.update');
    Route::delete('/admin/pelanggan/{id}', [AdminController::class, 'destroy'])->name('admin.pelanggan.destroy');
    Route::get('/admin/pelanggan/{id}/detail', [AdminController::class, 'show'])->name('admin.pelanggan.show');

    Route::get('/admin/status', [AdminController::class, 'status'])->name('admin.status');
    Route::get('/admin/pelanggaran', [AdminController::class, 'pelanggaran'])->name('admin.pelanggaran');

    // Menu Daftar Kartu (Menggunakan AdminController)
    Route::get('/admin/kartu', [AdminController::class, 'kartu'])->name('admin.kartu');
    Route::get('/admin/kartu/tambah', [AdminController::class, 'createKartu'])->name('admin.kartu.create');
    Route::post('/admin/kartu/simpan', [AdminController::class, 'storeKartu'])->name('admin.kartu.store');
    Route::delete('/admin/kartu/{id}', [AdminController::class, 'destroyKartu'])->name('admin.kartu.destroy');
    Route::get('/admin/kartu/{id}/detail', [AdminController::class, 'showKartu'])->name('admin.kartu.show');

    // untuk menangkap lemparan form dari halaman Status
    Route::post('/admin/status/scan', [App\Http\Controllers\AdminController::class, 'scanStatus'])->name('admin.status.scan');
});