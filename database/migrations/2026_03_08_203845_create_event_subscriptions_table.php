<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_subscriptions', function (Blueprint $table) {
            $table->id();

            // relacionamento com o evento
            $table->foreignId('event_id')
                  ->constrained()
                  ->onDelete('cascade');

            // relacionamento com o usuário inscrito
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // impede que o mesmo usuário se inscreva duas vezes no mesmo evento
            $table->unique(['event_id', 'user_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_subscriptions');
    }
};