<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model de avaliação de evento.
 *
 * Apenas usuários que fizeram check-in podem avaliar.
 * Apenas após a data do evento.
 * Não pode ser editada após enviada.
 */
class EventReview extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'stars',
        'music_rating',
        'drink_rating',
        'cleanliness_rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'stars'               => 'integer',
            'music_rating'        => 'integer',
            'drink_rating'        => 'integer',
            'cleanliness_rating'  => 'integer',
        ];
    }

    /**
     * A avaliação pertence a um evento.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * A avaliação pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}