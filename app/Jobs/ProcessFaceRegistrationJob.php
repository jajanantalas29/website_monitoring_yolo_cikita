<?php

namespace App\Jobs;

use App\Models\Pelanggan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProcessFaceRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public bool $failOnTimeout = true;
    public int $tries = 1;

    public function __construct(
        public int $pelangganId,
        public string $posesPath
    ) {
    }

    public function handle(): void
    {
        $pelanggan = Pelanggan::find($this->pelangganId);

        if (!$pelanggan) {
            return;
        }

        try {
            if (!Storage::disk('local')->exists($this->posesPath)) {
                $pelanggan->update([
                    'status_pendaftaran' => 'gagal',
                    'pesan_error' => 'File data pose tidak ditemukan.',
                ]);

                return;
            }

            $poses = json_decode(Storage::disk('local')->get($this->posesPath), true);

            if (!is_array($poses)) {
                $pelanggan->update([
                    'status_pendaftaran' => 'gagal',
                    'pesan_error' => 'Format data pose tidak valid.',
                ]);

                return;
            }

            $response = Http::withoutVerifying()
                ->connectTimeout(10)
                ->timeout(600)
                ->post(config('services.face_ai.register_url'), [
                    'visitor_id' => Str::slug($pelanggan->nama_lengkap) . '-' . $pelanggan->id,
                    'poses' => $poses,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                $pelanggan->update([
                    // PERBAIKAN: Ubah 'embeddings' menjadi 'all_embeddings' dan bungkus dengan json_encode
                    'embedding' => isset($data['all_embeddings']) ? json_encode($data['all_embeddings']) : null,
                    'status_pendaftaran' => 'berhasil',
                    'pesan_error' => null,
                ]);

                Storage::disk('local')->delete($this->posesPath);

                return;
            }

            $pelanggan->update([
                'status_pendaftaran' => 'gagal',
                'pesan_error' => $data['message'] ?? 'AI gagal mendeteksi wajah.',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal Proses AI Pendaftaran: ' . $e->getMessage());

            $pelanggan->update([
                'status_pendaftaran' => 'gagal',
                'pesan_error' => $e->getMessage(),
            ]);
        }
    }
    
    public function failed(?Throwable $exception): void
    {
        $pelanggan = Pelanggan::find($this->pelangganId);

        if (!$pelanggan) {
            return;
        }

        $pelanggan->update([
            'status_pendaftaran' => 'gagal',
            'pesan_error' => $exception?->getMessage() ?? 'Job AI pendaftaran wajah gagal diproses.',
        ]);
    }
}