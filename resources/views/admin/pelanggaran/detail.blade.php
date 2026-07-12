<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Detail Pelanggaran - Sistem Deteksi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>

    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
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
        <header class="h-20 flex items-center justify-between px-4 sm:px-6 md:px-8 bg-white flex-shrink-0 border-b border-gray-200">
            <div class="flex items-center gap-3 sm:gap-4">
                <button id="mobile-menu-btn" class="md:hidden text-[#1f2937] hover:text-gray-600 focus:outline-none">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1f2937] tracking-tight">Detail Pelanggaran</h2>
            </div>
        </header>

        <div class="flex-1 p-4 sm:p-6 md:p-8 overflow-y-auto">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sm:p-6 md:p-8 max-w-5xl mx-auto">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4 sm:gap-0">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 border-b-2 border-[#1f2937] pb-2 inline-block">Informasi Insiden</h3>
                    <a href="{{ route('admin.pelanggaran') }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition flex items-center self-start sm:self-auto shadow-sm border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-4 sm:space-y-6">
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ \Carbon\Carbon::parse($pelanggaran->waktu)->format('d F Y - H:i:s') }}
                            </div>
                        </div>

                        @if(isset($pelanggaran->pelanggan_id) && $pelanggaran->pelanggan_id != null)
                            @php
                                // PERBAIKAN: Hanya menghitung pelanggaran yang berstatus selain 'lunas' agar nilai denda reset setelah dibayar
                                $jumlahPelanggaran = \Illuminate\Support\Facades\DB::table('history_pelanggarans')
                                    ->where('pelanggan_id', $pelanggaran->pelanggan_id)
                                    ->where('status_pembayaran', '!=', 'lunas')
                                    ->count();
                                
                                $jumlahPelanggaran = $jumlahPelanggaran > 0 ? $jumlahPelanggaran : 1;
                                
                                if ($pelanggaran->status_pembayaran === 'lunas') {
                                    $denda = 0;
                                } else {
                                    $denda = $jumlahPelanggaran * 50000;
                                }
                            @endphp
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Denda Pelanggaran</label>
                                <div class="w-full px-4 py-3 rounded-lg border {{ $pelanggaran->status_pembayaran === 'lunas' ? 'border-green-300 bg-green-50 text-green-700' : 'border-red-300 bg-red-50 text-red-700' }} font-bold flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0">
                                    <div class="flex items-center text-base sm:text-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Rp {{ number_format($denda, 0, ',', '.') }}
                                    </div>
                                    <span class="text-xs sm:text-sm font-semibold {{ $pelanggaran->status_pembayaran === 'lunas' ? 'text-green-800 bg-green-200' : 'text-red-800 bg-red-200' }} px-3 py-1.5 rounded-full w-fit">
                                        Pelanggaran ke-{{ $jumlahPelanggaran }}
                                    </span>
                                </div>
                                
                                @if($pelanggaran->status_pembayaran === 'lunas')
                                    <div class="mt-3 w-full sm:w-auto px-6 py-2.5 bg-green-100 text-green-800 font-bold rounded-lg border border-green-300 flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Denda Sudah Lunas
                                    </div>
                                @else
                                    <button id="pay-button" 
                                            data-pelanggan-id="{{ $pelanggaran->pelanggan_id }}"
                                            data-denda="{{ $denda }}" 
                                            data-nama="{{ $pelanggaran->nama ?? 'Anonim' }}" 
                                            data-telepon="{{ $pelanggaran->nomor_telepon ?? '00000000' }}"
                                            class="mt-3 w-full sm:w-auto px-6 py-2.5 bg-[#0055b8] hover:bg-[#004291] text-white font-bold rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Bayar Denda Sekarang
                                    </button>
                                @endif
                                </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Sumber Kamera</label>
                            <div class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-800 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                @if(isset($pelanggaran->kamera) && $pelanggaran->kamera)
                                    CCTV Node {{ $pelanggaran->kamera }}
                                @else
                                    CCTV Utama (Default)
                                @endif
                            </div>
                        </div>

                        @if(isset($pelanggaran->similarity_score) && !is_null($pelanggaran->similarity_score))
                        @php
                            $simPercent = round($pelanggaran->similarity_score * 100, 1);
                            $simColor = $simPercent >= 60 ? 'bg-green-100 text-green-800 border-green-300' : ($simPercent >= 40 ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : 'bg-red-100 text-red-800 border-red-300');
                        @endphp
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Akurasi Pengenalan AI</label>
                            <div class="w-full px-4 py-3 rounded-lg border {{ $simColor }} font-bold flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex items-center text-base sm:text-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Cosine Similarity: {{ $simPercent }}%
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                    @if(!is_null($pelanggaran->match_margin))
                                        <span class="bg-white px-2 py-1 rounded border border-gray-300">Margin: {{ round($pelanggaran->match_margin, 3) }}</span>
                                    @endif
                                    @if($pelanggaran->lighting_condition)
                                        <span class="bg-white px-2 py-1 rounded border border-gray-300 uppercase">Cahaya: {{ $pelanggaran->lighting_condition }}</span>
                                    @endif
                                    @if($pelanggaran->vote_count && $pelanggaran->total_frames)
                                        <span class="bg-white px-2 py-1 rounded border border-gray-300">Frame: {{ $pelanggaran->vote_count }}/{{ $pelanggaran->total_frames }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(!empty($topCandidates))
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Top-3 Kandidat Terdekat</label>
                            <div class="w-full rounded-lg border border-gray-300 bg-gray-50 divide-y divide-gray-200">
                                @foreach($topCandidates as $idx => $cand)
                                    <div class="px-4 py-2 flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">
                                            <span class="inline-block w-6 h-6 rounded-full bg-gray-200 text-gray-700 text-xs font-bold text-center leading-6 mr-2">{{ $idx + 1 }}</span>
                                            {{ $cand['visitor_id'] ?? 'Unknown' }}
                                        </span>
                                        <span class="text-sm font-bold {{ $idx === 0 ? 'text-green-700' : 'text-gray-600' }}">
                                            {{ isset($cand['similarity']) ? round($cand['similarity'] * 100, 1) : 0 }}%
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endif
                        </div>

                    <div class="h-full flex flex-col mt-2 md:mt-0">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Bukti CCTV</label>
                        <div class="w-full h-full min-h-[200px] sm:min-h-[250px] md:min-h-[300px] rounded-lg border border-gray-300 bg-gray-50 flex items-center justify-center p-2 overflow-hidden shadow-sm">
                            @if($pelanggaran->gambar_bukti)
                                <img src="{{ asset('storage/' . $pelanggaran->gambar_bukti) }}" alt="Bukti CCTV" class="max-w-full max-h-64 sm:max-h-72 md:max-h-full object-contain rounded">
                            @else
                                <span class="text-gray-400 italic">Tidak ada bukti foto</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if(isset($pelanggaran->nama))
                <div class="mt-8 sm:mt-10">
                    <label class="block text-sm font-bold text-gray-700 mb-4 border-b pb-2 tracking-tight">Data Foto Wajah Terdaftar (Database)</label>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                        
                        @php
                            $list_foto = [
                                ['label' => 'Lurus', 'file' => $pelanggaran->foto_lurus ?? null],
                                ['label' => 'Kiri', 'file' => $pelanggaran->foto_kiri ?? null],
                                ['label' => 'Kanan', 'file' => $pelanggaran->foto_kanan ?? null],
                                ['label' => 'Mulut', 'file' => $pelanggaran->foto_mulut ?? null],
                                ['label' => 'Menunduk', 'file' => $pelanggaran->foto_menunduk ?? null],
                            ];
                        @endphp

                        @foreach($list_foto as $item)
                        <div class="border border-gray-200 rounded-xl p-2 sm:p-3 bg-gray-50 shadow-sm flex flex-col h-full">
                            <span class="block text-center text-[9px] sm:text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2 sm:mb-3">{{ $item['label'] }}</span>
                            <div class="w-full aspect-[3/4] rounded-lg overflow-hidden bg-[#242e3a] border border-gray-600 flex items-center justify-center shadow-inner relative group">
                                @if($item['file'])
                                    <img src="{{ asset('storage/wajah/' . $item['file']) }}" 
                                         alt="{{ $item['label'] }}" 
                                         class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:opacity-100 transition duration-300">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white opacity-80" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                                @endif
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

        const payButton = document.getElementById('pay-button');
        let isProcessing = false; 

        if (payButton) {
            payButton.addEventListener('click', async function () {
                if (isProcessing) return; 
                isProcessing = true; 
                
                const originalContent = this.innerHTML;
                this.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
                this.disabled = true;

                const pelangganIdValue = this.getAttribute('data-pelanggan-id');
                const dendaValue = this.getAttribute('data-denda');
                const namaValue = this.getAttribute('data-nama');
                const teleponValue = this.getAttribute('data-telepon');

                if (parseInt(dendaValue) <= 0) {
                    alert("Nominal denda tidak valid atau sudah lunas.");
                    isProcessing = false;
                    this.innerHTML = originalContent;
                    this.disabled = false;
                    return;
                }

                let snapToken = null; 

                try {
                    const response = await fetch('/api/midtrans/get-token', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            pelanggan_id: pelangganIdValue,
                            denda: dendaValue,
                            nama: namaValue,
                            telepon: teleponValue
                        })
                    });

                    const data = await response.json();

                    if (data.token) {
                        snapToken = data.token; 
                    } else {
                        isProcessing = false;
                        this.innerHTML = originalContent;
                        this.disabled = false;
                        alert('Gagal mendapatkan token: ' + (data.message || 'Error tidak diketahui'));
                        return; 
                    }
                } catch (error) {
                    isProcessing = false;
                    this.innerHTML = originalContent;
                    this.disabled = false;
                    alert('Gagal terhubung ke sistem pembayaran.');
                    return; 
                }

                if (snapToken) {
                    try {
                        window.snap.pay(snapToken, {
                            onSuccess: function(result) {
                                const payBtn = document.getElementById('pay-button');
                                if(payBtn) payBtn.innerHTML = 'Memperbarui database...';

                                fetch('/api/midtrans/update-lunas', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        order_id: result.order_id
                                    })
                                })
                                .then(response => response.json())
                                .then(updateData => {
                                    alert("Pembayaran Berhasil! Denda telah lunas.");
                                    window.location.reload(); 
                                })
                                .catch(error => {
                                    console.error('Error update DB:', error);
                                    alert("Pembayaran berhasil, tapi gagal update database. Lapor admin.");
                                    isProcessing = false;
                                    payButton.innerHTML = originalContent;
                                    payButton.disabled = false;
                                });
                            },
                            onPending: function(result) {
                                const payBtn = document.getElementById('pay-button');
                                if(payBtn) payBtn.innerHTML = 'Memperbarui database (Demo VA)...';

                                fetch('/api/midtrans/update-lunas', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        order_id: result.order_id
                                    })
                                })
                                .then(response => response.json())
                                .then(updateData => {
                                    alert("VA Dibuat! (Simulasi Demo: Denda langsung dianggap Lunas).");
                                    window.location.reload(); 
                                })
                                .catch(error => {
                                    console.error('Error update DB:', error);
                                    alert("Gagal update database.");
                                    isProcessing = false;
                                    payButton.innerHTML = originalContent;
                                    payButton.disabled = false;
                                });
                            },
                            onError: function(result) {
                                isProcessing = false;
                                payButton.innerHTML = originalContent;
                                payButton.disabled = false;
                                alert("Pembayaran Gagal.");
                            },
                            onClose: function() {
                                isProcessing = false;
                                payButton.innerHTML = originalContent;
                                payButton.disabled = false;
                                alert("Pembayaran dibatalkan.");
                            }
                        });
                    } catch (snapError) {
                        console.warn('Peringatan internal Midtrans (CSP):', snapError);
                    }
                }
            });
        }
    </script>

</body>
</html>