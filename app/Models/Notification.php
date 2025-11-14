<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion', 
        'tipo',
        'estado',
        'remitente',
        'usuario_accion'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getCreatedAtCorregidoAttribute()
    {
        return $this->created_at->subHours(6);
    }

    public function getCreatedAtHumansCorregidoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}