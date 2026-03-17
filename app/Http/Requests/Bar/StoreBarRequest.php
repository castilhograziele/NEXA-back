<?php

namespace App\Http\Requests\Bar;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para cadastro de bar.
 *
 * Apenas bar_owners podem cadastrar um bar.
 * Cada bar_owner pode ter apenas um bar.
 */
class StoreBarRequest extends FormRequest
{
    /**
     * Apenas bar_owners podem cadastrar um bar.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('bar_owner');
    }

    /**
     * Regras de validação para cadastro de bar.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'cnpj'         => ['required', 'string', 'size:14', 'unique:bars,cnpj'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'city'         => ['nullable', 'string', 'max:255'],
            'address'      => ['nullable', 'string', 'max:255'],
            'instagram'    => ['nullable', 'string', 'max:255'],
            'whatsapp'     => ['nullable', 'string', 'max:20'],
            'description'  => ['nullable', 'string'],
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
                'description' => 'Nome fantasia do bar.',
                'example'     => 'Bar do Zé',
            ],
            'razao_social' => [
                'description' => 'Razão social do estabelecimento.',
                'example'     => 'José da Silva ME',
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
            'whatsapp' => [
                'description' => 'Número do WhatsApp para contato ou reservas.',
                'example'     => '54999999999',
            ],
            'description' => [
                'description' => 'Descrição do bar.',
                'example'     => 'O melhor bar da cidade!',
            ],
        ];
    }
}