<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class UserSessions extends Model
{
    protected $table = 'user_sessions';

    const UPDATED_AT = 'last_active';

    protected $fillable = [
        'display_name',
        'preferred_language',
        'avatar_url',
        'interest_tag',
        'last_active',
        'session_id'
    ];

    protected $casts = [
        'last_active' => 'datetime',
    ];

    public function roomParticipants(): HasMany
    {
        return $this->hasMany(RoomsParticipants::class, 'session_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Messages::class, 'sender_id');
    }

    public function feedbackEntries(): HasMany
    {
        return $this->hasMany(Feedback::class, 'session_id');
    }

    public function filedReports(): HasMany
    {
        return $this->hasMany(Reports::class, 'reporter_id');
    }
}
