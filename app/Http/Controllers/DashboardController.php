<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RequisitoLegal;
use App\Models\Notification;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $riesgos = Risk::all();
        $requisitos = RequisitoLegal::all();
        $usuarios = User::all();
        
        $notificaciones = [];
        $unreadNotificationsCount = 0;
        
        if (session('user_rol') === 'Administrador') {
            $notificaciones = Notification::where('estado', 'no_leida')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            $unreadNotificationsCount = Notification::where('estado', 'no_leida')->count();
        }

        $metricas = [
            'total_riesgos' => $riesgos->count(),
            'riesgos_alto_impacto' => $riesgos->where('nivel_riesgo', 'alta')->count(),
            'riesgos_mediano_impacto' => $riesgos->where('nivel_riesgo', 'media')->count(),
            'riesgos_bajo_impacto' => $riesgos->where('nivel_riesgo', 'baja')->count(),
        ];

        $totalRequisitos = $requisitos->count();
        $requisitosNoCumplidos = $requisitos->where('cumplimiento', 'no')->count();

        $estado_sgsst = [
            'nivel_cumplimiento' =>'Faltan por cumplir ' . $requisitosNoCumplidos . ' requisitos legales',
            'color_estado' => $requisitosNoCumplidos >= 10 ? 'red' : ($requisitosNoCumplidos >= 5 ? 'orange' : 'green'),
        ];

        $alertas_urgentes = $this->generarAlertasUrgentes($riesgos, $requisitos);

        return view('dashboard', compact(
            'metricas', 
            'estado_sgsst', 
            'alertas_urgentes',
            'notificaciones',
            'unreadNotificationsCount'
        ));
    }

    private function generarAlertasUrgentes($riesgos, $requisitos)
    {
        $alertas = [];

        $riesgosUrgentes = $riesgos->where('nivel_riesgo', 'alta')->take(3);
        
        foreach ($riesgosUrgentes as $riesgo) {
            $alertas[] = [
                'tipo' => 'Riesgo',
                'titulo' => 'Riesgo alto identificado',
                'descripcion' => $riesgo->peligro . ' en ' . $riesgo->lugar,
                'fecha' => $riesgo->created_at->format('Y-m-d'),
                'prioridad' => 'Alta'
            ];
        }

        $requisitosPendientes = $requisitos->where('cumplimiento', 'no')
            ->where('fecha_cumplimiento', '<', now()->addDays(7))
            ->take(2);

        foreach ($requisitosPendientes as $requisito) {
            $alertas[] = [
                'tipo' => 'Requisito',
                'titulo' => 'Requisito pendiente de cumplimiento',
                'descripcion' => $requisito->titulo . ' - ' . $requisito->norma,
                'fecha' => $requisito->fecha_cumplimiento->format('Y-m-d'),
                'prioridad' => $requisito->fecha_cumplimiento < now() ? 'Alta' : 'Media'
            ];
        }

        if (empty($alertas)) {
            $alertas[] = [
                'tipo' => 'Sistema',
                'titulo' => 'Estado del sistema',
                'descripcion' => 'No hay alertas urgentes en este momento',
                'fecha' => now()->format('Y-m-d'),
                'prioridad' => 'Baja'
            ];
        }

        return $alertas;
    }
}