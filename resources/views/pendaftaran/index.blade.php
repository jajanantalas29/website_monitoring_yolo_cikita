<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefit Sistem - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    @vite('resources/css/app.css')
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            /* Mengganti background dengan gambar */
            background-image: url("{{ asset('img/bg_welcome.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="antialiased h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-xs sm:max-w-sm flex flex-col items-center relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-gray-800 to-gray-600"></div>

        <div class="rounded-full p-5 mb-6 transform transition hover:scale-110 duration-500">
            <img src="{{ asset('icon/icon_terompet.png') }}" alt="Icon Terompet" class="w-20 h-20 object-contain">
        </div>

        <h1 class="text-[#1f2937] text-2xl font-bold mb-6 tracking-tight text-center">
            Benefit Dari Sistem Ini
        </h1>

        <div class="w-full px-4 mb-0">
            <ol class="list-inside space-y-3 text-gray-700 text-sm font-medium text-left">
                
                <li class="flex items-start">
                    <span class="mr-2 font-bold text-[#1f2937]">1.</span>
                    <span class="leading-tight">Silahkan klik tombol daftar untuk melakukan pendaftaran</span>
                </li>
                
                <li class="flex items-start">
                    <span class="mr-2 font-bold text-[#1f2937]">2.</span>
                    <span class="leading-tight">Setelah pendaftaran, jangan lupa mengambil kartu di bagian admin</span>
                </li>
                
                <li class="flex items-start">
                    <span class="mr-2 font-bold text-[#1f2937]">3.</span>
                    <span class="leading-tight">Dapatkan kartunya segera</span>
                </li>

            </ol>
        </div>

        <a href="{{ route('pendaftaran.form') }}" 
           class="mt-10 w-full block text-center bg-[#1f2937] text-white font-bold py-3.5 px-8 rounded-full shadow-lg hover:bg-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 ease-in-out">
            Daftar Sekarang
        </a>

    </div>

</body>
</html>