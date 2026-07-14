<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenPlayMatchPlayer extends Model
{
    protected $guarded = [];

    public function match(): BelongsTo
    {
        return $this->belongsTo(OpenPlayMatch::class, 'open_play_match_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(OpenPlaySessionPlayer::class, 'open_play_session_player_id');
    }
}
