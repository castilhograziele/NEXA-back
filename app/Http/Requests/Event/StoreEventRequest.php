<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criação de eventos.
 *
 * Apenas bar_owners podem criar eventos.
 * Funcionalidades premium são validadas no EventService.
 */
class StoreEventRequest extends FormRequest
{
    /**
     * Apenas bar_owners podem criar eventos.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('bar_owner');
    }

    /**
     * Regras de validação para criação de evento.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'event_date'      => ['required', 'date', 'after_or_equal:today'],
            'event_time'      => ['required', 'date_format:H:i'],
            'category'        => ['required', 'string', 'max:100'],
            'music_style'     => ['nullable', 'string', 'max:255'],
            'is_active'       => ['nullable', 'boolean'],
            'is_featured'     => ['nullable', 'boolean'],
            'spotify_url'     => ['nullable', 'url', 'max:255'],
            'age_restriction' => ['nullable', 'in:none,18'],
            'benefit'         => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Exemplos para a documentação do Scribe.
     *
     * @return array<string, mixed>
     */
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
            'music_style' => [
                'description' => 'O que vai tocar no evento. Texto livre.',
                'example'     => 'Lady Gaga, Britney Spears, hits dos anos 2000',
            ],
            'is_active' => [
                'description' => 'Define se o evento está ativo.',
                'example'     => true,
            ],
            'is_featured' => [
                'description' => 'Define se o evento aparece em "Não pode perder". Apenas bares premium.',
                'example'     => false,
            ],
            'spotify_url' => [
                'description' => 'Link da playlist do Spotify. Apenas bares premium.',
                'example'     => 'https://open.spotify.com/playlist/exemplo',
            ],
            'age_restriction' => [
                'description' => 'Classificação etária: none (livre) ou 18 (maiores de 18).',
                'example'     => 'none',
            ],
            'benefit' => [
                'description' => 'Benefício oferecido ao usuário que fizer check-in.',
                'example'     => '1 drink grátis na entrada',
            ],
        ];
    }
}