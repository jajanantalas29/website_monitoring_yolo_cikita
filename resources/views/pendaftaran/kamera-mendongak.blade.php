<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ambil Foto Mendongak</title>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>
    @vite('resources/css/app.css')
    <style> video { transform: scaleX(-1); } </style>
</head>
<body class="bg-[#1f2937] min-h-screen flex items-center justify-center p-4">
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
                    Memuat AI...
                </div>
            </div>
            <div class="mt-6 text-center">
                <p class="text-[#1f2937] text-base sm:text-xl font-bold tracking-tight px-2">
                    Pastikan wajah anda sedikit ke atas
                </p>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 w-full">
            <a href="{{ route('pendaftaran.form') }}" class="w-full sm:w-auto border-2 border-white/40 text-white text-center font-bold py-3.5 sm:py-2.5 px-10 rounded-xl hover:bg-white/10 hover:border-white transition duration-300 text-sm sm:text-base flex items-center justify-center">
                Batal
            </a>
            <button id="btn-capture" class="w-full sm:w-auto bg-white text-[#1f2937] font-bold py-3.5 sm:py-2.5 px-10 rounded-xl shadow-xl hover:bg-gray-200 transition duration-300 text-sm sm:text-base flex items-center justify-center">
                Simpan (Manual)
            </button>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const statusText = document.getElementById('status-text');
        const btnCapture = document.getElementById('btn-capture');
        let capturedPhotos = []; 
        const MAX_PHOTOS = 20;
        let isCaptured = false;
        let lastSpokenText = "";

        // Fungsi Suara
        function speakText(text) {
            if ('speechSynthesis' in window && lastSpokenText !== text) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                window.speechSynthesis.speak(utterance);
                lastSpokenText = text;
            }
        }

        // Aktifkan suara dengan interaksi klik
        btnCapture.addEventListener('click', () => {
            window.speechSynthesis.resume();
            speakText("Mulai proses.");
        });

        localStorage.removeItem('temp_up');

        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
        ]).then(startVideo);

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                .then(stream => { video.srcObject = stream; })
                .catch(err => { statusText.innerText = "Kamera Error"; });
        }

        video.addEventListener('play', () => {
            speakText("Silahkan dongakkan wajah ke atas."); // Instruksi awal
            setInterval(async () => {
                if (isCaptured) return;
                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();
                
                if (detections.length > 0) {
                    const landmarks = detections[0].landmarks;
                    const leftEye = landmarks.positions[36];
                    const rightEye = landmarks.positions[45];
                    const noseTip = landmarks.positions[30];
                    const eyeCenterY = (leftEye.y + rightEye.y) / 2;
                    
                    if (noseTip.y > eyeCenterY + 10) { 
                        statusText.innerText = `Mengambil: ${capturedPhotos.length + 1}/${MAX_PHOTOS}`;
                        takeSnapshot();
                    } else {
                        statusText.innerText = "Dongakkan wajah ke atas...";
                        speakText("Dongakkan wajah ke atas.");
                    }
                } else {
                    statusText.innerText = "Wajah tidak terdeteksi...";
                }
            }, 300);
        });

        function takeSnapshot() {
            if(isCaptured || capturedPhotos.length >= MAX_PHOTOS) return;
            canvas.width = 300; 
            canvas.height = 225;
            const context = canvas.getContext('2d');
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            capturedPhotos.push(canvas.toDataURL('image/jpeg', 0.5));

            if (capturedPhotos.length >= MAX_PHOTOS) {
                isCaptured = true;
                localStorage.setItem('temp_up', JSON.stringify(capturedPhotos));
                
                // Suara saat selesai
                const endUtterance = new SpeechSynthesisUtterance("Foto mendongak selesai.");
                endUtterance.lang = 'id-ID';
                endUtterance.onend = () => {
                    window.location.href = "{{ route('pendaftaran.kamera-menunduk') }}";
                };
                window.speechSynthesis.speak(endUtterance);
            }
        }
    </script>
</body>
</html>