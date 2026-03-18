<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos de perfil estendido na tabela users.
     *
     * Inclui foto de perfil, cidade e preferências de notificação.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Foto de perfil do usuário
            $table->string('photo')->nullable()->after('birth_date');

            // Cidade do usuário para filtrar eventos próximos
            $table->string('city')->nullable()->after('photo');

            // Preferências de notificação
            $table->boolean('notify_new_events')->default(true)->after('city');
            $table->boolean('notify_event_reminder')->default(true)->after('notify_new_events');
        });
    }

    /**
     * Remove os campos adicionados caso necessário reverter.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'photo',
                'city',
                'notify_new_events',
                'notify_event_reminder',
            ]);
        });
    }
};