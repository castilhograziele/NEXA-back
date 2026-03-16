<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de códigos OTP no schema authentication
        // Armazena apenas o hash do código, nunca o valor puro
        Schema::create('authentication.otp_codes', function (Blueprint $table) {
            $table->id();

            // Telefone que solicitou o código
            $table->string('phone', 20)->index();

            // Hash bcrypt do código — nunca armazenamos o OTP puro
            $table->string('code_hash');

            // Momento em que o código expira (configurável via .env)
            $table->timestamp('expires_at')->index();

            // Quantas vezes o usuário tentou verificar este código
            // Máximo de tentativas configurável via .env
            $table->tinyInteger('attempts')->default(0);

            // Marca o código como usado para evitar reutilização
            $table->boolean('used')->default(false)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authentication.otp_codes');
    }
};