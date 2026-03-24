<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria tabela de avaliações de eventos.
     *
     * Apenas usuários que fizeram check-in podem avaliar.
     * Apenas após a data do evento.
     * Não pode ser editada após enviada.
     */
    public function up(): void
    {
        Schema::create('event_reviews', function (Blueprint $table) {
            $table->id();

            // Relacionamento com o evento avaliado
            $table->foreignId('event_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Relacionamento com o usuário que avaliou
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Classificação geral de 1 a 5 estrelas
            $table->unsignedTinyInteger('stars');

            // Avaliações específicas de 1 a 5
            $table->unsignedTinyInteger('music_rating')->nullable();
            $table->unsignedTinyInteger('drink_rating')->nullable();
            $table->unsignedTinyInteger('cleanliness_rating')->nullable();

            // Consideração livre de até 200 caracteres
            $table->string('comment', 200)->nullable();

            // Garante que cada usuário avalia um evento apenas uma vez
            $table->unique(['event_id', 'user_id']);

            $table->timestamps();
        });
    }

    /**
     * Remove a tabela de avaliações caso necessário reverter.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_reviews');
    }
};