<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Menambahkan kolom embedding bertipe json (nullable agar data lama tidak error)
            $table->json('embedding')->nullable()->after('foto_mulut');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};