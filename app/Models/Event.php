<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bar;


class Event extends Model
{
    // campos que podem ser preenchidos em massa
    protected $fillable = [
        'bar_id',
        'title',
        'description',
        'event_date',
        'event_time',
        'category',
        'is_active',
    ];

    // relacionamento: um evento pertence a um bar
    public function bar()
{
    return $this->belongsTo(Bar::class);
}

}
