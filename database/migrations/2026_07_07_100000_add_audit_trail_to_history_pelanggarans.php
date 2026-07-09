<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_pelanggarans', function (Blueprint $table) {
            $table->float('similarity_score')->nullable()->after('kamera');
            $table->float('match_margin')->nullable()->after('similarity_score');
            $table->json('top_candidates')->nullable()->after('match_margin');
            $table->integer('vote_count')->nullable()->after('top_candidates');
            $table->integer('total_frames')->nullable()->after('vote_count');
            $table->string('lighting_condition', 20)->nullable()->after('total_frames');
        });
    }

    public function down(): void
    {
        Schema::table('history_pelanggarans', function (Blueprint $table) {
            $table->dropColumn([
                'similarity_score',
                'match_margin',
                'top_candidates',
                'vote_count',
                'total_frames',
                'lighting_condition',
            ]);
        });
    }
};
