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
    ];

    // Verifica se o usuário tem idade suficiente para o evento
    public function isAgeAllowed(User $user): bool
    {
        // Evento livre — qualquer idade
        if ($this->age_restriction === 'none') {
            return true;
        }

        // Sem data de nascimento — bloqueia por segurança
        if (!$user->birth_date) {
            return false;
        }

        // Verifica se o usuário tem a idade mínima exigida
        return $user->birth_date->age >= (int) $this->age_restriction;
    }

    // um evento pertence a um bar
    public function bar(): BelongsTo
    {
        return $this->belongsTo(Bar::class);
    }

    // um evento pode ter várias inscrições
    public function subscriptions(): HasMany
    {
        return $this->hasMany(EventSubscription::class);
    }

    // um evento pode ter várias visualizações
    public function views(): HasMany
    {
        return $this->hasMany(EventView::class);
    }
}