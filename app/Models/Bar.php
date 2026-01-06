<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Event;


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
    ];

    // bar pertence a um usuário
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // bar possui vários eventos
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
