<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('bar_owner');
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date'  => ['required', 'date', 'after_or_equal:today'],
            'event_time'  => ['required', 'date_format:H:i'],
            'category'    => ['required', 'string', 'max:100'],
            'is_active'   => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'spotify_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'Título do evento.',
                'example'     => 'Show de Samba ao Vivo',
            ],
            'description' => [
                'description' => 'Descrição detalhada do evento.',
                'example'     => 'Uma noite incrível de samba ao vivo.',
            ],
            'event_date' => [
                'description' => 'Data do evento (formato Y-m-d).',
                'example'     => '2026-04-15',
            ],
            'event_time' => [
                'description' => 'Horário do evento (formato H:i).',
                'example'     => '21:00',
            ],
            'category' => [
                'description' => 'Categoria do evento.',
                'example'     => 'samba',
            ],
            'is_active' => [
                'description' => 'Define se o evento está ativo.',
                'example'     => true,
            ],
            'is_featured' => [
                'description' => 'Define se o evento aparece em "Não pode perder". Apenas bares premium.',
                'example'     => true,
            ],
            'spotify_url' => [
                'description' => 'Link da playlist do Spotify. Apenas bares premium.',
                'example'     => 'https://open.spotify.com/playlist/exemplo',
            ],
        ];
    }
}