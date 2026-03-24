<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona foto de perfil no bar e cria tabela de galeria de fotos.
     *
     * A foto de perfil é um campo simples no bar.
     * A galeria permite múltiplas fotos do ambiente.
     */
    public function up(): void
    {
        // Adiciona foto de perfil na tabela bars
        Schema::table('bars', function (Blueprint $table) {
            // Caminho da foto de perfil do bar no storage
            $table->string('photo')->nullable()->after('razao_social');
        });

        // Cria tabela de galeria de fotos do bar
        Schema::create('bar_photos', function (Blueprint $table) {
            $table->id();

            // Relacionamento com o bar
            $table->foreignId('bar_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Caminho da foto no storage
            $table->string('path');

            // Legenda opcional da foto
            $table->string('caption')->nullable();

            // Ordem de exibição na galeria
            $table->unsignedTinyInteger('order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverte as alterações da migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('bar_photos');

        Schema::table('bars', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};