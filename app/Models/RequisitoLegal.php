<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitoLegal extends Model
{
    use HasFactory;

    protected $table = 'legal_requirements';

    protected $fillable = [
        'norma',
        'titulo', 
        'tipo_requisito',
        'numero_requisito',
        'descripcion',
        'cumplimiento',
        'evidencia',
        'acciones_no',
        'peligro_asociado',
        'fecha_cumplimiento',
        'responsables',
        'frecuencia_control',
        'responsable_control'
    ];

    protected $casts = [
        'fecha_cumplimiento' => 'date',
    ];
}