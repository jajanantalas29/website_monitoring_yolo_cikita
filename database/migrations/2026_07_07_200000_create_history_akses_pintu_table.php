<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel audit trail untuk akses pintu Node 2.
     * Mencatat setiap tap kartu RFID: masuk/keluar, granted/denied.
     */
    public function up()
    {
        Schema::create('history_akses_pintu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->string('uid_kartu', 50);
            $table->string('direction', 10);          // 'masuk' | 'keluar'
            $table->string('status', 10);             // 'granted' | 'denied'
            $table->float('similarity_score')->nullable();
            $table->string('reason', 50)->nullable(); // 'uid_not_registered', 'face_mismatch', 'face_not_detected', 'pelanggan_mismatch', 'ai_server_error', 'invalid_uid'
            $table->timestamp('waktu')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('history_akses_pintu');
    }
};