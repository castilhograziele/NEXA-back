<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Model do bar.
 *
 * Representa o estabelecimento cadastrado pelo bar_owner.
 * Um usuário com role bar_owner pode ter apenas um bar.
 */
class Bar extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'razao_social',
        'cnpj',
        'phone',
        'city',
        'address',
        'instagram',
        'whatsapp',
        'description',
        'plan',
        'photo',
    ];

    /**
     * Retorna a URL pública da foto de perfil do bar.
     *
     * Retorna null se nenhuma foto estiver cadastrada.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }

    /**
     * Verifica se o bar possui plano premium.
     */
    public function isPremium(): bool
    {
        return $this->plan === 'premium';
    }

    /**
     * Retorna o limite mensal de eventos conforme o plano.
     *
     * Plano free: máximo 2 eventos ativos por mês.
     * Plano premium: sem limite (retorna null).
     */
    public function monthlyEventLimit(): ?int
    {
        return match($this->plan) {
            'free'    => 2,
            'premium' => null,
            default   => 2,
        };
    }

    /**
     * O bar pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * O bar possui vários eventos.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * O bar possui várias fotos na galeria.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(BarPhoto::class)->orderBy('order');
    }
}