<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campo de estilo musical na tabela events.
     *
     * Campo texto livre para o bar informar o que vai tocar no evento.
     * Ex: "Lady Gaga, Britney Spears", "MPB clássica", "Rock pesado"
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Estilo musical ou artistas que vão tocar no evento
            $table->string('music_style')->nullable()->after('benefit');
        });
    }

    /**
     * Remove o campo music_style caso necessário reverter.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('music_style');
        });
    }
};