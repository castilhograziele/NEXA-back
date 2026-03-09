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
    ];

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