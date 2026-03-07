<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Daftar Kartu - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white h-screen flex overflow-hidden">

    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden transition-opacity"></div>

    <aside id="sidebar" class="w-64 bg-[#1f2937] text-white flex-col flex-shrink-0 transition-transform duration-300 fixed md:relative z-50 h-full transform -translate-x-full md:translate-x-0 flex">
        <div class="h-24 flex flex-col items-center justify-center border-b border-gray-700 mb-6 mt-4 md:mt-0">
            <div class="bg-white p-2 rounded-lg mb-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#1f2937" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75z" /></svg>
            </div>
            <h1 class="text-lg font-bold tracking-wider">Sistem Deteksi</h1>
        </div>

        <nav class="flex-1 px-4 space-y-2">
            <a href="{{ route('admin.pelanggan') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" /></svg>
                <span class="font-medium">Pelanggan</span>
            </a>
            <a href="{{ route('admin.status') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                <span class="font-medium">Status</span>
            </a>
            <a href="{{ route('admin.pelanggaran') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span class="font-medium">Pelanggaran</span>
            </a>
            <a href="{{ route('admin.kartu') }}" class="flex items-center space-x-3 px-4 py-3 bg-white text-[#1f2937] rounded-l-full font-bold shadow-md transform translate-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                <span>Daftar Kartu</span>
            </a>
        </nav>

        <div class="p-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-2 text-gray-400 hover:text-white transition w-full text-left">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span class="font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden w-full bg-white relative">
        <header class="h-20 flex items-center justify-between px-6 md:px-8 bg-white shrink-0">
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="md:hidden text-[#1f2937] hover:text-gray-600 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-2xl md:text-3xl font-bold text-[#1f2937] tracking-tight">Detail Daftar Kartu</h2>
            </div>
            <div class="text-[#1f2937]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" /></svg>
            </div>
        </header>

        <div class="flex-1 p-4 md:p-8 pt-2 overflow-y-auto">
            <div class="border-2 border-gray-300 rounded-2xl p-4 md:p-8">
                <div class="mb-5">
                    <label class="block font-bold text-lg md:text-xl text-[#1f2937] mb-2">Nama</label>
                    <input type="text" value="{{ $kartu->pelanggan->nama_lengkap ?? 'Pelanggan telah dihapus' }}" readonly class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 bg-gray-50 font-medium outline-none">
                </div>

                <div class="mb-5">
                    <label class="block font-bold text-lg md:text-xl text-[#1f2937] mb-2">UID Kartu</label>
                    <input type="text" value="{{ $kartu->uid_kartu }}" readonly class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 bg-gray-50 font-medium outline-none">
                </div>

                <div class="mb-5">
                    <label class="block font-bold text-lg md:text-xl text-[#1f2937] mb-2">Nomor Telepon</label>
                    <input type="text" value="{{ $kartu->pelanggan->nomor_telepon ?? 'Tidak ada data' }}" readonly class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 bg-gray-50 font-medium outline-none">
                </div>

                <div class="mb-8">
                    <label class="block font-bold text-lg md:text-xl text-[#1f2937] mb-2">Foto Wajah</label>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 border-2 border-gray-300 rounded-xl p-3 md:p-5">
                        @php
                            $fotos = [
                                $kartu->pelanggan->foto_lurus ?? null,
                                $kartu->pelanggan->foto_kiri ?? null,
                                $kartu->pelanggan->foto_kanan ?? null,
                                $kartu->pelanggan->foto_mulut ?? null,
                            ];
                        @endphp

                        @foreach($fotos as $foto)
                            <div class="bg-[#242e3a] rounded-xl aspect-[3/4] flex items-center justify-center overflow-hidden border border-gray-600 shadow-inner relative">
                                @if($foto)
                                    <img src="{{ asset('storage/wajah/' . $foto) }}" class="w-full h-full object-cover">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-12 h-12 md:w-16 md:h-16 opacity-90"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" /></svg>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('admin.kartu') }}" class="border-2 border-gray-300 text-gray-700 font-semibold text-base md:text-lg py-2 px-6 md:px-8 rounded-lg hover:bg-gray-100 transition shadow-sm">
                        Kembali
                    </a>
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