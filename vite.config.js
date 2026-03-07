import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import os from 'os';

// Fungsi untuk mendapatkan IP Local WiFi/LAN secara otomatis
function getLocalIP() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            // Cari alamat IPv4 yang bukan localhost (internal)
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return 'localhost'; // Fallback jika tidak ada koneksi
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Mengizinkan akses dari semua perangkat
        hmr: {
            host: getLocalIP(), // Memanggil fungsi untuk IP dinamis
        }
    }
});