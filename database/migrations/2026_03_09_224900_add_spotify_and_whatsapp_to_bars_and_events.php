<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona link do WhatsApp na tabela bars
        Schema::table('bars', function (Blueprint $table) {
            // link de contato ou reserva via WhatsApp (apenas premium)
            $table->string('whatsapp')->nullable()->after('plan');
        });

        // Adiciona link do Spotify na tabela events
        Schema::table('events', function (Blueprint $table) {
            // playlist do evento no Spotify (apenas premium)
            $table->string('spotify_url')->nullable()->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('bars', function (Blueprint $table) {
            $table->dropColumn('whatsapp');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('spotify_url');
        });
    }
};