<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    @vite('resources/css/app.css')
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            /* Menambahkan Background Gambar */
            background-image: url("{{ asset('img/bg_welcome.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="antialiased h-screen overflow-hidden">

    <a href="{{ route('pendaftaran.index') }}" class="flex items-center justify-center h-full w-full p-4 cursor-pointer hover:bg-opacity-95 transition duration-300">

        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-xs sm:max-w-sm aspect-square flex flex-col items-center justify-center text-center transform hover:scale-105 transition duration-300 ease-in-out">
            
            <div class="rounded-full p-5 mb-6 flex items-center justify-center">
                <img src="{{ asset('icon/icon_tangan.png') }}" alt="Icon Tangan" class="w-20 h-20 object-contain">
            </div>

            <h1 class="text-[#1f2937] text-3xl font-bold mb-2 tracking-tight">
                Selamat Datang
            </h1>

            <p class="text-gray-500 text-sm px-4 leading-relaxed">
                Silahkan klik dimana saja untuk melakukan pendaftaran
            </p>
        </div>
    </a>

</body>
</html>