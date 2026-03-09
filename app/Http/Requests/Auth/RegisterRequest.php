<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    // Exemplos para a documentação do Scribe
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Nome completo do usuário.',
                'example'     => 'João Silva',
            ],
            'email' => [
                'description' => 'E-mail do usuário.',
                'example'     => 'joao@example.com',
            ],
            'password' => [
                'description' => 'Senha com mínimo de 8 caracteres.',
                'example'     => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Confirmação da senha.',
                'example'     => 'password123',
            ],
        ];
    }
}