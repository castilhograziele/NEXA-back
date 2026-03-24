<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model de inscrição em evento.
 *
 * Representa o vínculo entre um usuário e um evento,
 * incluindo o status de check-in e o benefício liberado.
 */
class EventSubscription extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'checked_in',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_in'    => 'boolean',
            'checked_in_at' => 'datetime',
        ];
    }

    /**
     * Verifica se o usuário já fez check-in neste evento.
     */
    public function isCheckedIn(): bool
    {
        return $this->checked_in === true;
    }

    /**
     * A inscrição pertence a um evento.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * A inscrição pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}