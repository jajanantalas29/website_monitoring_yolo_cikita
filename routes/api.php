<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController; 

// Pintu masuk untuk AI mengambil ingatan wajah
Route::get('/get-embeddings', [ApiController::class, 'getEmbeddings']);

// Pintu masuk untuk AI mengirim foto pelanggaran (Node 1)
Route::post('/save-violation', [ApiController::class, 'saveViolation']);

// PERBAIKAN ALUR NODE 2: Pintu masuk untuk AI mengubah status ruangan (Masuk/Keluar)
Route::post('/update-access-status', [ApiController::class, 'updateAccessStatus']);