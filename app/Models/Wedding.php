<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wedding extends Model
{
    protected $fillable = [
        'groom_name', 'bride_name',
        'akad_date', 'akad_time',
        'resepsi_date', 'resepsi_time',
        'venue_name', 'venue_address', 'gmaps_link',
        'rsvp_whatsapp', 'love_quote',
    ];

    protected $casts = [
        'akad_date'    => 'date',
        'resepsi_date' => 'date',
    ];

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }
}
