<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Menambahkan kolom status_ruangan, otomatis nilainya 'di_luar' untuk pendaftar baru
            $table->string('status_ruangan')->default('di_luar')->after('nomor_telepon');
        });
    }

    public function down()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn('status_ruangan');
        });
    }
};