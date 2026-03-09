<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_views', function (Blueprint $table) {
            $table->id();

            // relacionamento com o evento visualizado
            $table->foreignId('event_id')
                  ->constrained()
                  ->onDelete('cascade');

            // usuário logado que visualizou (opcional — visitantes não têm id)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            // ip do visitante para evitar contagem duplicada
            $table->string('ip_address');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_views');
    }
};