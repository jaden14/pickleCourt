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
        Schema::table('court_unavailabilities', function (Blueprint $table) {
            $table->foreignId('court_id')->nullable()->change();
            $table->enum('action', ['disable', 'add'])->default('disable')->after('time_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('court_unavailabilities', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }
};
