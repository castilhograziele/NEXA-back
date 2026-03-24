<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    // Tabela no schema authentication
    protected $table = 'authentication.otp_codes';

    protected $fillable = [
        'phone',
        'code_hash',
        'expires_at',
        'attempts',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
        'attempts'   => 'integer',
    ];

    // Nunca expõe o hash do código em respostas JSON
    protected $hidden = ['code_hash'];

    // Verifica se o código já expirou
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    // Verifica se atingiu o limite de tentativas
    public function isBlocked(): bool
    {
        return $this->attempts >= (int) env('OTP_MAX_ATTEMPTS', 3);
    }

    // Verifica se o código ainda é válido para uso
    public function isValid(): bool
    {
        return ! $this->used
            && ! $this->isExpired()
            && ! $this->isBlocked();
    }
}