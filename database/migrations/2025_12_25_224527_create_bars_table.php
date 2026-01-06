<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * roda quando executamos php artisan migrate
     */
    public function up(): void
    {
        Schema::create('bars', function (Blueprint $table) {
            $table->id(); // id único do bar

            // relacionamento 1:1 com users
            // cada bar pertence a um usuário
            // unique garante que um usuário só tenha um bar
            // onDelete cascade apaga o bar se o usuário for apagado
            $table->foreignId('user_id')
                  ->constrained()
                  ->unique()
                  ->onDelete('cascade');

            // nome do bar
            $table->string('name');

            // cnpj do bar, não pode repetir
            $table->string('cnpj')->unique();

            // telefone do bar (opcional)
            $table->string('phone')->nullable();

            // cidade do bar (opcional)
            $table->string('city')->nullable();

            // endereço do bar (opcional)
            $table->string('address')->nullable();

            // instagram do bar (opcional)
            $table->string('instagram')->nullable();

            // descrição do bar (opcional)
            $table->text('description')->nullable();

            // created_at e updated_at
            $table->timestamps();
        });
    }

    /**
     * roda quando executamos php artisan migrate:rollback
     */
    public function down(): void
    {
        // remove a tabela bars
        Schema::dropIfExists('bars');
    }
};
