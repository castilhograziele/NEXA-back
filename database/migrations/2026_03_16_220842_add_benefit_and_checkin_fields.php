<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona benefício na tabela events
        Schema::table('events', function (Blueprint $table) {
            // Benefício oferecido pelo bar ao usuário inscrito
            // Ex: "1 drink grátis", "Entrada gratuita", "20% de desconto"
            $table->string('benefit')->nullable()->after('age_restriction');
        });

        // Adiciona check-in na tabela event_subscriptions
        Schema::table('event_subscriptions', function (Blueprint $table) {
            // Indica se o usuário fez check-in no evento
            $table->boolean('checked_in')->default(false)->after('user_id');

            // Data e hora do check-in
            $table->timestamp('checked_in_at')->nullable()->after('checked_in');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('benefit');
        });

        Schema::table('event_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['checked_in', 'checked_in_at']);
        });
    }
};