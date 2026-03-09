<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bar extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'cnpj',
        'phone',
        'city',
        'address',
        'instagram',
        'description',
        'plan',
        'whatsapp',
    ];

    // verifica se o bar possui plano premium
    public function isPremium(): bool
    {
        return $this->plan === 'premium';
    }

    // retorna o limite de eventos ativos por mês conforme o plano
    public function monthlyEventLimit(): ?int
    {
        // null significa ilimitado
        return match($this->plan) {
            'free'    => 2,
            'premium' => null,
            default   => 2,
        };
    }

    // bar pertence a um usuário
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // bar possui vários eventos
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}