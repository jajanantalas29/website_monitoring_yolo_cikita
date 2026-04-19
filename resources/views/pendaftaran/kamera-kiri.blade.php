<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Foto Kiri - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>

    @vite('resources/css/app.css')
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Mirror effect agar pengalaman pengguna natural seperti bercermin */
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
                         <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </div>

                <div id="status-text" class="absolute bottom-4 bg-black bg-opacity-70 text-white px-4 py-1.5 rounded-full text-xs sm:text-sm font-medium z-20 transition-all duration-300">
                    Memuat AI Landmarks...
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-[#1f2937] text-base sm:text-xl font-bold tracking-tight px-2">
                    Pastikan wajah anda menengok ke kiri
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

        // Load Model (Detector + Landmarks)
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
        ]).then(startVideo).catch(err => {
            console.error(err);
            statusText.innerText = "Gagal Load AI. Cek Koneksi Internet.";
            statusText.classList.add('bg-red-500');
            speakText("Gagal memuat sistem pelacakan wajah.");
        });

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                .then(stream => {
                    video.srcObject = stream;
                    statusText.innerText = "Silahkan tengok ke KIRI...";
                })
                .catch(err => {
                    statusText.innerText = "Kamera Error";
                    speakText("Izin kamera ditolak.");
                });
        }

        // Deteksi Arah Wajah
        video.addEventListener('play', () => {
            // Sapaan dan Instruksi Awal
            setTimeout(() => { speakText("Bagus. Sekarang silakan menengok sedikit ke kiri."); }, 1000);

            setInterval(async () => {
                if (isCaptured) return;

                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();

                if (detections.length > 0) {
                    const landmarks = detections[0].landmarks;
                    
                    const nose = landmarks.getNose()[0]; 
                    const jawLeft = landmarks.getJawOutline()[0];
                    const jawRight = landmarks.getJawOutline()[16];

                    const distToLeft = Math.abs(nose.x - jawLeft.x);
                    const distToRight = Math.abs(nose.x - jawRight.x);

                    // LOGIKA KIRI
                    if (distToLeft > (distToRight * 2.0)) { 
                        
                        statusText.innerText = "Posisi KIRI Oke! Tahan...";
                        statusText.classList.remove('bg-black', 'bg-yellow-600', 'bg-red-500');
                        statusText.classList.add('bg-green-600');
                        
                        speakText("Posisi Kiri Oke. Tahan.");

                        setTimeout(() => {
                            if (!isCaptured) { takeSnapshot(); }
                        }, 1200);

                    } else if (distToRight > (distToLeft * 2.0)) {
                        statusText.innerText = "Itu Kanan! Silahkan tengok Kiri.";
                        statusText.classList.remove('bg-green-600', 'bg-yellow-600', 'bg-black');
                        statusText.classList.add('bg-red-500');
                        
                        speakText("Itu kanan. Tolong tengok ke kiri.");
                    } else {
                         statusText.innerText = "Belum pas... Tengok Kiri lagi.";
                         statusText.classList.remove('bg-green-600', 'bg-red-500', 'bg-black');
                         statusText.classList.add('bg-yellow-600');
                         
                         speakText("Belum pas. Tengok kiri lagi.");
                    }

                } else {
                    statusText.innerText = "Wajah tidak terdeteksi...";
                    statusText.classList.remove('bg-green-600', 'bg-yellow-600', 'bg-red-500');
                    statusText.classList.add('bg-black');
                    
                    lastSpokenText = "Wajah tidak terdeteksi...";
                }
            }, 350); // Sedikit lebih lambat dari lurus (350ms) karena butuh menghitung koordinat rahang
        });

        function takeSnapshot() {
            if(isCaptured) return;
            isCaptured = true;
            
            statusText.innerText = "Menyimpan...";
            speakText("Menyimpan foto kiri.");
            
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataURL = canvas.toDataURL('image/jpeg');
            localStorage.setItem('temp_foto_kiri', dataURL);

            // Redirect ke langkah selanjutnya (Kamera Kanan)
            setTimeout(() => {
                window.location.href = "{{ route('pendaftaran.kamera-kanan') }}";
            }, 2000);
        }

        btnCapture.addEventListener('click', () => {
            takeSnapshot();
        });
    </script>
</body>
</html>