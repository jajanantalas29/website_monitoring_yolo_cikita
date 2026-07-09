<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController; 

Route::post('/midtrans/get-token', function (Request $request) {
    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $params = [
        'transaction_details' => [
            'order_id' => 'DENDA-' . $request->pelanggaran_id . '-' . time(),
            'gross_amount' => (int) $request->denda,
        ],
        'customer_details' => [
            'first_name' => $request->nama,
            'phone' => $request->telepon,
        ]
    ];

    try {
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        return response()->json(['token' => $snapToken]);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
});

Route::post('/midtrans/tiket-token', function (\Illuminate\Http\Request $request) {
    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $params = [
        'transaction_details' => [
            'order_id' => 'TIKET-' . uniqid() . '-' . time(),
            'gross_amount' => 500, // Harga tiket 500 rupiah
        ],
        'customer_details' => [
            'first_name' => 'Pendaftar',
            'last_name' => 'Baru',
        ]
    ];

    try {
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        return response()->json(['token' => $snapToken]);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
});

Route::post('/akses-masuk', [ApiController::class, 'aksesMasuk']);
Route::post('/akses-keluar', [ApiController::class, 'aksesKeluar']);

// Pintu masuk untuk AI mengambil ingatan wajah
Route::get('/get-embeddings', [ApiController::class, 'getEmbeddings']);

// Endpoint baru: ambil embedding 1 pelanggan by UID (dipanggil AI Node 2)
Route::post('/get-embedding-by-uid', [ApiController::class, 'getEmbeddingByUid']);

// Pintu masuk untuk AI mengirim foto pelanggaran (Node 1)
Route::post('/save-violation', [ApiController::class, 'saveViolation']);

// PERBAIKAN ALUR NODE 2: Pintu masuk untuk AI mengubah status ruangan (Masuk/Keluar)
Route::post('/update-access-status', [ApiController::class, 'updateAccessStatus']);