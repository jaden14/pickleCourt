<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('open_play_sessions', function (Blueprint $table): void {
            $table->date('event_date')->nullable()->after('mode');
            $table->unsignedTinyInteger('team_count')->nullable()->after('event_date');
        });
    }

    public function down(): void
    {
        Schema::table('open_play_sessions', function (Blueprint $table): void {
            $table->dropColumn(['event_date', 'team_count']);
        });
    }
};
