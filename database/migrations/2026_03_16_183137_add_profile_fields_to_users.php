<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // CPF do usuário — único por pessoa
            $table->string('cpf', 11)->nullable()->unique()->after('name');

            // Data de nascimento — validamos idade mínima de 18 anos
            $table->date('birth_date')->nullable()->after('cpf');

            // Email opcional para usuário comum, obrigatório para bar_owner
            // já existe na tabela, só garantimos que aceita null
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'birth_date']);
        });
    }
};