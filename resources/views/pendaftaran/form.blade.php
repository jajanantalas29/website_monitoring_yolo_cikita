<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite('resources/css/app.css')
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input::placeholder { color: #9ca3af; font-size: 0.875rem; }
    </style>
</head>
<body class="bg-[#1f2937] antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg flex flex-col">
        
        <div class="bg-white rounded-[2rem] shadow-2xl p-6 sm:p-10 w-full mb-6">
            
            <div class="mb-8">
                <h1 class="text-[#1f2937] text-3xl sm:text-4xl font-black tracking-tighter mb-2">Pendaftaran</h1>
                <p class="text-gray-400 text-[13px] mb-4 leading-tight">Silahkan lakukan pendaftaran untuk dapat mendapatkan benefit dari sistem ini..</p>
                <div class="w-full border-b border-gray-200"></div>
            </div>

            @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-red-500 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold tracking-tight">Pendaftaran Gagal!</p>
                        <ul class="text-sm mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('pendaftaran.store') }}" method="POST" id="registrationForm" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-lg sm:text-xl tracking-tight">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-input w-full border border-gray-400 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-gray-200 transition text-gray-800" placeholder="Masukan nama lengkap anda..">
                </div>

                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-lg sm:text-xl tracking-tight">Nomor Telepon</label>
                    <input type="number" name="nomor_telepon" class="form-input w-full border border-gray-400 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-gray-200 transition text-gray-800" placeholder="Masukan nomor telepon anda..">
                </div>

                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-lg sm:text-xl tracking-tight">Foto Wajah</label>
                    
                    <input type="hidden" name="foto_lurus" id="input_foto_lurus">
                    <input type="hidden" name="foto_kiri" id="input_foto_kiri">
                    <input type="hidden" name="foto_kanan" id="input_foto_kanan">
                    <input type="hidden" name="foto_mulut" id="input_foto_mulut">
                    <input type="hidden" name="foto_menunduk" id="input_foto_menunduk"> 
                    
                    <div class="relative w-full border-2 border-gray-400 rounded-2xl overflow-hidden hover:bg-gray-50 transition-all">
                        
                        <a href="{{ route('pendaftaran.kamera') }}" id="btn-start-camera" class="flex flex-col items-center justify-center w-full h-60 cursor-pointer group">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="bg-[#1f2937] rounded-2xl p-4 mb-4 shadow-lg transform transition group-hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 text-white">
                                        <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-sm text-[#1f2937] font-bold tracking-wide">Ambil 5 Pose Wajah</p>
                            </div>
                        </a>

                        <div id="photo-grid" class="hidden grid-cols-3 gap-2 p-2 bg-gray-100">
                            
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden shadow-sm">
                                <img id="img-lurus" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[9px] sm:text-[10px] bg-black/60 text-white px-1 rounded">Lurus</span>
                            </div>
                            
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden shadow-sm">
                                <img id="img-kiri" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[9px] sm:text-[10px] bg-black/60 text-white px-1 rounded">Kiri</span>
                            </div>
                            
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden shadow-sm">
                                <img id="img-kanan" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[9px] sm:text-[10px] bg-black/60 text-white px-1 rounded">Kanan</span>
                            </div>
                            
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden shadow-sm col-start-1 sm:col-start-auto">
                                <img id="img-mulut" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[9px] sm:text-[10px] bg-black/60 text-white px-1 rounded">Mulut</span>
                            </div>

                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden shadow-sm">
                                <img id="img-menunduk" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[9px] sm:text-[10px] bg-black/60 text-white px-1 rounded">Menunduk</span>
                            </div>
                            
                            <button type="button" onclick="resetPhotos()" class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-full shadow-lg hover:bg-red-700 z-10 transition transform hover:scale-110" title="Hapus & Foto Ulang">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-xs sm:text-sm text-gray-500 mt-2 italic leading-tight">
                        *Data pengunjung dijaga kerahasiaannya dan hanya digunakan untuk keamanan pameran.
                    </p>

                </div>
            </form>
        </div>

        <div class="flex justify-end space-x-3 sm:space-x-4 w-full">
            <a href="{{ route('pendaftaran.index') }}" onclick="resetPhotos()" class="bg-white text-[#1f2937] font-bold py-3 px-8 sm:px-12 rounded-xl shadow-xl hover:bg-gray-100 transition duration-300 text-sm flex items-center justify-center min-w-[100px] sm:min-w-[120px]">
                Batal
            </a>
            <button type="button" id="btn-daftar" class="bg-white text-[#1f2937] font-bold py-3 px-8 sm:px-12 rounded-xl shadow-xl hover:bg-gray-100 transition duration-300 text-sm flex items-center justify-center min-w-[100px] sm:min-w-[120px]">
                Daftar
            </button>
        </div>
    </div>

    <script>
        // Fungsi Reset Foto (Menghapus 5 Key LocalStorage)
        function resetPhotos() {
            localStorage.removeItem('temp_foto_wajah');
            localStorage.removeItem('temp_foto_kiri');
            localStorage.removeItem('temp_foto_kanan');
            localStorage.removeItem('temp_foto_mulut');
            localStorage.removeItem('temp_foto_menunduk'); // Tambahan Baru
            window.location.reload();
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Ambil ke-5 data dari LocalStorage
            const fotoLurus = localStorage.getItem('temp_foto_wajah');
            const fotoKiri = localStorage.getItem('temp_foto_kiri');
            const fotoKanan = localStorage.getItem('temp_foto_kanan');
            const fotoMulut = localStorage.getItem('temp_foto_mulut');
            const fotoMenunduk = localStorage.getItem('temp_foto_menunduk'); // Tambahan Baru

            const btnStart = document.getElementById('btn-start-camera');
            const photoGrid = document.getElementById('photo-grid');

            // Cek apakah KELIMA foto sudah ada
            if (fotoLurus && fotoKiri && fotoKanan && fotoMulut && fotoMenunduk) {
                // Sembunyikan tombol mulai, Tampilkan Grid
                btnStart.classList.add('hidden');
                photoGrid.classList.remove('hidden');
                photoGrid.classList.add('grid');

                // Isi Source Image untuk ke-5 gambar
                document.getElementById('img-lurus').src = fotoLurus;
                document.getElementById('img-kiri').src = fotoKiri;
                document.getElementById('img-kanan').src = fotoKanan;
                document.getElementById('img-mulut').src = fotoMulut;
                document.getElementById('img-menunduk').src = fotoMenunduk; // Tambahan Baru

                // Isi Input Hidden (Siap dikirim ke backend)
                document.getElementById('input_foto_lurus').value = fotoLurus;
                document.getElementById('input_foto_kiri').value = fotoKiri;
                document.getElementById('input_foto_kanan').value = fotoKanan;
                document.getElementById('input_foto_mulut').value = fotoMulut;
                document.getElementById('input_foto_menunduk').value = fotoMenunduk; // Tambahan Baru
            }

            // PERBAIKAN: Klik Daftar -> Submit Form ke Database
            document.getElementById('btn-daftar').addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah submit default dulu

                const nama = document.querySelector('input[name="nama_lengkap"]').value;
                const telp = document.querySelector('input[name="nomor_telepon"]').value;

                // Cek Validasi Kelengkapan 5 Foto
                if (!nama || !telp) {
                    alert("Mohon lengkapi Nama Lengkap dan Nomor Telepon terlebih dahulu.");
                } else if (!fotoLurus || !fotoKiri || !fotoKanan || !fotoMulut || !fotoMenunduk) {
                    alert("Harap selesaikan pengambilan 5 pose foto wajah terlebih dahulu!");
                } else {
                    // Bersihkan LocalStorage sebelum pindah halaman
                    localStorage.removeItem('temp_foto_wajah');
                    localStorage.removeItem('temp_foto_kiri');
                    localStorage.removeItem('temp_foto_kanan');
                    localStorage.removeItem('temp_foto_mulut');
                    localStorage.removeItem('temp_foto_menunduk');

                    // Submit Form
                    document.getElementById('registrationForm').submit();
                }
            });
        });
    </script>
</body>
</html>