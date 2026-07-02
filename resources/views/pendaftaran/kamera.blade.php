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
            
            <div class="relative w-full aspect-video sm:aspect-square md:aspect-video border-2 border-gray-500 rounded-2xl flex items-center justify-center bg-black overflow-hidden">
                <video id="video" class="absolute inset-0 w-full h-full object-cover" autoplay muted playsinline></video>
                <canvas id="canvas" class="hidden"></canvas>

                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="white" class="w-24 h-24 sm:w-32 sm:h-32 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6v6H9V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5v3m0-3h3m12 0v3m0-3h-3m-12 12v3m0-3h3m12 0v3m0-3h-3" />
                    </svg>
                </div>

                <div id="status-text" class="absolute bottom-4 bg-black bg-opacity-70 text-white px-4 py-1.5 rounded-full text-xs sm:text-sm font-medium z-20 transition-all duration-300">
                    Memuat AI...
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-[#1f2937] text-base sm:text-xl font-bold tracking-tight px-2">
                    Pastikan wajah anda lurus dan sejajar dengan kamera
                </p>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 w-full">
            <a href="{{ route('pendaftaran.form') }}" class="w-full sm:w-auto border-2 border-white/40 text-white text-center font-bold py-3.5 sm:py-2.5 px-10 rounded-xl hover:bg-white/10 hover:border-white transition duration-300 text-sm sm:text-base flex items-center justify-center">
                Batal
            </a>
            <button id="btn-capture" class="w-full sm:w-auto bg-white text-[#1f2937] font-bold py-3.5 sm:py-2.5 px-10 rounded-xl shadow-xl hover:bg-gray-200 transition duration-300 text-sm sm:text-base flex items-center justify-center">
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
        
        // --- Sistem Text-to-Speech (Suara Google) ---
        let lastSpokenText = ""; 
        
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            if (window.speechSynthesis.resume) window.speechSynthesis.resume();
        }
        
        function speakText(text) {
            if ('speechSynthesis' in window && text !== lastSpokenText) {
                window.speechSynthesis.cancel();
                if (window.speechSynthesis.resume) window.speechSynthesis.resume();
                lastSpokenText = text;
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID'; 
                utterance.rate = 1.0; 
                utterance.pitch = 1.1;
                window.speechSynthesis.speak(utterance);
            }
        }

        // --- Variabel Baru untuk Multi-Capture ---
        let capturedPhotos = []; // Array untuk menampung 20 foto
        const MAX_PHOTOS = 20;

        // 1. Load Model
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
        ]).then(startVideo).catch(err => {
            console.error(err);
            statusText.innerText = "Gagal memuat AI.";
            statusText.classList.add('bg-red-500');
            speakText("Gagal memuat modul kecerdasan buatan.");
        });

        // 2. Start Webcam
        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } }) 
                .then(stream => {
                    video.srcObject = stream;
                    statusText.innerText = "Mencari wajah...";
                })
                .catch(err => {
                    statusText.innerText = "Izin kamera ditolak.";
                    statusText.classList.add('bg-red-500');
                    speakText("Tolong izinkan akses kamera.");
                });
        }

        // 3. Deteksi Wajah & Multi-Capture
        video.addEventListener('play', () => {
            setTimeout(() => { speakText("Mencari wajah. Silakan menghadap lurus ke kamera."); }, 1000);

            setInterval(async () => {
                if (isCaptured || capturedPhotos.length >= MAX_PHOTOS) return;

                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 }));

                if (detections.length > 0) {
                    statusText.innerText = `Mengambil foto: ${capturedPhotos.length + 1}/${MAX_PHOTOS}`;
                    statusText.classList.add('bg-green-600');
                    
                    // Eksekusi Capture Otomatis
                    takeSnapshot();
                } else {
                    statusText.innerText = "Mencari wajah...";
                    statusText.classList.remove('bg-green-600');
                }
            }, 300); 
        });

        // 4. Fungsi Capture (Modifikasi untuk Looping)

        function savePoseToStorage(key, photos) {
            try {
                localStorage.setItem(key, JSON.stringify(photos));
                return true;
            } catch (error) {
                console.error('Storage penuh:', key, error);

                try {
                    const reducedPhotos = photos.slice(-10);
                    localStorage.setItem(key, JSON.stringify(reducedPhotos));
                    return true;
                } catch (retryError) {
                    console.error('Storage tetap gagal:', key, retryError);
                    alert('Penyimpanan foto di HP penuh. Tekan Batal lalu ulangi pengambilan foto.');
                    return false;
                }
            }
        }
        function takeSnapshot() {
            const context = canvas.getContext('2d');
            canvas.width = 300;
            canvas.height = 225;
            
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataURL = canvas.toDataURL('image/jpeg', 0.5);
            // Simpan ke array
            capturedPhotos.push(dataURL);

            if (capturedPhotos.length >= MAX_PHOTOS) {
                isCaptured = true;
                statusText.innerText = "Selesai!";
                speakText("Foto wajah lurus selesai.");
                
                // Simpan ke localStorage sebagai objek array untuk dikirim nanti
                if (!savePoseToStorage('temp_straight', capturedPhotos)) return;

                setTimeout(() => {
                    window.location.href = "{{ route('pendaftaran.kamera-kiri') }}"; 
                }, 1000);
            }
        }

        btnCapture.addEventListener('click', () => {
            if(capturedPhotos.length > 0) {
                alert("Proses otomatis sedang berjalan...");
            }
        });
    </script>
</body>
</html>