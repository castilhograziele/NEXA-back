<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'regex:/^\+?[1-9]\d{7,14}$/',
                // Usa a conexão padrão pgsql com o schema correto
                Rule::unique('users', 'phone'),
            ],
            'role'  => ['sometimes', 'in:user,bar_owner'],
        ];

        // E-mail obrigatório apenas para bar_owner
        if ($this->input('role') === 'bar_owner') {
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ];
        }

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Nome completo do usuário.',
                'example'     => 'João Silva',
            ],
            'phone' => [
                'description' => 'Telefone com DDI. Um SMS com código será enviado.',
                'example'     => '+5554999999999',
            ],
            'email' => [
                'description' => 'Obrigatório apenas para bar_owner.',
                'example'     => 'joao@example.com',
            ],
            'role' => [
                'description' => 'Papel do usuário: user ou bar_owner.',
                'example'     => 'user',
            ],
        ];
    }
}