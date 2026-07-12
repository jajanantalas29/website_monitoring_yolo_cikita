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
            // PERBAIKAN: Diubah dari pelanggaran_id menjadi pelanggan_id
            'order_id' => 'DENDA-' . $request->pelanggan_id . '-' . time(),
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

// Rute untuk menerima laporan sukses dari frontend
Route::post('/midtrans/update-lunas', function (\Illuminate\Http\Request $request) {
    // Midtrans akan mengembalikan order_id (contoh: DENDA-5-171829384)
    $order_id = $request->input('order_id');
    
    if (!$order_id) {
        return response()->json(['success' => false, 'message' => 'Order ID tidak ditemukan'], 400);
    }

    // Pecah string untuk mengambil ID Pelanggan (Angka di tengah)
    $parts = explode('-', $order_id);
    if (count($parts) >= 2) {
        // PERBAIKAN: Mengambil ID Pelanggan
        $pelanggan_id = $parts[1];

        // PERBAIKAN: Update database menjadi lunas berdasarkan pelanggan_id
        // Ini akan melunasi SEMUA baris pelanggaran untuk orang tersebut
        \Illuminate\Support\Facades\DB::table('history_pelanggarans')
            ->where('pelanggan_id', $pelanggan_id)
            ->update([
                'status_pembayaran' => 'lunas',
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Format Order ID salah'], 400);
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