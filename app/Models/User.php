<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birth_date'        => 'date',
        ];
    }

    // Verifica se o usuário tem pelo menos 18 anos
    public function isAdult(): bool
    {
        if (!$this->birth_date) {
            return true;
        }

        return $this->birth_date->age >= 18;
    }

    // Um usuário pode ter um bar
    public function bar(): HasOne
    {
        return $this->hasOne(Bar::class);
    }

    // Eventos que o usuário se inscreveu
    public function subscribedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'main.event_subscriptions')
                    ->withTimestamps();
    }
}