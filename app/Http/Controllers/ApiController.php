<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    // 1. Endpoint untuk memberikan data "Ingatan Wajah" ke AI (Node 1 & Node 2)
    public function getEmbeddings()
    {
        try {
            $pelanggan = DB::table('pelanggans')
                ->join('kartus', 'pelanggans.id', '=', 'kartus.pelanggan_id')
                ->whereNotNull('pelanggans.embedding') 
                ->where('pelanggans.status_ruangan', 'di_dalam') // DIKEMBALIKAN: Hanya mengambil status 'di_dalam' sesuai permintaan
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

    // 2. Endpoint untuk menerima laporan foto pelanggaran dari AI (Node 1 Lukisan)
    public function saveViolation(Request $request)
    {
        try {
            $waktu = $request->waktu;
            $status = $request->status;
            $pelanggan_id = $request->pelanggan_id;
            $foto_base64 = $request->foto_base64; 
            $kamera = $request->kamera; 

            $filename = 'violation_' . time() . '.jpg';
            
            $foto_data = base64_decode($foto_base64);
            Storage::disk('public')->put('violation_images/' . $filename, $foto_data);

            DB::table('history_pelanggarans')->insert([
                'pelanggan_id' => $pelanggan_id,
                'waktu' => $waktu,
                'gambar_bukti' => 'violation_images/' . $filename,
                'status' => $status,
                'kamera' => $kamera ?? null, 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Pelanggaran sukses dicatat di Server!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 3. Endpoint untuk mengubah status ruangan (Node 2 Pintu)
    public function updateAccessStatus(Request $request)
    {
        try {
            $pelanggan_id = $request->pelanggan_id;
            $status_ruangan = $request->status_ruangan;

            if (!$pelanggan_id) {
                return response()->json(['success' => false, 'message' => 'ID Pelanggan tidak ditemukan.'], 400);
            }

            // Update status pelanggan di database MySQL
            DB::table('pelanggans')
                ->where('id', $pelanggan_id)
                ->update([
                    'status_ruangan' => $status_ruangan,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true, 
                'message' => 'Status ruangan berhasil diubah menjadi ' . $status_ruangan
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}