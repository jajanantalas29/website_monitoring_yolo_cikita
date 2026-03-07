<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Foto Wajah - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>

    @vite('resources/css/app.css')
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        video { transform: scaleX(-1); } /* Mirror Effect */
    </style>
</head>
<body class="bg-[#1f2937] antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl flex flex-col">
        
        <div class="bg-white rounded-[1.5rem] shadow-2xl p-4 sm:p-6 w-full mb-6">
            
            <div class="relative w-full aspect-video border-2 border-gray-500 rounded-2xl flex items-center justify-center bg-black overflow-hidden">
                <video id="video" class="absolute inset-0 w-full h-full object-cover" autoplay muted playsinline></video>
                <canvas id="canvas" class="hidden"></canvas>

                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="white" class="w-32 h-32 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6v6H9V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5v3m0-3h3m12 0v3m0-3h-3m-12 12v3m0-3h3m12 0v3m0-3h-3" />
                    </svg>
                </div>

                <div id="status-text" class="absolute bottom-4 bg-black bg-opacity-70 text-white px-4 py-1 rounded-full text-sm font-medium z-20 transition-all duration-300">
                    Memuat AI...
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-[#1f2937] text-lg sm:text-xl font-bold tracking-tight">
                    Pastikan wajah anda lurus dan sejajar dengan kamera
                </p>
            </div>
        </div>

        <div class="flex justify-end space-x-4 w-full">
            <a href="{{ route('pendaftaran.form') }}" class="bg-white text-[#1f2937] font-bold py-2.5 px-10 rounded-xl shadow-xl hover:bg-gray-100 transition duration-300 text-sm">
                Batal
            </a>
            <button id="btn-capture" class="bg-[#1f2937] text-white font-bold py-2.5 px-10 rounded-xl shadow-xl hover:bg-gray-800 transition duration-300 text-sm">
                Ambil Manual
            </button>
        </div>

    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const statusText = document.getElementById('status-text');
        const btnCapture = document.getElementById('btn-capture');
        let isCaptured = false;

        // 1. Load Model (Hanya TinyFaceDetector agar ringan & cepat)
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
        ]).then(startVideo).catch(err => {
            console.error(err);
            statusText.innerText = "Gagal memuat AI (Cek Koneksi).";
            statusText.classList.add('bg-red-500');
        });

        // 2. Start Webcam
        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => {
                    video.srcObject = stream;
                    statusText.innerText = "Mencari wajah...";
                })
                .catch(err => {
                    console.error("Camera Error:", err);
                    statusText.innerText = "Izin kamera ditolak.";
                    statusText.classList.add('bg-red-500');
                });
        }

        // 3. Deteksi Wajah
        video.addEventListener('play', () => {
            setInterval(async () => {
                if (isCaptured) return;

                // PERBAIKAN: Hapus .withFaceLandmarks() karena modelnya tidak kita load (bikin error)
                // Gunakan ScoreThreshold 0.5 agar lebih mudah mendeteksi
                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 }));

                if (detections.length > 0) {
                    // Ambil wajah dengan score tertinggi
                    const detection = detections[0];
                    const score = detection.score;

                    // Debugging: Lihat score di console browser (F12)
                    console.log("Akurasi Wajah:", score);

                    // PERBAIKAN: Turunkan batas menjadi 0.55 (Cukup akurat untuk webcam)
                    if (score > 0.55) {
                        statusText.innerText = "Wajah Terdeteksi! Tahan...";
                        statusText.classList.remove('bg-black', 'bg-red-500');
                        statusText.classList.add('bg-green-600');

                        // Jeda 800ms sebelum ambil foto agar user siap (tidak blur)
                        setTimeout(() => {
                            if (!isCaptured) {
                                takeSnapshot();
                            }
                        }, 800);
                    } else {
                        statusText.innerText = "Wajah kurang jelas...";
                        statusText.classList.remove('bg-green-600');
                        statusText.classList.add('bg-yellow-600');
                    }
                } else {
                    statusText.innerText = "Mencari wajah...";
                    statusText.classList.remove('bg-green-600', 'bg-yellow-600');
                    statusText.classList.add('bg-black');
                }
            }, 200); // Cek lebih cepat (setiap 200ms)
        });

        // 4. Fungsi Capture
        function takeSnapshot() {
            if(isCaptured) return;
            isCaptured = true;

            statusText.innerText = "Menyimpan Foto...";
            
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Mirror flip canvas agar hasil foto sesuai preview
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataURL = canvas.toDataURL('image/jpeg');

            // Simpan ke LocalStorage
            localStorage.setItem('temp_foto_wajah', dataURL);

            // Redirect LANJUT KE WAJAH KIRI
            setTimeout(() => {
                window.location.href = "{{ route('pendaftaran.kamera-kiri') }}"; 
            }, 500);
        }

        btnCapture.addEventListener('click', () => {
            takeSnapshot();
        });
    </script>
</body>
</html>