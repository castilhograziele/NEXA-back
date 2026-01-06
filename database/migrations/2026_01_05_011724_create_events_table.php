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
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // id único do evento

            // relacionamento com o bar
            // cada evento pertence a um bar
            // se o bar for apagado, os eventos também são
            $table->foreignId('bar_id')
                  ->constrained()
                  ->onDelete('cascade');

            // título do evento
            $table->string('title');

            // descrição do evento (opcional)
            $table->text('description')->nullable();

            // data do evento (ex: 2026-01-10)
            $table->date('event_date');

            // horário do evento (ex: 22:00)
            $table->time('event_time');

            // categoria do evento (ex: samba, eletrônico, rock)
            $table->string('category');

            // define se o evento está ativo ou não
            // útil para esconder eventos sem deletar
            $table->boolean('is_active')->default(true);

            // created_at e updated_at
            $table->timestamps();
        });
    }

    /**
     * roda quando executamos php artisan migrate:rollback
     */
    public function down(): void
    {
        // remove a tabela events
        Schema::dropIfExists('events');
    }
};
