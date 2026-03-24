<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Model de foto da galeria do bar.
 *
 * Representa uma foto do ambiente do bar.
 * Cada bar pode ter múltiplas fotos na galeria.
 */
class BarPhoto extends Model
{
    protected $table = 'main.bar_photos';
    
    protected $fillable = [
        'bar_id',
        'path',
        'caption',
        'order',
    ];

    /**
     * Retorna a URL pública da foto.
     *
     * Gera o link acessível pelo frontend a partir do path salvo no storage.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * A foto pertence a um bar.
     */
    public function bar(): BelongsTo
    {
        return $this->belongsTo(Bar::class);
    }
}