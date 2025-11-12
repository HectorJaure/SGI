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
        
        // Obtener notificaciones no leídas (solo para administradores)
        $notificaciones = [];
        $unreadNotificationsCount = 0;
        
        if (session('user_rol') === 'Administrador') {
            $notificaciones = Notification::where('estado', 'no_leida')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            $unreadNotificationsCount = Notification::where('estado', 'no_leida')->count();
        }

        // Métricas de riesgos
        $metricas = [
            'total_riesgos' => $riesgos->count(),
            'riesgos_alto_impacto' => $riesgos->where('nivel_riesgo', 'alta')->count(),
            'riesgos_mediano_impacto' => $riesgos->where('nivel_riesgo', 'media')->count(),
            'riesgos_bajo_impacto' => $riesgos->where('nivel_riesgo', 'baja')->count(),
            'riesgos_muy_alto' => $riesgos->where('nivel_riesgo', 'muy-alta')->count(),
        ];

        // Métricas de requisitos legales
        $totalRequisitos = $requisitos->count();
        $requisitosCumplidos = $requisitos->where('cumplimiento', 'si')->count();
        $porcentajeCumplimiento = $totalRequisitos > 0 ? round(($requisitosCumplidos / $totalRequisitos) * 100) : 0;

        $estado_sgsst = [
            'general' => $porcentajeCumplimiento >= 80 ? 'Cumplimiento Satisfactorio' : 
                        ($porcentajeCumplimiento >= 60 ? 'Cumplimiento Parcial' : 'Cumplimiento Insuficiente'),
            'nivel_cumplimiento' => $porcentajeCumplimiento,
            'ultima_auditoria' => '2024-02-15',
            'proxima_auditoria' => '2024-08-15',
            'color_estado' => $porcentajeCumplimiento >= 80 ? '#27ae60' : 
                             ($porcentajeCumplimiento >= 60 ? '#f39c12' : '#e74c3c')
        ];

        // Alertas urgentes basadas en datos reales
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

        // Alertas de riesgos altos o muy altos
        $riesgosUrgentes = $riesgos->whereIn('nivel_riesgo', ['alta', 'muy-alta'])->take(3);
        
        foreach ($riesgosUrgentes as $riesgo) {
            $alertas[] = [
                'tipo' => 'Riesgo',
                'titulo' => 'Riesgo ' . ($riesgo->nivel_riesgo == 'alta' ? 'alto' : 'muy alto') . ' identificado',
                'descripcion' => $riesgo->peligro . ' en ' . $riesgo->lugar,
                'fecha' => $riesgo->created_at->format('Y-m-d'),
                'prioridad' => 'Alta'
            ];
        }

        // Alertas de requisitos no cumplidos
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

        // Si no hay alertas, mostrar mensaje informativo
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