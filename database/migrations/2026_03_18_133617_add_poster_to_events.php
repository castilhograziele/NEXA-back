<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campo de cartaz/flyer na tabela events.
     *
     * O cartaz é a imagem de divulgação do evento.
     * Disponível apenas para bares com plano premium.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Caminho do cartaz/flyer do evento no storage
            $table->string('poster')->nullable()->after('benefit');
        });
    }

    /**
     * Remove o campo poster caso necessário reverter.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('poster');
        });
    }
};