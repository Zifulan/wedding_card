<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'wedding_id', 'name', 'unique_token', 'invite_url',
        'open_count', 'last_opened_at', 'created_at',
    ];

    protected $casts = [
        'last_opened_at' => 'datetime',
        'created_at'     => 'datetime',
    ];

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    public function rsvp(): HasOne
    {
        return $this->hasOne(Rsvp::class);
    }
}
