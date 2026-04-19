<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Foto Kanan - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>

    @vite('resources/css/app.css')
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Mirror effect */
        video { transform: scaleX(-1); } 
    </style>
</head>
<body class="bg-[#1f2937] antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl flex flex-col">
        
        <div class="bg-white rounded-[1.5rem] shadow-2xl p-4 sm:p-6 w-full mb-6">
            
            <div class="relative w-full aspect-video sm:aspect-square md:aspect-video border-2 border-gray-500 rounded-2xl flex items-center justify-center bg-black overflow-hidden">
                <video id="video" class="absolute inset-0 w-full h-full object-cover" autoplay muted playsinline></video>
                <canvas id="canvas" class="hidden"></canvas>

                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="w-24 h-24 sm:w-32 sm:h-32 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5V6a3 3 0 013-3h1.5M16.5 3H18a3 3 0 013 3v1.5M21 16.5V18a3 3 0 01-3 3h-1.5M7.5 21H6a3 3 0 01-3-3v-1.5" stroke-width="1" class="opacity-80"/>
                    </svg>
                </div>

                <div id="status-text" class="absolute bottom-4 bg-black bg-opacity-70 text-white px-4 py-1.5 rounded-full text-xs sm:text-sm font-medium z-20 transition-all duration-300">
                    Memuat AI Landmarks...
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-[#1f2937] text-base sm:text-xl font-bold tracking-tight px-2">
                    Pastikan wajah anda menengok ke kanan
                </p>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 w-full">
            <a href="{{ route('pendaftaran.form') }}" class="w-full sm:w-auto border-2 border-white/40 text-white text-center font-bold py-3.5 sm:py-2.5 px-10 rounded-xl hover:bg-white/10 hover:border-white transition duration-300 text-sm sm:text-base flex items-center justify-center">
                Batal
            </a>
            <button id="btn-capture" class="w-full sm:w-auto bg-white text-[#1f2937] font-bold py-3.5 sm:py-2.5 px-10 rounded-xl shadow-xl hover:bg-gray-200 transition duration-300 text-sm sm:text-base flex items-center justify-center">
                Lanjut (Manual)
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
        
        function speakText(text) {
            if ('speechSynthesis' in window && text !== lastSpokenText) {
                lastSpokenText = text;
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 1.0;
                utterance.pitch = 1.1;
                window.speechSynthesis.speak(utterance);
            }
        }
        // ---------------------------------------------

        // Load AI
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
        ]).then(startVideo).catch(err => {
            console.error(err);
            statusText.innerText = "Gagal Load AI. Cek Koneksi.";
            statusText.classList.add('bg-red-500');
            speakText("Gagal memuat sistem pelacakan wajah.");
        });

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                .then(stream => {
                    video.srcObject = stream;
                    statusText.innerText = "Silahkan tengok ke KANAN...";
                })
                .catch(err => {
                    statusText.innerText = "Kamera Error";
                    speakText("Izin kamera ditolak.");
                });
        }

        video.addEventListener('play', () => {
            // Sapaan dan Instruksi Awal untuk Kanan
            setTimeout(() => { speakText("Hebat. Sekarang silakan menengok sedikit ke kanan."); }, 1000);

            setInterval(async () => {
                if (isCaptured) return;

                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();

                if (detections.length > 0) {
                    const landmarks = detections[0].landmarks;
                    const nose = landmarks.getNose()[0];
                    const jawLeft = landmarks.getJawOutline()[0];  // Rahang Kanan User (Kiri di Layar)
                    const jawRight = landmarks.getJawOutline()[16]; // Rahang Kiri User (Kanan di Layar)

                    const distToLeft = Math.abs(nose.x - jawLeft.x);
                    const distToRight = Math.abs(nose.x - jawRight.x);

                    // LOGIKA TENGOK KANAN
                    if (distToRight > (distToLeft * 2.0)) {
                        
                        statusText.innerText = "Posisi KANAN Oke! Tahan...";
                        statusText.classList.remove('bg-black', 'bg-red-500', 'bg-yellow-600');
                        statusText.classList.add('bg-green-600');
                        
                        speakText("Posisi Kanan Oke. Tahan.");

                        setTimeout(() => {
                            if (!isCaptured) { takeSnapshot(); }
                        }, 1200);

                    } else if (distToLeft > (distToRight * 2.0)) {
                        statusText.innerText = "Itu Kiri! Silahkan tengok Kanan.";
                        statusText.classList.remove('bg-green-600');
                        statusText.classList.add('bg-red-500');
                        
                        speakText("Itu kiri. Tolong tengok ke kanan.");
                    } else {
                         statusText.innerText = "Belum pas... Tengok Kanan lagi.";
                         statusText.classList.remove('bg-green-600', 'bg-red-500');
                         statusText.classList.add('bg-yellow-600');
                         
                         speakText("Belum pas. Tengok kanan lagi.");
                    }

                } else {
                    statusText.innerText = "Wajah tidak terdeteksi...";
                    statusText.classList.remove('bg-green-600', 'bg-yellow-600', 'bg-red-500');
                    statusText.classList.add('bg-black');
                    
                    lastSpokenText = "Wajah tidak terdeteksi...";
                }
            }, 350);
        });

        function takeSnapshot() {
            if(isCaptured) return;
            isCaptured = true;
            
            statusText.innerText = "Menyimpan...";
            speakText("Menyimpan foto kanan.");
            
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataURL = canvas.toDataURL('image/jpeg');

            // SIMPAN DATA KANAN
            localStorage.setItem('temp_foto_kanan', dataURL);

            // Redirect ke langkah selanjutnya (Halaman - Mulut)
            setTimeout(() => {
                window.location.href = "{{ route('pendaftaran.kamera-mulut') }}";
            }, 2000);
        }

        btnCapture.addEventListener('click', () => {
            takeSnapshot();
        });
    </script>
</body>
</html>