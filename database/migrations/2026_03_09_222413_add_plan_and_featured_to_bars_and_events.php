<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona o plano na tabela bars
        Schema::table('bars', function (Blueprint $table) {
            // free = plano gratuito, premium = plano pago
            $table->enum('plan', ['free', 'premium'])
                  ->default('free')
                  ->after('description');
        });

        // Adiciona o destaque na tabela events
        Schema::table('events', function (Blueprint $table) {
            // true = evento aparece em "Não pode perder"
            $table->boolean('is_featured')
                  ->default(false)
                  ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('bars', function (Blueprint $table) {
            $table->dropColumn('plan');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};