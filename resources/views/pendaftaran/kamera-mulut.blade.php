<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Foto Mulut - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>

    @vite('resources/css/app.css')
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        video { transform: scaleX(-1); } 
    </style>
</head>
<body class="bg-[#1f2937] antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl flex flex-col">
        
        <div class="bg-white rounded-[1.5rem] shadow-2xl p-4 sm:p-6 w-full mb-6">
            
            <div class="relative w-full aspect-video border-2 border-gray-500 rounded-2xl flex items-center justify-center bg-black overflow-hidden">
                <video id="video" class="absolute inset-0 w-full h-full object-cover" autoplay muted playsinline></video>
                <canvas id="canvas" class="hidden"></canvas>

                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="w-24 h-24 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5V6a3 3 0 013-3h1.5M16.5 3H18a3 3 0 013 3v1.5M21 16.5V18a3 3 0 01-3 3h-1.5M7.5 21H6a3 3 0 01-3-3v-1.5" stroke-width="1" class="opacity-80"/>
                    </svg>
                </div>

                <div id="status-text" class="absolute bottom-4 bg-black bg-opacity-70 text-white px-4 py-1 rounded-full text-sm font-medium z-20 transition-all duration-300">
                    Memuat AI Landmarks...
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-[#1f2937] text-lg sm:text-xl font-bold tracking-tight">
                    Pastikan mulut anda terbuka
                </p>
            </div>
        </div>

        <div class="flex justify-end space-x-4 w-full">
            <a href="{{ route('pendaftaran.form') }}" class="bg-white text-[#1f2937] font-bold py-2.5 px-10 rounded-xl shadow-xl hover:bg-gray-100 transition duration-300 text-sm">
                Batal
            </a>
            
            <button id="btn-capture" class="bg-white text-[#1f2937] font-bold py-2.5 px-10 rounded-xl shadow-xl hover:bg-gray-100 transition duration-300 text-sm">
                Simpan
            </button>
        </div>

    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const statusText = document.getElementById('status-text');
        const btnCapture = document.getElementById('btn-capture');
        let isCaptured = false;

        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('https://justadudewhohacks.github.io/face-api.js/models'),
        ]).then(startVideo).catch(err => {
            console.error(err);
            statusText.innerText = "Gagal Load AI.";
            statusText.classList.add('bg-red-500');
        });

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => {
                    video.srcObject = stream;
                    statusText.innerText = "Silahkan BUKA MULUT anda...";
                })
                .catch(err => {
                    statusText.innerText = "Kamera Error";
                });
        }

        video.addEventListener('play', () => {
            setInterval(async () => {
                if (isCaptured) return;

                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();

                if (detections.length > 0) {
                    const landmarks = detections[0].landmarks;
                    
                    // Titik Bibir Dalam
                    // Point 62: Bibir Atas Tengah (Bawahnya bibir atas)
                    // Point 66: Bibir Bawah Tengah (Atasnya bibir bawah)
                    const mouthTop = landmarks.positions[62];
                    const mouthBottom = landmarks.positions[66];

                    // Hitung Jarak Vertikal
                    const mouthOpening = Math.abs(mouthBottom.y - mouthTop.y);

                    // Debugging: Lihat nilai bukaan mulut di console
                    // console.log("Mouth Opening:", mouthOpening);

                    // Threshold: Jika jarak > 15 pixel (bisa disesuaikan), dianggap terbuka
                    if (mouthOpening > 15) { 
                        
                        statusText.innerText = "Mulut Terbuka! Tahan...";
                        statusText.classList.remove('bg-black', 'bg-red-500');
                        statusText.classList.add('bg-green-600');

                        setTimeout(() => {
                            if (!isCaptured) {
                                takeSnapshot();
                            }
                        }, 800);

                    } else {
                         statusText.innerText = "Buka mulut lebih lebar...";
                         statusText.classList.remove('bg-green-600');
                         statusText.classList.add('bg-black');
                    }

                } else {
                    statusText.innerText = "Wajah tidak terdeteksi...";
                    statusText.classList.add('bg-black');
                }
            }, 200);
        });

        function takeSnapshot() {
            if(isCaptured) return;
            isCaptured = true;
            statusText.innerText = "Menyimpan Data...";
            
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataURL = canvas.toDataURL('image/jpeg');

            // SIMPAN DATA MULUT
            localStorage.setItem('temp_foto_mulut', dataURL);

            // SELESAI -> KEMBALI KE FORM UTAMA
            setTimeout(() => {
                window.location.href = "{{ route('pendaftaran.form') }}";
            }, 500);
        }

        btnCapture.addEventListener('click', () => {
            takeSnapshot();
        });
    </script>
</body>
</html>