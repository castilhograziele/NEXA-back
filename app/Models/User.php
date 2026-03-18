<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

/**
 * Model do usuário.
 *
 * Representa tanto usuários comuns quanto bar_owners.
 * A distinção entre os tipos é feita pelo sistema de roles do Spatie.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $table = 'main.users';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'cpf',
        'birth_date',
        'photo',
        'city',
        'notify_new_events',
        'notify_event_reminder',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'password'               => 'hashed',
            'birth_date'             => 'date',
            'notify_new_events'      => 'boolean',
            'notify_event_reminder'  => 'boolean',
        ];
    }

    /**
     * Retorna a URL pública da foto de perfil do usuário.
     *
     * Retorna null se nenhuma foto estiver cadastrada.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }

    /**
     * Verifica se o usuário tem pelo menos 18 anos.
     */
    public function isAdult(): bool
    {
        if (!$this->birth_date) {
            return true;
        }

        return $this->birth_date->age >= 18;
    }

    /**
     * Um usuário pode ter um bar.
     */
    public function bar(): HasOne
    {
        return $this->hasOne(Bar::class);
    }

    /**
     * Eventos que o usuário se inscreveu.
     */
    public function subscribedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'main.event_subscriptions')
                    ->withTimestamps();
    }
}