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
        /* Animasi berdenyut halus untuk icon (opsional, biar terlihat 'loading') */
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

    </div>

    <script>
        setTimeout(function() {
            // Arahkan ke route 'pendaftaran.berhasil' setelah 3 detik
            window.location.href = "{{ route('pendaftaran.berhasil') }}";
        }, 3000); 
    </script>

</body>
</html>