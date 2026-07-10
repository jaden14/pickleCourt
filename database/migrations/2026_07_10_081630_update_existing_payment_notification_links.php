<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('notifications')
            ->orderBy('created_at')
            ->each(function (object $notification): void {
                $data = json_decode($notification->data, true);

                if (! is_array($data)) {
                    return;
                }

                $wasUpdated = false;

                foreach ($data['actions'] ?? [] as &$action) {
                    if (($action['label'] ?? null) !== 'View reservations') {
                        continue;
                    }

                    $action['url'] = url('/admin/reservations/my-reservations');
                    $wasUpdated = true;
                }

                unset($action);

                if ($wasUpdated) {
                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->update(['data' => json_encode($data)]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing notification destinations cannot be safely reconstructed.
    }
};
