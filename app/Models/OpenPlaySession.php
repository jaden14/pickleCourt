<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpenPlaySession extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['event_date' => 'date', 'team_names' => 'array', 'points_on' => 'boolean', 'timer' => 'boolean', 'started_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(OpenPlaySessionPlayer::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(OpenPlayMatch::class);
    }
}
