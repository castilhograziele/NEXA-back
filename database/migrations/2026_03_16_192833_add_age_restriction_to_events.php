<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Classificação etária do evento
            // none = livre, 18 = maiores de 18, 21 = maiores de 21
            $table->enum('age_restriction', ['none', '18', '21'])
                  ->default('none')
                  ->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('age_restriction');
        });
    }
};