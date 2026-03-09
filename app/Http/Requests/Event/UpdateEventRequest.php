<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('bar_owner') &&
               $this->user()->bar?->id === $this->route('event')->bar_id;
    }

    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date'  => ['sometimes', 'date', 'after_or_equal:today'],
            'event_time'  => ['sometimes', 'date_format:H:i'],
            'category'    => ['sometimes', 'string', 'max:100'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    // Exemplos para a documentação do Scribe
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
        ];
    }
}