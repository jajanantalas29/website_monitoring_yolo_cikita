<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Kartu;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ==========================================
    // MENU PELANGGAN
    // ==========================================

    public function index()
    {
        $pelanggan = Pelanggan::all();
        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        // 1. Validasi: Ubah 'json_poses' menjadi 'array' bukan 'string'
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'json_poses'    => 'required|array', // Pastikan ini array
        ]);

        // 2. Tidak perlu json_decode, karena Laravel otomatis mengonversi JSON request menjadi array
        $poses = $request->json_poses; 

        // 3. PROSES INTEGRASI KE AI SERVER
        try {
            // Pastikan struktur pengiriman ke AI tetap sesuai yang diminta Python
            $response = Http::withoutVerifying()
                ->connectTimeout(120) // Naikkan jadi 120 detik untuk koneksi awal
                ->timeout(600)       // Naikkan jadi 600 detik (10 menit) untuk proses AI 120 foto
                ->post('http://192.168.18.12:8001/api/register-face', [
                    'visitor_id' => Str::slug($request->nama_lengkap) . '-' . time(),
                    'poses'      => $poses
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['success']) && $data['success'] == true) {
                
                $saveBase64 = function($base64_string, $prefix) {
                    if (!$base64_string || strpos($base64_string, 'data:image') === false) return null;
                    $image_data = explode(',', $base64_string)[1];
                    $fileName = $prefix . '_' . time() . '_' . Str::random(5) . '.jpg';
                    Storage::disk('public')->put('wajah/' . $fileName, base64_decode($image_data));
                    return $fileName;
                };

                Pelanggan::create([
                    'nama_lengkap'   => $request->nama_lengkap,
                    'nomor_telepon'  => $request->nomor_telepon,
                    'foto_lurus'     => $saveBase64($poses['straight'][0], 'lurus'),
                    'foto_kiri'      => $saveBase64($poses['left'][0], 'kiri'),
                    'foto_kanan'     => $saveBase64($poses['right'][0], 'kanan'),
                    'foto_mulut'     => $saveBase64($poses['mouth_open'][0], 'mulut'),
                    'foto_menunduk'  => $saveBase64($poses['down'][0], 'menunduk'),
                    'foto_mendongak' => $saveBase64($poses['up'][0], 'mendongak'), // PENAMBAHAN POSE MENDONGAK
                    'embedding'      => json_encode($data['embeddings']), 
                ]);

                // KEMBALIKAN JSON (PENTING untuk frontend fetch)
                return response()->json(['success' => true]);
            
            } else {
                return response()->json(['success' => false, 'message' => $data['message'] ?? 'AI Gagal deteksi wajah.'], 400);
            }

        } catch (\Exception $e) {
            // Tambahkan log error ke laravel.log supaya kita bisa baca aslinya
            \Illuminate\Support\Facades\Log::error('Gagal Simpan Pendaftaran: ' . $e->getMessage());
            
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'nomor_telepon' => 'required',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->only(['nama_lengkap', 'nomor_telepon']));

        return redirect()->route('admin.pelanggan')->with('success', 'Data diperbarui!');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        Storage::disk('public')->delete([
            'wajah/' . $pelanggan->foto_lurus,
            'wajah/' . $pelanggan->foto_kiri,
            'wajah/' . $pelanggan->foto_kanan,
            'wajah/' . $pelanggan->foto_mulut,
            'wajah/' . $pelanggan->foto_menunduk,
            'wajah/' . $pelanggan->foto_mendongak // PENAMBAHAN HAPUS FILE MENDONGAK
        ]);
        $pelanggan->delete();
        return redirect()->route('admin.pelanggan')->with('success', 'Data dihapus!');
    }

    // ==========================================
    // MENU STATUS & PELANGGARAN (Dibiarkan Tetap)
    // ==========================================

    public function status()
    {
        $pelanggan = Pelanggan::all();
        return view('admin.status.index', compact('pelanggan'));
    }

    public function scanStatus(Request $request)
    {
        $request->validate(['uid_kartu' => 'required|string']);
        $kartu = Kartu::where('uid_kartu', $request->uid_kartu)->first();

        if (!$kartu) {
            return redirect()->route('admin.status')->with('error', 'Kartu tidak terdaftar!');
        }

        $pelanggan = $kartu->pelanggan;
        if (!$pelanggan) {
            return redirect()->route('admin.status')->with('error', 'Kartu tidak terhubung ke pelanggan.');
        }

        if ($pelanggan->status_ruangan === 'di_dalam') {
            $pelanggan->status_ruangan = 'di_luar';
            $pesan = $pelanggan->nama_lengkap . ' telah keluar ruangan.';
        } else {
            $pelanggan->status_ruangan = 'di_dalam';
            $pesan = $pelanggan->nama_lengkap . ' telah masuk ruangan.';
        }

        $pelanggan->save();
        return redirect()->route('admin.status')->with('success', $pesan);
    }

    public function show($id)
    {
        $pelanggan = Pelanggan::with('kartu')->findOrFail($id);
        return view('admin.status.detail', compact('pelanggan'));
    }

    public function pelanggaran()
    {
        $pelanggaran = DB::table('history_pelanggarans')
            ->leftJoin('pelanggans', 'history_pelanggarans.pelanggan_id', '=', 'pelanggans.id')
            ->select('history_pelanggarans.*', 'pelanggans.nama_lengkap as nama', 'pelanggans.nomor_telepon')
            ->orderBy('history_pelanggarans.waktu', 'desc')
            ->get();

        return view('admin.pelanggaran.index', compact('pelanggaran'));
    }

    public function destroyPelanggaran($id)
    {
        $pelanggaran = DB::table('history_pelanggarans')->where('id', $id)->first();
        if ($pelanggaran) {
            if ($pelanggaran->gambar_bukti && Storage::disk('public')->exists($pelanggaran->gambar_bukti)) {
                Storage::disk('public')->delete($pelanggaran->gambar_bukti);
            }
            DB::table('history_pelanggarans')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Data pelanggaran berhasil dihapus!');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    public function detailPelanggaran($id)
    {
        // PENAMBAHAN 'pelanggans.foto_mendongak' PADA SELECT
        $pelanggaran = DB::table('history_pelanggarans')
            ->leftJoin('pelanggans', 'history_pelanggarans.pelanggan_id', '=', 'pelanggans.id')
            ->select('history_pelanggarans.*', 'pelanggans.nama_lengkap as nama', 'pelanggans.nomor_telepon', 'pelanggans.foto_lurus', 'pelanggans.foto_kiri', 'pelanggans.foto_kanan', 'pelanggans.foto_mulut', 'pelanggans.foto_menunduk', 'pelanggans.foto_mendongak')
            ->where('history_pelanggarans.id', $id)
            ->first();

        if (!$pelanggaran) return redirect()->route('admin.pelanggaran')->with('error', 'Data tidak ditemukan!');
        return view('admin.pelanggaran.detail', compact('pelanggaran'));
    }

    public function kartu()
    {
        $kartus = Kartu::with('pelanggan')->latest()->get();
        return view('admin.kartu.index', compact('kartus'));
    }

    public function createKartu()
    {
        $pelanggan = Pelanggan::doesntHave('kartu')->get();
        return view('admin.kartu.create', compact('pelanggan'));
    }

    public function storeKartu(Request $request)
    {
        $request->validate(['pelanggan_id' => 'required|exists:pelanggans,id', 'uid_kartu' => 'required|unique:kartus,uid_kartu']);
        Kartu::create($request->all());
        return redirect()->route('admin.kartu')->with('success', 'Kartu terdaftar!');
    }

    public function destroyKartu($id)
    {
        Kartu::findOrFail($id)->delete();
        return redirect()->route('admin.kartu')->with('success', 'Kartu dihapus!');
    }

    public function showKartu($id)
    {
        $kartu = Kartu::with('pelanggan')->findOrFail($id);
        return view('admin.kartu.show', compact('kartu'));
    }
}