<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_play_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_key', 64);
            $table->string('name');
            $table->string('mode', 20)->default('balanced');
            $table->unsignedTinyInteger('courts')->default(1);
            $table->unsignedSmallInteger('points')->nullable();
            $table->boolean('points_on')->default(true);
            $table->boolean('timer')->default(false);
            $table->unsignedInteger('round')->default(1);
            $table->unsignedInteger('matches_played')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'session_key']);
            $table->index(['user_id', 'started_at']);
        });

        Schema::create('open_play_session_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_play_session_id')->constrained()->cascadeOnDelete();
            $table->string('player_key', 64)->nullable();
            $table->string('name');
            $table->unsignedTinyInteger('rating')->default(4);
            $table->string('status', 20)->default('ready');
            $table->unsignedInteger('games_played')->default(0);
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->string('partner_key', 64)->nullable();
            $table->timestamp('queued_at')->nullable();
            $table->timestamps();
            $table->unique(['open_play_session_id', 'name']);
        });

        Schema::create('open_play_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_play_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('court')->default(1);
            $table->unsignedInteger('round')->default(1);
            $table->unsignedSmallInteger('score_a')->default(0);
            $table->unsignedSmallInteger('score_b')->default(0);
            $table->char('winner', 1)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['open_play_session_id', 'round']);
        });

        Schema::create('open_play_match_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_play_match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('open_play_session_player_id')->constrained()->cascadeOnDelete();
            $table->char('team', 1);
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['open_play_match_id', 'open_play_session_player_id'], 'op_match_player_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_play_match_players');
        Schema::dropIfExists('open_play_matches');
        Schema::dropIfExists('open_play_session_players');
        Schema::dropIfExists('open_play_sessions');
    }
};
