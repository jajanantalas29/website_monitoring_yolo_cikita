<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Menambahkan kolom foto_mendongak setelah foto_menunduk
            $table->string('foto_mendongak')->nullable()->after('foto_menunduk');
            
            // Sekalian memastikan kolom embedding cukup besar untuk menampung data AI (120 pose)
            // Hapus atau beri komentar baris ini jika tipe data embedding di tabelmu SUDAH longText
            $table->longText('embedding')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn('foto_mendongak');
        });
    }
};