<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    // 1. Endpoint untuk memberikan data "Ingatan Wajah" ke AI
    public function getEmbeddings()
    {
        try {
            $pelanggan = DB::table('pelanggans')
                // Menghubungkan tabel pelanggans dengan tabel kartus
                // Asumsi: di tabel pelanggans ada kolom 'id' dan di tabel kartus ada kolom 'pelanggan_id'
                ->join('kartus', 'pelanggans.id', '=', 'kartus.pelanggan_id')
                ->whereNotNull('pelanggans.embedding') // Pastikan sudah ada data wajah
                ->where('pelanggans.status_ruangan', 'di_dalam') // Hanya yang statusnya sedang di dalam
                ->select(
                    'pelanggans.id', 
                    'pelanggans.nama_lengkap', 
                    'pelanggans.embedding', 
                    'kartus.uid_kartu'
                )
                ->get();

            return response()->json([
                'success' => true, 
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 2. Endpoint untuk menerima laporan foto pelanggaran dari AI
    public function saveViolation(Request $request)
    {
        try {
            // Ambil data yang dikirim oleh Python
            $waktu = $request->waktu;
            $status = $request->status;
            $pelanggan_id = $request->pelanggan_id;
            $foto_base64 = $request->foto_base64; // Foto dikirim dalam bentuk teks sandi
            
            // TAMBAHAN: Tangkap data nomor kamera dari Python
            $kamera = $request->kamera; 

            $filename = 'violation_' . time() . '.jpg';
            
            // Terjemahkan sandi base64 kembali menjadi file foto fisik
            $foto_data = base64_decode($foto_base64);
            Storage::disk('public')->put('violation_images/' . $filename, $foto_data);

            // Masukkan ke Database MySQL
            DB::table('history_pelanggarans')->insert([
                'pelanggan_id' => $pelanggan_id,
                'waktu' => $waktu,
                'gambar_bukti' => 'violation_images/' . $filename,
                'status' => $status,
                'kamera' => $kamera ?? null, // TAMBAHAN: Masukkan ke kolom kamera (jika kosong, isi dengan null)
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Pelanggaran sukses dicatat di Server Kontrakan!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}