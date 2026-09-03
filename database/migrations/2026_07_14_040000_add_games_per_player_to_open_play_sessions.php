<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('open_play_sessions', function (Blueprint $table): void {
            $table->unsignedTinyInteger('games_per_player')->default(4)->after('team_names');
        });
    }

    public function down(): void
    {
        Schema::table('open_play_sessions', function (Blueprint $table): void {
            $table->dropColumn('games_per_player');
        });
    }
};
