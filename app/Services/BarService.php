<?php

namespace App\Services;

use App\Models\Bar;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class BarService
{
    // Cadastra um novo bar para o usuário autenticado
    public function store(User $user, array $data): Bar
    {
        // Regra: um bar_owner só pode ter um bar
        if ($user->bar) {
            throw ValidationException::withMessages([
                'bar' => ['Você já possui um bar cadastrado.'],
            ]);
        }

        // Cria o bar vinculado ao usuário
        return Bar::create([
            'user_id'     => $user->id,
            'name'        => $data['name'],
            'cnpj'        => $data['cnpj'],
            'phone'       => $data['phone'] ?? null,
            'city'        => $data['city'] ?? null,
            'address'     => $data['address'] ?? null,
            'instagram'   => $data['instagram'] ?? null,
            'whatsapp'    => $data['whatsapp'] ?? null,
            'description' => $data['description'] ?? null,
        ]);
    }

    // Atualiza os dados do bar
    public function update(Bar $bar, array $data): Bar
    {
        // Apenas premium pode adicionar WhatsApp
        if (!empty($data['whatsapp']) && !$bar->isPremium()) {
            throw ValidationException::withMessages([
                'whatsapp' => ['Apenas bares com plano premium podem adicionar link do WhatsApp.'],
            ]);
        }

        $bar->update($data);

        return $bar;
    }
}