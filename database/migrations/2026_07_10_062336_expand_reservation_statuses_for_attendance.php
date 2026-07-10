<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', [
                'pending_payment',
                'paid',
                'occupied',
                'completed',
                'no_show',
                'cancelled',
            ])->default('paid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reservations')
            ->whereIn('status', ['occupied', 'completed', 'no_show'])
            ->update(['status' => 'paid']);

        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', ['pending_payment', 'paid', 'cancelled'])
                ->default('paid')
                ->change();
        });
    }
};
