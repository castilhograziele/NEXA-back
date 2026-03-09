<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSubscription extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
    ];

    // inscrição pertence a um evento
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // inscrição pertence a um usuário
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}