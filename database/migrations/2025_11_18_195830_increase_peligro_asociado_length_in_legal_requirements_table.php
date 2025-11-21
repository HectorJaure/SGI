<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('legal_requirements', function (Blueprint $table) {
            // Cambiar el tipo de columna a TEXT para campos largos
            $table->text('peligro_asociado')->change();
            $table->text('descripcion')->change();
            $table->text('acciones_no')->change();
            $table->text('evidencia')->change();
        });
    }

    public function down()
    {
        Schema::table('legal_requirements', function (Blueprint $table) {
            // Revertir los cambios si es necesario
            $table->string('peligro_asociado', 255)->change();
            $table->string('descripcion', 255)->change();
            $table->string('acciones_no', 255)->change();
            $table->string('evidencia', 255)->change();
        });
    }
};