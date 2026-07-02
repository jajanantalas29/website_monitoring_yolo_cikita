<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran - Monitoring Pameran</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input::placeholder { color: #9ca3af; font-size: 0.875rem; }
    </style>
</head>
<body class="bg-[#1f2937] antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg flex flex-col">
        <div class="bg-white rounded-[2rem] shadow-2xl p-6 sm:p-10 w-full mb-6">
            <div class="mb-8">
                <h1 class="text-[#1f2937] text-3xl sm:text-4xl font-black tracking-tighter mb-2">Pendaftaran</h1>
                <p class="text-gray-400 text-[13px] mb-4 leading-tight">Pastikan semua data dan foto sudah sesuai.</p>
                <div class="w-full border-b border-gray-200"></div>
            </div>

            <form action="{{ route('pendaftaran.store') }}" method="POST" id="registrationForm" class="space-y-6">
                @csrf
                <!-- Input Nama & Telepon -->
                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-lg tracking-tight">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-input w-full border border-gray-400 rounded-xl px-4 py-3.5 focus:outline-none transition text-gray-800" placeholder="Masukan nama lengkap anda..">
                </div>
                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-lg tracking-tight">Nomor Telepon</label>
                    <input type="number" name="nomor_telepon" id="nomor_telepon" class="form-input w-full border border-gray-400 rounded-xl px-4 py-3.5 focus:outline-none transition text-gray-800" placeholder="Masukan nomor telepon anda..">
                </div>

                <!-- Hidden Input untuk dikirim ke Controller Laravel -->
                <input type="hidden" name="json_poses" id="json_poses">

                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-lg tracking-tight">Foto Wajah (6 Pose)</label>
                    
                    <a href="{{ route('pendaftaran.kamera') }}" id="btn-start-camera" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-400 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all">
                        <p class="text-sm font-bold text-gray-600">Mulai Ambil Foto (120 Frame)</p>
                    </a>

                    <!-- Grid Preview 6 Pose -->
                    <div id="photo-grid" class="hidden grid-cols-3 gap-2 p-2 bg-gray-100 rounded-xl mt-4">
                        <div class="relative aspect-square bg-gray-300 rounded overflow-hidden"><img id="img-straight" src="" class="w-full h-full object-cover"></div>
                        <div class="relative aspect-square bg-gray-300 rounded overflow-hidden"><img id="img-left" src="" class="w-full h-full object-cover"></div>
                        <div class="relative aspect-square bg-gray-300 rounded overflow-hidden"><img id="img-right" src="" class="w-full h-full object-cover"></div>
                        <div class="relative aspect-square bg-gray-300 rounded overflow-hidden"><img id="img-mouth" src="" class="w-full h-full object-cover"></div>
                        <div class="relative aspect-square bg-gray-300 rounded overflow-hidden"><img id="img-up" src="" class="w-full h-full object-cover"></div>
                        <div class="relative aspect-square bg-gray-300 rounded overflow-hidden"><img id="img-down" src="" class="w-full h-full object-cover"></div>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="button" onclick="resetPhotos()" class="bg-white text-[#1f2937] font-bold py-3 px-8 rounded-xl">Batal</button>
            <button type="button" id="btn-daftar" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-blue-700 transition">Daftar</button>
        </div>
    </div>

    <script>
        const poseConfig = [
            { name: 'straight', key: 'temp_straight', imgId: 'img-straight' },
            { name: 'left', key: 'temp_left', imgId: 'img-left' },
            { name: 'right', key: 'temp_right', imgId: 'img-right' },
            { name: 'mouth_open', key: 'temp_mouth_open', imgId: 'img-mouth' },
            { name: 'up', key: 'temp_up', imgId: 'img-up' },
            { name: 'down', key: 'temp_down', imgId: 'img-down' },
        ];

        let capturedPoseData = {};
        let capturedPoseComplete = false;

        function resetPhotos() {
            poseConfig.forEach(pose => localStorage.removeItem(pose.key));
            window.location.reload();
        }

        function readCapturedPoses() {
            const allData = {};
            let isComplete = true;
            let capturedCount = 0;

            poseConfig.forEach(pose => {
                const rawData = localStorage.getItem(pose.key);
                const img = document.getElementById(pose.imgId);

                if (!rawData) {
                    isComplete = false;
                    if (img) img.removeAttribute('src');
                    return;
                }

                try {
                    const parsedData = JSON.parse(rawData);

                    if (!Array.isArray(parsedData) || parsedData.length === 0 || !parsedData[0]) {
                        isComplete = false;
                        if (img) img.removeAttribute('src');
                        return;
                    }

                    allData[pose.name] = parsedData;
                    capturedCount++;
                    if (img) img.src = parsedData[0];
                } catch (error) {
                    console.error('Data pose rusak:', pose.key, error);
                    localStorage.removeItem(pose.key);
                    isComplete = false;
                    if (img) img.removeAttribute('src');
                }
            });

            capturedPoseData = allData;
            capturedPoseComplete = isComplete;

            const startCameraButton = document.getElementById('btn-start-camera');
            const photoGrid = document.getElementById('photo-grid');

            if (capturedCount > 0) {
                photoGrid.classList.remove('hidden');
                photoGrid.classList.add('grid');
            } else {
                photoGrid.classList.add('hidden');
                photoGrid.classList.remove('grid');
            }

            if (capturedPoseComplete) {
                startCameraButton.classList.add('hidden');
            } else {
                startCameraButton.classList.remove('hidden');
            }

            return capturedPoseComplete;
        }

        async function submitRegistration() {
            if (!readCapturedPoses()) {
                alert("Harap selesaikan pengambilan semua 6 pose wajah!");
                return;
            }

            const btn = document.getElementById('btn-daftar');
            const originalText = btn.innerText;

            btn.innerText = "Sedang Memproses...";
            btn.disabled = true;

            try {
                const response = await fetch("{{ route('pendaftaran.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        nama_lengkap: document.getElementById('nama_lengkap').value,
                        nomor_telepon: document.getElementById('nomor_telepon').value,
                        json_poses: capturedPoseData
                    })
                });

                const contentType = response.headers.get("content-type") || "";
                const result = contentType.includes("application/json")
                    ? await response.json()
                    : { success: false, message: "Server tidak mengembalikan JSON. Kemungkinan terjadi timeout atau error server." };

                if (response.ok && result.success && result.pelanggan_id) {
                    resetPhotosOnly();
                    window.location.href = "{{ route('pendaftaran.proses') }}?id=" + encodeURIComponent(result.pelanggan_id);
                } else {
                    alert("Gagal menyimpan: " + (result.message || "Kesalahan server"));
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                console.error("Error:", error);
                alert("Terjadi kesalahan koneksi.");
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        function resetPhotosOnly() {
            poseConfig.forEach(pose => localStorage.removeItem(pose.key));
        }

        function initializeForm() {
            readCapturedPoses();
            document.getElementById('btn-daftar').removeEventListener('click', submitRegistration);
            document.getElementById('btn-daftar').addEventListener('click', submitRegistration);
        }

        document.addEventListener("DOMContentLoaded", initializeForm);
        window.addEventListener("pageshow", readCapturedPoses);
        document.addEventListener("visibilitychange", function() {
            if (!document.hidden) readCapturedPoses();
        });
    </script>
</body>
</html>