<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('main.users', function (Blueprint $table) {
            // Telefone é o identificador principal no novo sistema OTP
            $table->string('phone', 20)->unique()->nullable()->after('name');

            // E-mail passa a ser opcional — obrigatório apenas para bar_owner
            $table->string('email')->nullable()->change();

            // Senha removida do fluxo — OTP substitui autenticação por senha
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('main.users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};