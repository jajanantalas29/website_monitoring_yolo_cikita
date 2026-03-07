<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Kartu;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        ]);

        // Helper function untuk memproses Base64 ke File
        $saveBase64 = function($base64_string, $prefix) {
            if (!$base64_string) return null;
            
            // Memisahkan header base64 dan datanya
            $image_parts = explode(";base64,", $base64_string);
            $image_base64 = base64_decode($image_parts[1]);

            $fileName = $prefix . '_' . time() . '_' . Str::random(5) . '.jpg';
            
            // Simpan ke storage/app/public/wajah
            Storage::disk('public')->put('wajah/' . $fileName, $image_base64);

            return $fileName;
        };

        // 2. Proses Konversi dan Penyimpanan Foto Lokal
        $fotoLurus = $saveBase64($request->foto_lurus, 'lurus');
        $fotoKiri  = $saveBase64($request->foto_kiri, 'kiri');
        $fotoKanan = $saveBase64($request->foto_kanan, 'kanan');
        $fotoMulut = $saveBase64($request->foto_mulut, 'mulut');

        // 3. PROSES INTEGRASI KE AI SERVER (Port 8001)
        try {
            // Kita ambil file yang baru saja disimpan untuk dikirim ke AI
            $filePath = storage_path('app/public/wajah/' . $fotoLurus);

            $response = Http::timeout(20)->attach(
                'straight', 
                file_get_contents($filePath), 
                'foto_lurus.jpg'
            )->post('http://127.0.0.1:8001/api/register-face');

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
                    'embedding'     => $data['embedding'], // Menyimpan 512 angka vektor
                ]);

                return redirect()->route('pendaftaran.proses');
            
            } else {
                // Hapus file jika AI gagal mendeteksi wajah
                Storage::disk('public')->delete([
                    'wajah/' . $fotoLurus, 'wajah/' . $fotoKiri, 
                    'wajah/' . $fotoKanan, 'wajah/' . $fotoMulut
                ]);
                
                $msg = $data['message'] ?? 'Wajah tidak terdeteksi. Pastikan pencahayaan cukup.';
                return back()->withInput()->withErrors(['ai_error' => $msg]);
            }

        } catch (\Exception $e) {
            // Hapus file jika server AI mati
            Storage::disk('public')->delete([
                'wajah/' . $fotoLurus, 'wajah/' . $fotoKiri, 
                'wajah/' . $fotoKanan, 'wajah/' . $fotoMulut
            ]);
            return back()->withInput()->withErrors(['ai_error' => 'Koneksi ke AI Server (Port 8001) gagal. Pastikan server Python sudah jalan.']);
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
            'wajah/' . $pelanggan->foto_mulut
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

    public function show($id)
    {
        $pelanggan = Pelanggan::with('kartu')->findOrFail($id);
        return view('admin.status.detail', compact('pelanggan'));
    }

    public function pelanggaran()
    {
        // Masih data statis sesuai permintaan awal
        $data_pelanggaran = [
            ['id' => 1, 'nama' => 'Sukarjo Juninho', 'telepon' => '081223787653', 'foto' => 'Kiri.jpg'],
            ['id' => 2, 'nama' => 'Fikri Wahyudi', 'telepon' => '081223787673', 'foto' => 'Kiri.jpg'],
        ];
        $pelanggaran = json_decode(json_encode($data_pelanggaran));
        return view('admin.pelanggaran.index', compact('pelanggaran'));
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