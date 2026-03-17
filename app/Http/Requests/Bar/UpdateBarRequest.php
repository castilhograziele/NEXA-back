<?php

namespace App\Http\Requests\Bar;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para atualização de bar.
 *
 * Apenas o bar_owner dono do bar pode editar.
 * CNPJ não pode ser alterado após o cadastro.
 */
class UpdateBarRequest extends FormRequest
{
    /**
     * Apenas o bar_owner dono do bar pode editar.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('bar_owner') &&
               $this->user()->bar?->id === $this->route('bar')->id;
    }

    /**
     * Regras de validação para atualização de bar.
     * Todos os campos são opcionais — atualiza apenas o que for enviado.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'string', 'max:255'],
            'razao_social' => ['nullable', 'string', 'max:255'],
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