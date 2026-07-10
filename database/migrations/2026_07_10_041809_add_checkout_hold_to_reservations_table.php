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
        if (! Schema::hasIndex('reservations', 'reservations_slot_index')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index(['court_id', 'date', 'time_slot'], 'reservations_slot_index');
            });
        }

        if (! Schema::hasColumn('reservations', 'expires_at')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable()->after('status');
            });
        }

        if (Schema::hasIndex('reservations', 'reservations_court_id_date_time_slot_unique')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropUnique(['court_id', 'date', 'time_slot']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unique(['court_id', 'date', 'time_slot']);
            $table->dropColumn('expires_at');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservations_slot_index');
        });
    }
};
