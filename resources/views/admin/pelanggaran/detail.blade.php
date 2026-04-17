<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelanggaran - Sistem Deteksi</title>
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
            <a href="{{ route('admin.pelanggaran') }}" class="flex items-center space-x-3 px-4 py-3 bg-white text-[#1f2937] rounded-l-full font-bold shadow-md transform translate-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Pelanggaran</span>
            </a>
            <a href="{{ route('admin.pelanggan') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-400 hover:text-white transition">
                <span class="font-medium">Kembali ke Pelanggan</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col bg-gray-50 overflow-hidden relative">
        <header class="h-20 flex items-center justify-between px-6 md:px-8 bg-white flex-shrink-0 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="md:hidden text-[#1f2937] hover:text-gray-600 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-2xl md:text-3xl font-bold text-[#1f2937] tracking-tight">Detail Pelanggaran</h2>
            </div>
        </header>

        <div class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 md:p-8 max-w-5xl mx-auto">
                
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-bold text-gray-800 border-b-2 border-[#1f2937] pb-2 inline-block">Informasi Insiden</h3>
                    <a href="{{ route('admin.pelanggaran') }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Pelanggar</label>
                            <div class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-800 font-medium">
                                {{ $pelanggaran->nama ?? $pelanggaran->status }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Telepon</label>
                            <div class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-800 font-medium">
                                {{ $pelanggaran->nomor_telepon ?? 'Tidak Tersedia' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Waktu Pelanggaran</label>
                            <div class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-800 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ \Carbon\Carbon::parse($pelanggaran->waktu)->format('d F Y - H:i:s') }}
                            </div>
                        </div>
                    </div>

                    <div class="h-full">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Bukti CCTV</label>
                        <div class="w-full h-full min-h-[250px] rounded-lg border border-gray-300 bg-gray-50 flex items-center justify-center p-2 overflow-hidden">
                            @if($pelanggaran->gambar_bukti)
                                <img src="{{ asset('storage/' . $pelanggaran->gambar_bukti) }}" alt="Bukti CCTV" class="max-h-64 object-contain rounded">
                            @else
                                <span class="text-gray-400 italic">Tidak ada bukti foto</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($pelanggaran->nama)
                <div class="mt-10">
                    <label class="block text-sm font-bold text-gray-700 mb-4 border-b pb-2 tracking-tight">Data Foto Wajah Terdaftar (Database)</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        
                        @php
                            $list_foto = [
                                ['label' => 'Lurus', 'file' => $pelanggaran->foto_lurus],
                                ['label' => 'Kiri', 'file' => $pelanggaran->foto_kiri],
                                ['label' => 'Kanan', 'file' => $pelanggaran->foto_kanan],
                                ['label' => 'Mulut', 'file' => $pelanggaran->foto_mulut],
                            ];
                        @endphp

                        @foreach($list_foto as $item)
                        <div class="border border-gray-200 rounded-xl p-3 bg-gray-50 shadow-sm flex flex-col">
                            <span class="block text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">{{ $item['label'] }}</span>
                            <div class="w-full aspect-video rounded-lg overflow-hidden bg-gray-200 border border-gray-100 flex items-center justify-center">
                                <img src="{{ asset('storage/wajah/' . $item['file']) }}" 
                                    alt="{{ $item['label'] }}" 
                                    class="max-w-full max-h-full object-contain shadow-inner">
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
                @endif

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