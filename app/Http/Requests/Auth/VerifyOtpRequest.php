<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+?[1-9]\d{7,14}$/'],
            'code'  => ['required', 'string', 'digits:6'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'phone' => [
                'description' => 'Telefone que recebeu o código.',
                'example'     => '+5511999999999',
            ],
            'code' => [
                'description' => 'Código de 6 dígitos recebido por SMS.',
                'example'     => '123456',
            ],
        ];
    }
}