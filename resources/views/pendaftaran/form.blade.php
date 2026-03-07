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
        
        <div class="bg-white rounded-[2rem] shadow-2xl p-10 w-full mb-6">
            
            <div class="mb-8">
                <h1 class="text-[#1f2937] text-4xl font-black tracking-tighter mb-2">Pendaftaran</h1>
                <p class="text-gray-400 text-[13px] mb-4 leading-tight">Silahkan lakukan pendaftaran untuk dapat mendapatkan benefit dari sistem ini..</p>
                <div class="w-full border-b border-gray-200"></div>
            </div>

            <form action="{{ route('pendaftaran.store') }}" method="POST" id="registrationForm" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-xl tracking-tight">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-input w-full border border-gray-400 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-gray-200 transition text-gray-800" placeholder="Masukan nama lengkap anda..">
                </div>

                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-xl tracking-tight">Nomor Telepon</label>
                    <input type="number" name="nomor_telepon" class="form-input w-full border border-gray-400 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-gray-200 transition text-gray-800" placeholder="Masukan nomor telepon anda..">
                </div>

                <div>
                    <label class="block text-[#1f2937] font-bold mb-2 text-xl tracking-tight">Foto Wajah</label>
                    
                    <input type="hidden" name="foto_lurus" id="input_foto_lurus">
                    <input type="hidden" name="foto_kiri" id="input_foto_kiri">
                    <input type="hidden" name="foto_kanan" id="input_foto_kanan">
                    <input type="hidden" name="foto_mulut" id="input_foto_mulut">

                    <div class="relative w-full border-2 border-gray-400 rounded-2xl overflow-hidden hover:bg-gray-50 transition-all">
                        
                        <a href="{{ route('pendaftaran.kamera') }}" id="btn-start-camera" class="flex flex-col items-center justify-center w-full h-60 cursor-pointer group">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="bg-[#1f2937] rounded-2xl p-4 mb-4 shadow-lg transform transition group-hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 text-white">
                                        <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-sm text-[#1f2937] font-bold tracking-wide">Ambil 4 Pose Wajah</p>
                            </div>
                        </a>

                        <div id="photo-grid" class="hidden grid-cols-2 gap-1 p-1 bg-gray-100">
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden">
                                <img id="img-lurus" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[10px] bg-black/60 text-white px-1 rounded">Lurus</span>
                            </div>
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden">
                                <img id="img-kiri" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[10px] bg-black/60 text-white px-1 rounded">Kiri</span>
                            </div>
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden">
                                <img id="img-kanan" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[10px] bg-black/60 text-white px-1 rounded">Kanan</span>
                            </div>
                            <div class="relative aspect-square bg-gray-300 rounded overflow-hidden">
                                <img id="img-mulut" src="" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-1 text-[10px] bg-black/60 text-white px-1 rounded">Mulut</span>
                            </div>
                            
                            <button type="button" onclick="resetPhotos()" class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full shadow hover:bg-red-700 z-10" title="Hapus & Foto Ulang">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <div class="flex justify-end space-x-4 w-full">
            <a href="{{ route('pendaftaran.index') }}" onclick="resetPhotos()" class="bg-white text-[#1f2937] font-bold py-3 px-12 rounded-xl shadow-xl hover:bg-gray-100 transition duration-300 text-sm flex items-center justify-center min-w-[120px]">
                Batal
            </a>
            <button type="button" id="btn-daftar" class="bg-white text-[#1f2937] font-bold py-3 px-12 rounded-xl shadow-xl hover:bg-gray-100 transition duration-300 text-sm flex items-center justify-center min-w-[120px]">
                Daftar
            </button>
        </div>
    </div>

    <script>
        // Fungsi Reset Foto
        function resetPhotos() {
            localStorage.removeItem('temp_foto_wajah');
            localStorage.removeItem('temp_foto_kiri');
            localStorage.removeItem('temp_foto_kanan');
            localStorage.removeItem('temp_foto_mulut');
            window.location.reload();
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Ambil data dari LocalStorage
            const fotoLurus = localStorage.getItem('temp_foto_wajah');
            const fotoKiri = localStorage.getItem('temp_foto_kiri');
            const fotoKanan = localStorage.getItem('temp_foto_kanan');
            const fotoMulut = localStorage.getItem('temp_foto_mulut');

            const btnStart = document.getElementById('btn-start-camera');
            const photoGrid = document.getElementById('photo-grid');

            // Cek apakah KEEMPAT foto sudah ada
            if (fotoLurus && fotoKiri && fotoKanan && fotoMulut) {
                // Sembunyikan tombol mulai, Tampilkan Grid
                btnStart.classList.add('hidden');
                photoGrid.classList.remove('hidden');
                photoGrid.classList.add('grid');

                // Isi Source Image
                document.getElementById('img-lurus').src = fotoLurus;
                document.getElementById('img-kiri').src = fotoKiri;
                document.getElementById('img-kanan').src = fotoKanan;
                document.getElementById('img-mulut').src = fotoMulut;

                // Isi Input Hidden (Siap dikirim ke backend)
                document.getElementById('input_foto_lurus').value = fotoLurus;
                document.getElementById('input_foto_kiri').value = fotoKiri;
                document.getElementById('input_foto_kanan').value = fotoKanan;
                document.getElementById('input_foto_mulut').value = fotoMulut;
            }

            // PERBAIKAN: Klik Daftar -> Submit Form ke Database
            document.getElementById('btn-daftar').addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah submit default dulu

                const nama = document.querySelector('input[name="nama_lengkap"]').value;
                const telp = document.querySelector('input[name="nomor_telepon"]').value;
                const foto = document.getElementById('input_foto_mulut').value;

                if (!nama || !telp) {
                    alert("Mohon lengkapi Nama dan Nomor Telepon.");
                } else if (!foto) {
                    alert("Harap selesaikan pengambilan 4 pose foto terlebih dahulu!");
                } else {
                    // Submit Form secara program
                    // Pastikan untuk menghapus localStorage agar bersih untuk pendaftar selanjutnya
                    localStorage.removeItem('temp_foto_wajah');
                    localStorage.removeItem('temp_foto_kiri');
                    localStorage.removeItem('temp_foto_kanan');
                    localStorage.removeItem('temp_foto_mulut');

                    document.getElementById('registrationForm').submit();
                }
            });
        });
    </script>
</body>
</html>