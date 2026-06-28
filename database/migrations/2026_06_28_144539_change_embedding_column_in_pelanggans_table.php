<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Ubah tipe data kolom menjadi LONGTEXT agar mampu menampung data ukuran raksasa
            $table->longText('embedding')->change();
        });
    }

    public function down()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Kembalikan ke TEXT jika terjadi rollback (sesuaikan dengan tipe data lamamu)
            $table->text('embedding')->change();
        });
    }
};
