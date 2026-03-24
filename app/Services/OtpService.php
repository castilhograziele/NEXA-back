<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    // Gera um novo código OTP e invalida os anteriores do mesmo telefone
    public function generate(string $phone): string
    {
        // Invalida todos os códigos anteriores deste telefone
        // para evitar que múltiplos códigos válidos coexistam
        OtpCode::where('phone', $phone)
            ->where('used', false)
            ->update(['used' => true]);

        // Gera código numérico de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Armazena apenas o hash — nunca o código puro
        OtpCode::create([
            'phone'      => $phone,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes((int) env('OTP_EXPIRATION_MINUTES', 5)),
            'attempts'   => 0,
            'used'       => false,
        ]);

        return $code;
    }

    // Verifica se o código informado é válido para o telefone
    public function verify(string $phone, string $code): bool
    {
        $otp = OtpCode::where('phone', $phone)
            ->where('used', false)
            ->latest()
            ->first();

        // Sem código ativo para este telefone
        if (! $otp) {
            Log::warning('OTP: tentativa sem código ativo', ['phone' => $phone]);
            return false;
        }

        // Incrementa tentativas antes de qualquer verificação
        // para contar inclusive tentativas com código expirado/bloqueado
        $otp->increment('attempts');

        if (! $otp->isValid()) {
            Log::warning('OTP: código inválido ou expirado', [
                'phone'    => $phone,
                'expired'  => $otp->isExpired(),
                'blocked'  => $otp->isBlocked(),
            ]);
            return false;
        }

        // Verifica o código contra o hash armazenado
        if (! Hash::check($code, $otp->code_hash)) {
            Log::warning('OTP: código incorreto', [
                'phone'    => $phone,
                'attempts' => $otp->attempts,
            ]);
            return false;
        }

        // Marca como usado — não pode ser reutilizado
        $otp->update(['used' => true]);

        return true;
    }

    // Remove códigos expirados — chamar via scheduled job
    public function cleanup(string $phone): void
    {
        OtpCode::where('phone', $phone)
            ->where('expires_at', '<', now())
            ->delete();
    }
}