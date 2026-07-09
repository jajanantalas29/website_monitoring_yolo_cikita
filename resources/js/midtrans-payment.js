document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', async function () {
            const originalContent = this.innerHTML;
            this.innerHTML = '<span class="animate-spin">⏳</span> Memproses...';
            this.disabled = true;

            try {
                const response = await fetch('/api/midtrans/get-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        pelanggaran_id: payButton.getAttribute('data-id'),
                        denda: payButton.getAttribute('data-denda'),
                        nama: payButton.getAttribute('data-nama'),
                        telepon: payButton.getAttribute('data-telepon')
                    })
                });

                const data = await response.json();

                if (data.token) {
                    window.snap.pay(data.token, {
                        onSuccess: function(result) {
                            alert("Pembayaran Berhasil!");
                            window.location.reload();
                        },
                        onError: function(result) {
                            alert("Pembayaran Gagal.");
                        },
                        onClose: function() {
                            alert("Pembayaran dibatalkan.");
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token: ' + (data.message || 'Error'));
                }
            } catch (error) {
                alert('Gagal terhubung ke sistem pembayaran.');
            } finally {
                this.innerHTML = originalContent;
                this.disabled = false;
            }
        });
    }
});