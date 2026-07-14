<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'staff'], true);
    }

    public function openPlayAccessRequest(): HasOne
    {
        return $this->hasOne(OpenPlayAccessRequest::class);
    }

    public function openPlayState(): HasOne
    {
        return $this->hasOne(OpenPlayState::class);
    }

    public function openPlaySessions(): HasMany
    {
        return $this->hasMany(OpenPlaySession::class);
    }

    public function canUseOpenPlay(): bool
    {
        return $this->role === 'admin'
            || $this->openPlayAccessRequest()->where('status', 'approved')->exists();
    }
}
