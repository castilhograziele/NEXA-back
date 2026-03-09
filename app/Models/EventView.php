<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventView extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'ip_address',
    ];

    // visualização pertence a um evento
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // visualização pode pertencer a um usuário (nullable)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}