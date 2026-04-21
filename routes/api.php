<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController; // <-- Panggil ApiController kamu

// Pintu masuk untuk AI mengambil ingatan wajah
Route::get('/get-embeddings', [ApiController::class, 'getEmbeddings']);

// Pintu masuk untuk AI mengirim foto pelanggaran
Route::post('/save-violation', [ApiController::class, 'saveViolation']);