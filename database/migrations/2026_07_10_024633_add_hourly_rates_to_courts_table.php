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
        Schema::table('courts', function (Blueprint $table) {
            $table->decimal('day_hourly_rate', 10, 2)->default(200)->after('booking_ends_at');
            $table->decimal('night_hourly_rate', 10, 2)->default(250)->after('day_hourly_rate');
            $table->time('day_rate_starts_at')->default('06:00:00')->after('night_hourly_rate');
            $table->time('night_rate_starts_at')->default('18:00:00')->after('day_rate_starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->dropColumn([
                'day_hourly_rate',
                'night_hourly_rate',
                'day_rate_starts_at',
                'night_rate_starts_at',
            ]);
        });
    }
};
