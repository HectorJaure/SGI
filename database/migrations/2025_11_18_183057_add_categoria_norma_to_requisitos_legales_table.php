<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('legal_requirements', function (Blueprint $table) {
        $table->enum('categoria_norma', ['seguridad', 'salud', 'organizacion'])
              ->default('seguridad')
              ->after('norma');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_requirements', function (Blueprint $table) {
            $table->dropColumn('categoria_norma');
        });
    }
};
