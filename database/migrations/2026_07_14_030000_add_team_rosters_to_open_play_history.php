<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('open_play_sessions', function (Blueprint $table): void {
            $table->json('team_names')->nullable()->after('team_count');
        });

        Schema::table('open_play_session_players', function (Blueprint $table): void {
            $table->unsignedTinyInteger('team_index')->nullable()->after('partner_key');
        });
    }

    public function down(): void
    {
        Schema::table('open_play_session_players', function (Blueprint $table): void {
            $table->dropColumn('team_index');
        });

        Schema::table('open_play_sessions', function (Blueprint $table): void {
            $table->dropColumn('team_names');
        });
    }
};
