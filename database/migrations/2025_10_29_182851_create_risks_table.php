<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('lugar');
            $table->string('actividad');
            $table->text('peligro');
            $table->enum('tipo_riesgo', ['Interno', 'Externo']);
            $table->enum('clasificacion', ['Seguridad', 'Salud']);
            $table->decimal('tiempo_exposicion', 3, 1);
            $table->decimal('personas_expuestas', 3, 1);
            $table->decimal('probabilidad_ocurrencia', 3, 1);
            $table->decimal('consecuencia_personas', 3, 1);
            $table->decimal('consecuencia_infraestructura', 3, 1);
            $table->decimal('significancia', 8, 2);
            $table->string('nivel_riesgo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('risks');
    }
};
