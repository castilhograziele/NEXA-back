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
            'name' => ['required', 'string', 'max:255'],

            'phone' => [
                'required',
                'string',
                'regex:/^\+?[1-9]\d{7,14}$/',
                Rule::unique('users', 'phone'),
            ],

            // CPF apenas números, 11 dígitos
            'cpf' => [
                'required',
                'string',
                'size:11',
                'regex:/^\d{11}$/',
                Rule::unique('users', 'cpf'),
            ],

            // Data de nascimento — usada para verificar idade na inscrição em eventos
            'birth_date' => [
                'required',
                'date',
                'before' => now()->toDateString(), // apenas garante que é uma data passada
            ],

            'role' => ['sometimes', 'in:user,bar_owner'],

            // Email opcional para user, obrigatório para bar_owner
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
        ];

        // Email obrigatório apenas para bar_owner
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

    public function messages(): array
    {
        return [
        'cpf.size'   => 'O CPF deve ter 11 dígitos.',
        'cpf.regex'  => 'O CPF deve conter apenas números.',
        'cpf.unique' => 'Este CPF já está cadastrado.',
        ];
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
            'cpf' => [
                'description' => 'CPF do usuário, apenas números, 11 dígitos.',
                'example'     => '12345678901',
            ],
            'birth_date' => [
                'description' => 'Data de nascimento (formato Y-m-d)',
                'example'     => '1990-05-15',
            ],
            'email' => [
                'description' => 'Obrigatório apenas para bar_owner, opcional para user.',
                'example'     => 'joao@example.com',
            ],
            'role' => [
                'description' => 'Papel do usuário: user ou bar_owner.',
                'example'     => 'user',
            ],
        ];
    }
}