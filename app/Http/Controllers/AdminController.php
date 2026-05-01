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
        // 1. Validasi Input (Format Base64 adalah string)
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'foto_lurus'    => 'required|string',
            'foto_kiri'     => 'required|string',
            'foto_kanan'    => 'required|string',
            'foto_mulut'    => 'required|string',
            'foto_menunduk' => 'required|string', // TAMBAHAN BARU
        ]);

        // Helper function untuk memproses Base64 ke File yang LEBIH AMAN
        $saveBase64 = function($base64_string, $prefix) {
            if (!$base64_string) return null;

            // Cek apakah ada koma (prefix base64)
            if (strpos($base64_string, ',') !== false) {
                // Jika ada prefix, pisahkan dan ambil datanya saja
                $image_parts = explode(",", $base64_string);
                $image_base64 = base64_decode($image_parts[1]);
            } else {
                // Jika murni teks sandi tanpa prefix, langsung decode
                $image_base64 = base64_decode($base64_string);
            }

            $fileName = $prefix . '_' . time() . '_' . Str::random(5) . '.jpg';

            // Simpan ke storage/app/public/wajah
            Storage::disk('public')->put('wajah/' . $fileName, $image_base64);

            return $fileName;
        };

        // 2. Proses Konversi dan Penyimpanan Foto Lokal
        $fotoLurus   = $saveBase64($request->foto_lurus, 'lurus');
        $fotoKiri    = $saveBase64($request->foto_kiri, 'kiri');
        $fotoKanan   = $saveBase64($request->foto_kanan, 'kanan');
        $fotoMulut   = $saveBase64($request->foto_mulut, 'mulut');
        $fotoMenunduk = $saveBase64($request->foto_menunduk, 'menunduk'); // TAMBAHAN BARU

        // 3. PROSES INTEGRASI KE AI SERVER (Port 8001)
        try {
            // Mengirim KELIMA foto sekaligus ke Python AI
            $response = Http::withoutVerifying()->timeout(60)
                ->attach('straight', file_get_contents(storage_path('app/public/wajah/' . $fotoLurus)), 'lurus.jpg')
                ->attach('kiri', file_get_contents(storage_path('app/public/wajah/' . $fotoKiri)), 'kiri.jpg')
                ->attach('kanan', file_get_contents(storage_path('app/public/wajah/' . $fotoKanan)), 'kanan.jpg')
                ->attach('mulut', file_get_contents(storage_path('app/public/wajah/' . $fotoMulut)), 'mulut.jpg')
                ->attach('menunduk', file_get_contents(storage_path('app/public/wajah/' . $fotoMenunduk)), 'menunduk.jpg')
                ->post('https://ai-cikita.rrlabs.web.id/api/register-face');

            $data = $response->json();

            // 4. Jika AI Berhasil Mendeteksi Wajah
            if ($response->successful() && isset($data['success']) && $data['success'] == true) {
                
                Pelanggan::create([
                    'nama_lengkap'  => $request->nama_lengkap,
                    'nomor_telepon' => $request->nomor_telepon,
                    'foto_lurus'    => $fotoLurus,
                    'foto_kiri'     => $fotoKiri,
                    'foto_kanan'    => $fotoKanan,
                    'foto_mulut'    => $fotoMulut,
                    'foto_menunduk' => $fotoMenunduk, // TAMBAHAN BARU
                    'embedding'     => json_encode($data['embedding']), // Menyimpan 512 angka vektor
                ]);

                return redirect()->route('pendaftaran.proses');
            
            } else {
                // Hapus file jika AI gagal mendeteksi wajah (termasuk menunduk)
                Storage::disk('public')->delete([
                    'wajah/' . $fotoLurus, 'wajah/' . $fotoKiri, 
                    'wajah/' . $fotoKanan, 'wajah/' . $fotoMulut,
                    'wajah/' . $fotoMenunduk // TAMBAHAN BARU
                ]);
                
                $msg = $data['message'] ?? 'Wajah tidak terdeteksi. Pastikan pencahayaan cukup.';
                return back()->withInput()->withErrors(['ai_error' => $msg]);
            }

        } catch (\Exception $e) {
            // Hapus file jika server AI mati (termasuk menunduk)
            Storage::disk('public')->delete([
                'wajah/' . $fotoLurus, 'wajah/' . $fotoKiri, 
                'wajah/' . $fotoKanan, 'wajah/' . $fotoMulut,
                'wajah/' . $fotoMenunduk // TAMBAHAN BARU
            ]);
            return back()->withInput()->withErrors(['ai_error' => 'Gagal terhubung ke AI: ' . $e->getMessage()]);
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
            'wajah/' . $pelanggan->foto_menunduk // TAMBAHAN BARU
        ]);
        $pelanggan->delete();
        return redirect()->route('admin.pelanggan')->with('success', 'Data dihapus!');
    }

    // ==========================================
    // MENU STATUS & PELANGGARAN
    // ==========================================

    public function status()
    {
        $pelanggan = Pelanggan::all();
        return view('admin.status.index', compact('pelanggan'));
    }

    // Fungsi untuk memproses hasil scan RFID di halaman Status
    public function scanStatus(Request $request)
    {
        // Pastikan ada UID yang terkirim
        $request->validate([
            'uid_kartu' => 'required|string',
        ]);

        // Cari kartu berdasarkan UID yang di-scan
        $kartu = Kartu::where('uid_kartu', $request->uid_kartu)->first();

        // Jika kartu tidak ditemukan di database
        if (!$kartu) {
            return redirect()->route('admin.status')->with('error', 'Kartu dengan UID ' . $request->uid_kartu . ' tidak terdaftar!');
        }

        // Ambil data pelanggan yang terhubung dengan kartu tersebut
        $pelanggan = $kartu->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('admin.status')->with('error', 'Kartu terdaftar, tapi tidak terhubung ke pelanggan mana pun.');
        }

        // Logika Toggle: Jika di luar jadi di dalam, jika di dalam jadi di luar
        if ($pelanggan->status_ruangan === 'di_dalam') {
            $pelanggan->status_ruangan = 'di_luar';
            $pesan = $pelanggan->nama_lengkap . ' telah keluar ruangan.';
        } else {
            $pelanggan->status_ruangan = 'di_dalam';
            $pesan = $pelanggan->nama_lengkap . ' telah masuk ruangan.';
        }

        // Simpan perubahan ke database
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
        // Mengambil data dari tabel history_pelanggarans dan join dengan tabel pelanggans
        // Left Join digunakan agar data "Tidak Dikenali" (tanpa pelanggan_id) tetap ikut tampil
        $pelanggaran = DB::table('history_pelanggarans')
            ->leftJoin('pelanggans', 'history_pelanggarans.pelanggan_id', '=', 'pelanggans.id')
            ->select(
                'history_pelanggarans.*', 
                'pelanggans.nama_lengkap as nama', // Ubah nama_lengkap menjadi nama untuk view
                'pelanggans.nomor_telepon'
            )
            ->orderBy('history_pelanggarans.waktu', 'desc')
            ->get();

        return view('admin.pelanggaran.index', compact('pelanggaran'));
    }

    public function destroyPelanggaran($id)
    {
        // Cari datanya di database
        $pelanggaran = \Illuminate\Support\Facades\DB::table('history_pelanggarans')->where('id', $id)->first();
        
        if ($pelanggaran) {
            // Hapus foto fisiknya dari folder storage agar memori Orange Pi tidak penuh
            if ($pelanggaran->gambar_bukti && \Illuminate\Support\Facades\Storage::disk('public')->exists($pelanggaran->gambar_bukti)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pelanggaran->gambar_bukti);
            }
            
            // Hapus datanya dari database
            \Illuminate\Support\Facades\DB::table('history_pelanggarans')->where('id', $id)->delete();
            
            return redirect()->back()->with('success', 'Data pelanggaran berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    public function detailPelanggaran($id)
    {
        // Ambil data detail beserta data pelanggan yang berelasi (jika ada)
        $pelanggaran = DB::table('history_pelanggarans')
            ->leftJoin('pelanggans', 'history_pelanggarans.pelanggan_id', '=', 'pelanggans.id')
            ->select(
                'history_pelanggarans.*', 
                'pelanggans.nama_lengkap as nama',
                'pelanggans.nomor_telepon',
                'pelanggans.foto_lurus',
                'pelanggans.foto_kiri',
                'pelanggans.foto_kanan',
                'pelanggans.foto_mulut',
                'pelanggans.foto_menunduk' // TAMBAHAN BARU
            )
            ->where('history_pelanggarans.id', $id)
            ->first();

        if (!$pelanggaran) {
            return redirect()->route('admin.pelanggaran')->with('error', 'Data tidak ditemukan!');
        }

        return view('admin.pelanggaran.detail', compact('pelanggaran'));
    }

    // ==========================================
    // MENU DAFTAR KARTU
    // ==========================================

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
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'uid_kartu' => 'required|unique:kartus,uid_kartu',
        ]);

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