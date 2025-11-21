<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('legal_requirements', function (Blueprint $table) {
            // Hacer nullable las columnas que pueden ser opcionales
            $table->text('peligro_asociado')->nullable()->change();
            $table->text('evidencia')->nullable()->change();
            $table->text('acciones_no')->nullable()->change();
            $table->string('cumplimiento', 10)->nullable()->change();
            $table->date('fecha_cumplimiento')->nullable()->change();
            $table->string('responsables', 255)->nullable()->change();
            $table->string('frecuencia_control', 100)->nullable()->change();
            $table->string('responsable_control', 255)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('legal_requirements', function (Blueprint $table) {
            // Revertir los cambios
            $table->text('peligro_asociado')->nullable(false)->change();
            $table->text('evidencia')->nullable(false)->change();
            $table->text('acciones_no')->nullable(false)->change();
            $table->string('cumplimiento', 10)->nullable(false)->change();
            $table->date('fecha_cumplimiento')->nullable(false)->change();
            $table->string('responsables', 255)->nullable(false)->change();
            $table->string('frecuencia_control', 100)->nullable(false)->change();
            $table->string('responsable_control', 255)->nullable(false)->change();
        });
    }
};