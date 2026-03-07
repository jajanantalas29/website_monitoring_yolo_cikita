<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#1f2937] antialiased h-screen flex items-center justify-center p-4 overflow-hidden">

    <div class="bg-white rounded-[2rem] shadow-2xl p-10 w-full max-w-md flex flex-col items-center justify-center text-center min-h-[300px]">
        
        <div class="mb-6">
            <img src="{{ asset('icon/icon_kartu_berhasil_diproses.png') }}" alt="Berhasil" class="w-32 h-32 object-contain">
        </div>

        <h2 class="text-[#1f2937] text-2xl font-bold tracking-tight px-4 leading-snug">
            Selamat kartu anda sudah dapat digunakan
        </h2>

    </div>

    <script>
        setTimeout(function() {
            window.location.href = "{{ route('pendaftaran.index') }}"; 
        }, 3000); 
    </script>

</body>
</html>