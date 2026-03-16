<?php

namespace App\Services;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private OtpService $otpService) {}

    // Cria usuário e dispara OTP — não retorna token ainda
    public function register(array $data): array
    {
        $user = User::create([
            'name'       => $data['name'],
            'phone'      => $data['phone'],
            'email'      => $data['email'] ?? null,
            'cpf'        => $data['cpf'],
            'birth_date' => $data['birth_date'],
            'role'       => $data['role'] ?? 'user',
        ]);

        // Todo usuário começa com a role correta
        $user->assignRole($data['role'] ?? 'user');

        // Gera e envia OTP — token só vem após verificação
        $code = $this->otpService->generate($data['phone']);

        // Por enquanto loga o código — Twilio entra no próximo passo
        Log::info('OTP gerado para novo usuário', [
            'phone' => $data['phone'],
            'code'  => $code, // remover após integrar Twilio
        ]);

        return [
            'message' => 'Código enviado para ' . $data['phone'],
            'phone'   => $data['phone'],
        ];
    }

    // Valida telefone e dispara OTP — não retorna token ainda
    public function login(array $data): array
    {
        $user = User::where('phone', $data['phone'])->first();

        // Resposta genérica — não revela se o telefone existe
        if (! $user) {
            Log::warning('OTP: tentativa de login com telefone não cadastrado', [
                'phone' => $data['phone'],
            ]);
        } else {
            $code = $this->otpService->generate($data['phone']);

            Log::info('OTP gerado para login', [
                'phone' => $data['phone'],
                'code'  => $code, // remover após integrar Twilio
            ]);
        }

        // Sempre retorna a mesma resposta — não revela se existe
        return [
            'message' => 'Se este número estiver cadastrado, um código foi enviado.',
            'phone'   => $data['phone'],
        ];
    }

    // Verifica OTP e retorna token de acesso
    public function verifyOtp(string $phone, string $code): array
    {
        $valid = $this->otpService->verify($phone, $code);

        if (! $valid) {
            throw ValidationException::withMessages([
                'code' => ['Código inválido, expirado ou número de tentativas excedido.'],
            ]);
        }

        $user = User::where('phone', $phone)->firstOrFail();

        // Revoga tokens anteriores antes de gerar novo
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    // Revoga todos os tokens do usuário autenticado
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}