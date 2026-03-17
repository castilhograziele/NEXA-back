<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'bar_id',
        'title',
        'description',
        'event_date',
        'event_time',
        'category',
        'is_active',
        'is_featured',
        'spotify_url',
        'age_restriction',
        'benefit',
    ];

    /**
     * Verifica se o usuário tem idade suficiente para o evento.
     *
     * Eventos com classificação 'none' são livres para qualquer idade.
     * Se o usuário não tiver birth_date cadastrado, bloqueia por segurança.
     */
    public function isAgeAllowed(User $user): bool
    {
        if ($this->age_restriction === 'none') {
            return true;
        }

        if (!$user->birth_date) {
            return false;
        }

        return $user->birth_date->age >= (int) $this->age_restriction;
    }

    /**
     * Um evento pertence a um bar.
     */
    public function bar(): BelongsTo
    {
        return $this->belongsTo(Bar::class);
    }

    /**
     * Um evento pode ter várias inscrições.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(EventSubscription::class);
    }

    /**
     * Um evento pode ter várias visualizações.
     */
    public function views(): HasMany
    {
        return $this->hasMany(EventView::class);
    }
}