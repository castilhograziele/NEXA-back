<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    // Exemplos para a documentação do Scribe
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'E-mail do usuário.',
                'example'     => 'usuario@example.com',
            ],
            'password' => [
                'description' => 'Senha do usuário.',
                'example'     => 'password123',
            ],
        ];
    }
}