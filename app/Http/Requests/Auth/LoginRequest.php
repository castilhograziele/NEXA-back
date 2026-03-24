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
            'phone' => ['required', 'string', 'regex:/^\+?[1-9]\d{7,14}$/'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'phone' => [
                'description' => 'Telefone cadastrado. Receberá um SMS com o código.',
                'example'     => '+5511999999999',
            ],
        ];
    }
}