<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona razão social na tabela bars.
     *
     * Campo opcional — bares podem não ter CNPJ/razão social formal,
     * especialmente em fase inicial de cadastro.
     */
    public function up(): void
    {
        Schema::table('bars', function (Blueprint $table) {
            // Razão social do estabelecimento (opcional)
            $table->string('razao_social')->nullable()->after('name');
        });
    }

    /**
     * Remove a coluna razao_social caso necessário reverter.
     */
    public function down(): void
    {
        Schema::table('bars', function (Blueprint $table) {
            $table->dropColumn('razao_social');
        });
    }
};