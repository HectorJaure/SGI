<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    use HasFactory;

    protected $fillable = [
        'lugar',
        'actividad', 
        'peligro',
        'tipo_riesgo',
        'otros_factores', 
        'clasificacion',
        'tiempo_exposicion',
        'personas_expuestas',
        'probabilidad_ocurrencia',
        'consecuencia_infraestructura',
        'consecuencia_personas',
        'significancia',
        'nivel_riesgo'
    ];

    protected $attributes = [
        'otros_factores' => 'No aplica', 
    ];

    protected $casts = [
        'tiempo_exposicion' => 'float',
        'personas_expuestas' => 'float',
        'probabilidad_ocurrencia' => 'float',
        'consecuencia_personas' => 'float',
        'consecuencia_infraestructura' => 'float',
        'significancia' => 'float',
    ];
}