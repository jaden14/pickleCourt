<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenPlaySessionPlayer extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['queued_at' => 'datetime'];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(OpenPlaySession::class, 'open_play_session_id');
    }
}
