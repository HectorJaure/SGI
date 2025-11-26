<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitoLegal extends Model
{
    use HasFactory;

    protected $table = 'legal_requirements';

    protected $fillable = [
        'categoria_norma',
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

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria_norma', $categoria);
    }

    public function getCategoriaCompletaAttribute()
    {
        $categorias = [
            'seguridad' => 'Normas de Seguridad',
            'salud' => 'Normas de Salud',
            'organizacion' => 'Normas de Organización'
        ];

        return $categorias[$this->categoria_norma] ?? 'Sin categoría';
    }
}