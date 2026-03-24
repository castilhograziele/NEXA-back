<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validação para cadastro de usuário.
 *
 * Regras diferenciadas por role:
 * - user: CPF obrigatório, email opcional
 * - bar_owner: CPF obrigatório, email obrigatório
 */
class RegisterRequest extends FormRequest
{
    /**
     * Qualquer visitante pode se cadastrar.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para cadastro.
     *
     * @return array<string, mixed>
     */
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

            // CPF obrigatório para todos — identifica a pessoa física
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
                'before:today',
            ],

            'role' => ['sometimes', 'in:user,bar_owner'],

            // Email opcional para user
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
        ];

        // Email obrigatório para bar_owner — necessário para comunicação comercial
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

    /**
     * Mensagens de validação customizadas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cpf.size'         => 'O CPF deve ter 11 dígitos.',
            'cpf.regex'        => 'O CPF deve conter apenas números.',
            'cpf.unique'       => 'Este CPF já está cadastrado.',
            'birth_date.before'=> 'A data de nascimento deve ser uma data passada.',
            'phone.unique'     => 'Este telefone já está cadastrado.',
            'email.unique'     => 'Este e-mail já está cadastrado.',
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
                'description' => 'Data de nascimento (formato Y-m-d).',
                'example'     => '1990-05-15',
            ],
            'email' => [
                'description' => 'Obrigatório para bar_owner, opcional para user.',
                'example'     => 'joao@example.com',
            ],
            'role' => [
                'description' => 'Papel do usuário: user ou bar_owner.',
                'example'     => 'user',
            ],
        ];
    }
}