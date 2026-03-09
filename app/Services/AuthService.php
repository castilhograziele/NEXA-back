<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    // Cria um novo usuário e atribui a role padrão
    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Todo usuário começa com a role 'user'
        $user->assignRole('user');

        // Gera o token Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    // Valida credenciais e gera token de acesso
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        // Verifica se o usuário existe e a senha está correta
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        // Gera um novo token Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    // Revoga todos os tokens do usuário autenticado
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}