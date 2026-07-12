<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    // ====================================================
    // HELPER: NORMALISASI UID
    // ====================================================
    // ESP32 mengirim UID sebagai HEX uppercase (mis. "04A2B3C4").
    // Database kartus menyimpan UID sebagai decimal string (mis. "78901234").
    // Fungsi ini mengkonversi input HEX ke decimal jika diperlukan.
    private function normalizeUID($uid)
    {
        if (!$uid) return '';
        $clean = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $uid));
        if ($clean === '') return '';

        // Jika ada huruf A-F, kemungkinan format HEX dari ESP32
        if (preg_match('/[A-F]/', $clean)) {
            $dec = strval(intval($clean, 16));
            return $dec;
        }
        // Jika hanya angka, sudah decimal
        return $clean;
    }

    // ====================================================
    // HELPER: LOG AKSES PINTU (audit trail)
    // ====================================================
    private function logDoorAccess($pelanggan_id, $uid_kartu, $direction, $status, $similarity, $reason)
    {
        try {
            DB::table('history_akses_pintu')->insert([
                'pelanggan_id'      => $pelanggan_id,
                'uid_kartu'         => $uid_kartu,
                'direction'         => $direction,
                'status'            => $status,
                'similarity_score'  => $similarity,
                'reason'            => $reason,
                'waktu'             => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal log akses pintu: ' . $e->getMessage());
        }
    }

    // 1. Endpoint untuk memberikan data "Ingatan Wajah" ke AI (Node 1 & Node 2)
    public function getEmbeddings()
    {
        try {
            $pelanggan = DB::table('pelanggans')
                ->join('kartus', 'pelanggans.id', '=', 'kartus.pelanggan_id')
                ->whereNotNull('pelanggans.embedding')
                // --- PERBAIKAN: Hanya kirim data pelanggan yang sedang di dalam ruangan ---
                ->where('pelanggans.status_ruangan', 'di_dalam') 
                // -------------------------------------------------------------------------
                ->select(
                    'pelanggans.id',
                    'pelanggans.nama_lengkap',
                    'pelanggans.embedding',
                    'kartus.uid_kartu',
                    'pelanggans.status_ruangan'
                )
                ->get();

            return response()->json(['success' => true, 'data' => $pelanggan]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ====================================================
    // 1b. Endpoint: GET EMBEDDING BY UID (untuk AI Node 2)
    // Dipanggil oleh AI server saat verifikasi wajah pintu.
    // Mengembalikan 1 pelanggan yang UID-nya cocok.
    // ====================================================
    public function getEmbeddingByUid(Request $request)
    {
        try {
            $uidHex = $request->input('uid_kartu', '');
            $uid = $this->normalizeUID($uidHex);

            if (!$uid) {
                return response()->json(['success' => false, 'message' => 'uid_empty'], 200);
            }

            $kartu = DB::table('kartus')->where('uid_kartu', $uid)->first();
            if (!$kartu) {
                return response()->json(['success' => false, 'message' => 'uid_not_registered'], 200);
            }

            $pel = DB::table('pelanggans')->where('id', $kartu->pelanggan_id)->first();
            if (!$pel || !$pel->embedding) {
                return response()->json(['success' => false, 'message' => 'no_embedding'], 200);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'pelanggan_id'    => $pel->id,
                    'nama_lengkap'    => $pel->nama_lengkap,
                    'embedding'        => $pel->embedding,
                    'status_ruangan'  => $pel->status_ruangan,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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

            $similarity = $request->input('similarity');
            $margin = $request->input('margin');
            $topCandidates = $request->input('top_candidates');
            $voteCount = $request->input('vote_count');
            $totalFrames = $request->input('total_frames');
            $lightingCondition = $request->input('lighting_condition');

            $filename = 'violation_' . time() . '.jpg';

            $foto_data = base64_decode($foto_base64);
            Storage::disk('public')->put('violation_images/' . $filename, $foto_data);

            DB::table('history_pelanggarans')->insert([
                'pelanggan_id' => $pelanggan_id,
                'waktu' => $waktu,
                'gambar_bukti' => 'violation_images/' . $filename,
                'status' => $status,
                'kamera' => $kamera ?? null,
                'similarity_score' => $similarity,
                'match_margin' => $margin,
                'top_candidates' => is_array($topCandidates) ? json_encode($topCandidates) : null,
                'vote_count' => $voteCount,
                'total_frames' => $totalFrames,
                'lighting_condition' => $lightingCondition,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Pelanggaran sukses dicatat di Server!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ====================================================
    // 3. AKSES MASUK (Node 2 Pintu - dari luar)
    // ====================================================
    // Alur: ESP32 tap -> POST uid_kartu -> Laravel cek UID + ambil embedding dari DB lokal
    //   -> POST {uid_kartu, pelanggan_id, embedding, nama} ke AI /trigger2
    //      -> AI capture CCTV + compare wajah -> return granted/denied
    //   -> Laravel update status_ruangan + log audit + return ke ESP32
    //
    // FIX: Embedding di-pass dari Laravel ke AI untuk menghindari deadlock
    // (Laravel single-threaded tidak bisa menerima callback AI saat masih
    //  menahan request dari ESP32).
    public function aksesMasuk(Request $request)
    {
        $uidHex = $request->input('uid_kartu', '');
        $uid = $this->normalizeUID($uidHex);

        if (!$uid) {
            $this->logDoorAccess(null, $uidHex, 'masuk', 'denied', null, 'invalid_uid');
            return response()->json(['status' => 'denied', 'message' => 'GAGAL'], 200);
        }

        // PERBAIKAN: Tambahkan 'pelanggans.status_ruangan' ke dalam select
        $kartu = DB::table('kartus')
            ->join('pelanggans', 'kartus.pelanggan_id', '=', 'pelanggans.id')
            ->where('kartus.uid_kartu', $uid)
            ->select('kartus.pelanggan_id', 'pelanggans.nama_lengkap', 'pelanggans.embedding', 'pelanggans.status_ruangan')
            ->first();

        if (!$kartu) {
            $this->logDoorAccess(null, $uid, 'masuk', 'denied', null, 'uid_not_registered');
            return response()->json(['status' => 'denied', 'message' => 'GAGAL'], 200);
        }

        // --- PERBAIKAN: Cek jika pelanggan sudah di dalam ---
        if ($kartu->status_ruangan === 'di_dalam') {
            $this->logDoorAccess($kartu->pelanggan_id, $uid, 'masuk', 'denied', null, 'already_inside');
            return response()->json(['status' => 'denied', 'message' => 'GAGAL: Sudah di dalam'], 200);
        }
        // ----------------------------------------------------

        $pelanggan_id = $kartu->pelanggan_id;
        $nama_lengkap = $kartu->nama_lengkap;
        $embedding    = $kartu->embedding;

        if (!$embedding) {
            $this->logDoorAccess($pelanggan_id, $uid, 'masuk', 'denied', null, 'no_embedding');
            return response()->json(['status' => 'denied', 'message' => 'GAGAL'], 200);
        }

        $aiUrl = 'http://10.109.1.6:8001/trigger2';

        try {
            $response = Http::timeout(10)->post($aiUrl, [
                'uid_kartu'    => $uid,
                'pelanggan_id' => $pelanggan_id,
                'nama_lengkap' => $nama_lengkap,
                'embedding'    => $embedding,
                'direction'    => 'masuk',
            ]);
            $aiData = $response->json() ?? [];
        } catch (\Exception $e) {
            $this->logDoorAccess($pelanggan_id, $uid, 'masuk', 'denied', null, 'ai_server_error');
            return response()->json(['status' => 'denied', 'message' => 'GAGAL'], 200);
        }

        $access = $aiData['access'] ?? 'denied';

        if ($access === 'granted') {
            $aiPelangganId = $aiData['pelanggan_id'] ?? null;
            $sim = $aiData['similarity'] ?? 0;

            if ($aiPelangganId == $pelanggan_id) {
                DB::table('pelanggans')
                    ->where('id', $pelanggan_id)
                    ->update(['status_ruangan' => 'di_dalam', 'updated_at' => now()]);

                $this->logDoorAccess($pelanggan_id, $uid, 'masuk', 'granted', $sim, null);

                // PERBAIKAN: Sisipkan kata 'SUKSES' agar dibaca oleh ESP32
                return response()->json([
                    'status' => 'granted',
                    'similarity' => $sim,
                    'message' => 'SUKSES' 
                ], 200);
            } else {
                $this->logDoorAccess($pelanggan_id, $uid, 'masuk', 'denied', $sim, 'pelanggan_mismatch');
                return response()->json(['status' => 'denied', 'message' => 'GAGAL'], 200);
            }
        } else {
            $reason = $aiData['reason'] ?? 'face_verification_failed';
            $sim = $aiData['similarity'] ?? null;
            $this->logDoorAccess($pelanggan_id, $uid, 'masuk', 'denied', $sim, $reason);
            return response()->json(['status' => 'denied', 'message' => 'GAGAL'], 200);
        }
    }

    // ====================================================
    // 4. AKSES KELUAR (Node 2 Pintu - dari dalam)
    // ====================================================
    // Alur: ESP32 tap -> POST uid_kartu -> Laravel cek UID
    //   -> Jika terdaftar: update status_ruangan='di_luar' + log audit + return granted
    //   -> Jika tidak terdaftar: return denied
    // TIDAK ADA verifikasi AI wajah di alur keluar.
    public function aksesKeluar(Request $request)
    {
        $uidHex = $request->input('uid_kartu', '');
        $uid = $this->normalizeUID($uidHex);

        if (!$uid) {
            $this->logDoorAccess(null, $uidHex, 'keluar', 'denied', null, 'invalid_uid');
            return response()->json(['status' => 'denied', 'reason' => 'invalid_uid'], 200);
        }

        $kartu = DB::table('kartus')->where('uid_kartu', $uid)->first();
        if (!$kartu) {
            $this->logDoorAccess(null, $uid, 'keluar', 'denied', null, 'uid_not_registered');
            return response()->json(['status' => 'denied', 'reason' => 'uid_not_registered'], 200);
        }

        $pelanggan_id = $kartu->pelanggan_id;

        // KUNCI PERBAIKAN: Cek apakah pelanggan ini memiliki denda yang belum dibayar
        $adaDenda = DB::table('history_pelanggarans')
            ->where('pelanggan_id', $pelanggan_id)
            ->where('status_pembayaran', '!=', 'lunas') // Memblokir jika ada status selain 'lunas'
            ->exists();

        if ($adaDenda) {
            // Catat ke log bahwa akses ditolak karena denda, dan kirim respons DENDA ke Node 2
            $this->logDoorAccess($pelanggan_id, $uid, 'keluar', 'denied', null, 'DENDA');
            return response()->json(['status' => 'denied', 'reason' => 'DENDA'], 200);
        }

        // Jika tidak ada denda, update status_ruangan langsung (no AI verifikasi)
        DB::table('pelanggans')
            ->where('id', $pelanggan_id)
            ->update(['status_ruangan' => 'di_luar', 'updated_at' => now()]);

        $this->logDoorAccess($pelanggan_id, $uid, 'keluar', 'granted', null, null);

        return response()->json(['status' => 'granted'], 200);
    }

    // ====================================================
    // 5. UPDATE ACCESS STATUS (dipanggil oleh AI saat granted)
    // ====================================================
    // Dipakai oleh AI Node 2 untuk update status_ruangan.
    // Menerima parameter direction ("masuk"|"keluar").
    // Jika direction tidak ada, default masuk (set 'di_dalam').
    public function updateAccessStatus(Request $request)
    {
        try {
            $pelanggan_id = $request->pelanggan_id;
            $direction = $request->input('direction', 'masuk');

            if (!$pelanggan_id) {
                return response()->json(['success' => false, 'message' => 'ID Pelanggan tidak ditemukan.'], 400);
            }

            $pelanggan = DB::table('pelanggans')->where('id', $pelanggan_id)->first();
            if (!$pelanggan) {
                return response()->json(['success' => false, 'message' => 'Data pelanggan tidak ditemukan.'], 404);
            }

            // SET langsung berdasarkan direction (bukan toggle)
            $status_baru = ($direction === 'keluar') ? 'di_luar' : 'di_dalam';

            DB::table('pelanggans')
                ->where('id', $pelanggan_id)
                ->update([
                    'status_ruangan' => $status_baru,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status ruangan berhasil diubah menjadi ' . $status_baru,
                'status_sekarang' => $status_baru
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}