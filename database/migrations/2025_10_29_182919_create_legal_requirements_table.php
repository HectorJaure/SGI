<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('legal_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('norma');
            $table->string('titulo');
            $table->string('tipo_requisito');
            $table->string('numero_requisito');
            $table->text('descripcion');
            $table->enum('cumplimiento', ['si', 'no']);
            $table->text('evidencia')->nullable();
            $table->text('acciones_no')->nullable();
            $table->string('peligro_asociado');
            $table->date('fecha_cumplimiento');
            $table->string('responsables');
            $table->string('frecuencia_control');
            $table->string('responsable_control');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('legal_requirements');
    }
};