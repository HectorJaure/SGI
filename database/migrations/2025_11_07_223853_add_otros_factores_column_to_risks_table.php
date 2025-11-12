<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Verificar si la columna ya existe para evitar errores
        $hasColumn = Schema::hasColumn('risks', 'otros_factores');
        
        if (!$hasColumn) {
            Schema::table('risks', function (Blueprint $table) {
                $table->string('otros_factores')->nullable()->default('No aplica')->after('tipo_riesgo');
            });
        }
    }

    public function down()
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->dropColumn('otros_factores');
        });
    }
};