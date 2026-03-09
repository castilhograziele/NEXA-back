<?php

namespace App\Http\Requests\Bar;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('bar_owner');
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'cnpj'        => ['required', 'string', 'size:14', 'unique:bars,cnpj'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'city'        => ['nullable', 'string', 'max:255'],
            'address'     => ['nullable', 'string', 'max:255'],
            'instagram'   => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    // Exemplos para a documentação do Scribe
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Nome do bar.',
                'example'     => 'Bar do Zé',
            ],
            'cnpj' => [
                'description' => 'CNPJ do bar (apenas números, 14 dígitos).',
                'example'     => '12345678000195',
            ],
            'phone' => [
                'description' => 'Telefone de contato.',
                'example'     => '54999999999',
            ],
            'city' => [
                'description' => 'Cidade onde o bar está localizado.',
                'example'     => 'Passo Fundo',
            ],
            'address' => [
                'description' => 'Endereço completo do bar.',
                'example'     => 'Rua Morom, 123',
            ],
            'instagram' => [
                'description' => 'Perfil do Instagram do bar.',
                'example'     => '@bardoze',
            ],
            'description' => [
                'description' => 'Descrição do bar.',
                'example'     => 'O melhor bar da cidade!',
            ],
        ];
    }
}