<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelanggaran - Sistem Deteksi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white h-screen flex overflow-hidden relative">

    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden transition-opacity"></div>

    <aside id="sidebar" class="w-64 bg-[#1f2937] text-white flex-col flex-shrink-0 transition-transform duration-300 fixed md:relative z-50 h-full transform -translate-x-full md:translate-x-0 flex">
        <div class="h-24 flex flex-col items-center justify-center border-b border-gray-700 mb-6 mt-4 md:mt-0">
            <div class="bg-white p-2 rounded-lg mb-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#1f2937" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75z" />
                </svg>
            </div>
            <h1 class="text-lg font-bold tracking-wider">Sistem Deteksi</h1>
        </div>

        <nav class="flex-1 px-4 space-y-2 mt-2">
            <a href="{{ route('admin.pelanggan') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                </svg>
                <span class="font-medium">Pelanggan</span>
            </a>

            <a href="{{ route('admin.status') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span class="font-medium">Status</span>
            </a>

            <a href="{{ route('admin.pelanggaran') }}" class="flex items-center space-x-3 px-4 py-3 bg-white text-[#1f2937] rounded-l-full font-bold shadow-md transform translate-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Pelanggaran</span>
            </a>

            <a href="{{ route('admin.kartu') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                </svg>
                <span class="font-medium">Daftar Kartu</span>
            </a>
        </nav>

        <div class="p-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-2 text-gray-400 hover:text-white transition w-full text-left">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col bg-white overflow-hidden relative">
        
        <header class="h-20 flex items-center justify-between px-6 md:px-8 bg-white flex-shrink-0">
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="md:hidden text-[#1f2937] hover:text-gray-600 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-2xl md:text-3xl font-bold text-[#1f2937] tracking-tight">Pelanggaran</h2>
            </div>

            <div class="text-[#1f2937]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                </svg>
            </div>
        </header>

        <div class="flex-1 p-4 md:p-8 pt-2 overflow-y-auto">
            
            <div class="border-2 border-gray-300 rounded-2xl h-full p-1 overflow-hidden shadow-sm flex flex-col bg-white">
                <div class="w-full h-full overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px] md:min-w-full">
                        <thead class="bg-white sticky top-0 z-10 border-b-2 border-gray-300">
                            <tr>
                                <th class="p-4 text-base md:text-lg font-bold text-[#1f2937]">Nama</th>
                                <th class="p-4 text-base md:text-lg font-bold text-[#1f2937]">Nomor Telepon</th>
                                <th class="p-4 text-base md:text-lg font-bold text-[#1f2937]">Foto Wajah</th>
                                <th class="p-4 text-base md:text-lg font-bold text-[#1f2937] text-center">Lainnya</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($pelanggaran as $p)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="p-4 py-4 md:py-6 font-medium">{{ $p->nama }}</td>
                                <td class="p-4 py-4 md:py-6">{{ $p->telepon }}</td>
                                <td class="p-4 py-4 md:py-6 text-gray-500 text-xs md:text-sm italic">
                                    {{ $p->foto }}
                                </td>
                                <td class="p-4 py-4 md:py-6 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        
                                        <button class="text-gray-600 hover:text-black transition" title="Lihat">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">Belum ada data pelanggaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');

        btn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
</html>