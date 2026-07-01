<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Diproses - Monitoring Pameran</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')

    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s infinite;
        }
    </style>
</head>
<body class="bg-[#1f2937] antialiased h-screen flex items-center justify-center p-4 overflow-hidden">

    <div class="bg-white rounded-[2rem] shadow-2xl p-10 w-full max-w-md flex flex-col items-center justify-center text-center min-h-[300px]">

        <div class="mb-8 animate-pulse-slow">
            <img src="{{ asset('icon/icon_kartu_sedang_diproses.png') }}" alt="Sedang Diproses" class="w-32 h-32 object-contain">
        </div>

        <h2 class="text-[#1f2937] text-2xl font-bold tracking-tight">
            Kartu anda sedang diproses...
        </h2>

        <p id="status-message" class="text-gray-500 text-sm mt-4 leading-relaxed">
            Mohon tunggu, sistem sedang memvalidasi wajah dengan AI.
        </p>

    </div>

    <script>
        const pelangganId = new URLSearchParams(window.location.search).get('id');
        const statusMessage = document.getElementById('status-message');
        const statusUrlTemplate = "{{ route('pendaftaran.status', ['id' => '__ID__']) }}";
        const berhasilUrl = "{{ route('pendaftaran.berhasil') }}";
        const formUrl = "{{ route('pendaftaran.form') }}";

        if (!pelangganId) {
            alert('ID pendaftaran tidak ditemukan. Silakan ulangi pendaftaran.');
            window.location.href = formUrl;
        }

        async function cekStatusPendaftaran() {
            try {
                const response = await fetch(statusUrlTemplate.replace('__ID__', encodeURIComponent(pelangganId)), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const contentType = response.headers.get('content-type') || '';
                const result = contentType.includes('application/json')
                    ? await response.json()
                    : { success: false, message: 'Server tidak mengembalikan JSON.' };

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal mengecek status pendaftaran.');
                }

                if (result.status === 'berhasil') {
                    window.location.href = berhasilUrl;
                    return;
                }

                if (result.status === 'gagal') {
                    alert('Pendaftaran gagal: ' + (result.message || 'AI gagal memproses wajah.'));
                    window.location.href = formUrl;
                    return;
                }

                statusMessage.innerText = 'Data wajah masih diproses. Mohon tunggu sebentar.';
                setTimeout(cekStatusPendaftaran, 3000);
            } catch (error) {
                console.error('Error:', error);
                statusMessage.innerText = 'Koneksi sedang tidak stabil. Sistem akan mencoba mengecek ulang.';
                setTimeout(cekStatusPendaftaran, 5000);
            }
        }

        cekStatusPendaftaran();
    </script>

</body>
</html>