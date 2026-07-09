<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Tiket Pendaftaran - Monitoring Pameran</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    
    <!-- Script Midtrans -->
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
            
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#1f2937] antialiased min-h-screen flex items-center justify-center p-4 relative">

    <div class="w-full max-w-md flex flex-col z-10">
        
        <div class="bg-white rounded-[1.5rem] shadow-2xl p-8 w-full text-center">
            
            <!-- Icon Tiket -->
            <div class="w-20 h-20 bg-[#f3f4f6] rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-white shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1f2937" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                </svg>
            </div>
            
            <h2 class="text-[#1f2937] text-2xl font-bold tracking-tight mb-2">Tiket Pendaftaran</h2>
            <p class="text-gray-500 text-sm mb-8 px-2">
                Pemindaian 6 pose wajah berhasil disimpan di perangkat. Silakan lakukan pembayaran tiket sebesar <strong>Rp 500</strong> untuk dapat memproses pendaftaran.
            </p>

            <div class="flex flex-col gap-3 w-full">
                <button id="pay-button" class="w-full bg-[#1f2937] text-white font-bold py-3.5 px-6 rounded-xl shadow-xl hover:bg-gray-800 transition duration-300 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Bayar Tiket (Rp 500)
                </button>
                
                <a href="{{ route('pendaftaran.kamera') }}" class="w-full bg-white border-2 border-gray-200 text-gray-600 font-bold py-3 px-6 rounded-xl hover:bg-gray-50 transition duration-300 text-sm mt-2">
                    Ulangi Foto Wajah
                </a>
            </div>
        </div>
    </div>

    <script>
        const payButton = document.getElementById('pay-button');

        if (payButton) {
            payButton.addEventListener('click', async function () {
                const originalContent = this.innerHTML;
                this.innerHTML = '<span class="animate-spin mr-2">⏳</span> Memproses...';
                this.disabled = true;

                try {
                    // Panggil rute API untuk generate token tiket
                    const response = await fetch('/api/midtrans/tiket-token', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.token) {
                        window.snap.pay(data.token, {
                            onSuccess: function(result) {
                                // Jika sukses, arahkan ke halaman form pendaftaran
                                alert("Pembayaran Berhasil! Silakan lengkapi biodata Anda.");
                                window.location.href = "{{ route('pendaftaran.form') }}";
                            },
                            onPending: function(result) {
                                alert("Menunggu konfirmasi pembayaran Anda.");
                            },
                            onError: function(result) {
                                alert("Pembayaran Gagal. Silakan coba lagi.");
                            },
                            onClose: function() {
                                alert("Jendela pembayaran ditutup.");
                            }
                        });
                    } else {
                        alert('Gagal memuat pembayaran. Silakan coba lagi.');
                    }
                } catch (error) {
                    alert('Gagal terhubung ke server pembayaran.');
                } finally {
                    this.innerHTML = originalContent;
                    this.disabled = false;
                }
            });
        }
    </script>
</body>
</html>