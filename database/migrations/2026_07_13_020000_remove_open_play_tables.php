<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('open_play_entries');
        Schema::dropIfExists('open_play_games');
        Schema::dropIfExists('open_play_sessions');
        Schema::dropIfExists('players');

        if (Schema::hasColumn('courts', 'target_tier')) {
            Schema::table('courts', function (Blueprint $table) {
                $table->dropColumn('target_tier');
            });
        }
    }

    public function down(): void
    {
        // Open Play was intentionally removed.
    }
};
