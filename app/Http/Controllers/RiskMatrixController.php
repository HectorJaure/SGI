<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Http\Controllers\NotificationController;

class RiskMatrixController extends Controller
{
    public function matrix(Request $request)
    {
        $query = Risk::query();
        
        // Aplicar filtros
        if ($request->has('lugar') && $request->lugar != '') {
            $query->where('lugar', $request->lugar);
        }
        
        if ($request->has('actividad') && $request->actividad != '') {
            $query->where('actividad', $request->actividad);
        }
        
        if ($request->has('tipo_riesgo') && $request->tipo_riesgo != '') {
            $query->where('tipo_riesgo', $request->tipo_riesgo);
        }
        
        if ($request->has('clasificacion') && $request->clasificacion != '') {
            $query->where('clasificacion', $request->clasificacion);
        }
        
        if ($request->has('nivel_riesgo') && $request->nivel_riesgo != '') {
            $query->where('nivel_riesgo', $request->nivel_riesgo);
        }
        
        $perPage = $request->get('per_page', 10);
        $riesgos = $query->orderBy('lugar')->orderBy('actividad')->paginate($perPage);
        
        // Agrupar riesgos por lugar y actividad para la vista
        $riesgosAgrupados = [];
        foreach ($riesgos as $riesgo) {
            $clave = $riesgo->lugar . '|' . $riesgo->actividad;
            if (!isset($riesgosAgrupados[$clave])) {
                $riesgosAgrupados[$clave] = [
                    'lugar' => $riesgo->lugar,
                    'actividad' => $riesgo->actividad,
                    'riesgos' => []
                ];
            }
            $riesgosAgrupados[$clave]['riesgos'][] = $riesgo;
        }
        
        // Para los contadores necesitamos todos los registros (sin paginación)
        $queryForCounters = Risk::query();
        
        // Aplicar los mismos filtros para los contadores
        if ($request->has('lugar') && $request->lugar != '') {
            $queryForCounters->where('lugar', $request->lugar);
        }
        
        if ($request->has('actividad') && $request->actividad != '') {
            $queryForCounters->where('actividad', $request->actividad);
        }
        
        if ($request->has('tipo_riesgo') && $request->tipo_riesgo != '') {
            $queryForCounters->where('tipo_riesgo', $request->tipo_riesgo);
        }
        
        if ($request->has('clasificacion') && $request->clasificacion != '') {
            $queryForCounters->where('clasificacion', $request->clasificacion);
        }
        
        if ($request->has('nivel_riesgo') && $request->nivel_riesgo != '') {
            $queryForCounters->where('nivel_riesgo', $request->nivel_riesgo);
        }
        
        $riesgosForCounters = $queryForCounters->get();
        
        // Obtener datos para los filtros
        $lugares = Risk::distinct('lugar')->pluck('lugar')->filter();
        $actividades = Risk::distinct('actividad')->pluck('actividad')->filter();
        
        // Calcular contadores con los datos filtrados
        $contadores = [
            'bajo' => $riesgosForCounters->where('nivel_riesgo', 'baja')->count(),
            'medio' => $riesgosForCounters->where('nivel_riesgo', 'media')->count(),
            'alto' => $riesgosForCounters->where('nivel_riesgo', 'alta')->count(),
            'muy_alto' => $riesgosForCounters->where('nivel_riesgo', 'muy-alta')->count(),
        ];
        
        return view('risks.matrix', compact('riesgos', 'riesgosAgrupados', 'contadores', 'lugares', 'actividades'));
    }

    public function create()
    {
        $actividades = Risk::distinct('actividad')->orderBy('actividad')->pluck('actividad')->filter();
        $lugares = Risk::distinct('lugar')->orderBy('lugar')->pluck('lugar')->filter();
        
        return view('risks.create', compact('actividades', 'lugares'));
    }

    public function edit($id)
    {
        $riesgo = Risk::findOrFail($id);
        $actividades = Risk::distinct('actividad')->orderBy('actividad')->pluck('actividad')->filter();
        $lugares = Risk::distinct('lugar')->orderBy('lugar')->pluck('lugar')->filter();
        
        return view('risks.edit', compact('riesgo', 'actividades', 'lugares'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lugar' => 'required|string|max:255',
            'actividad' => 'required|string|max:255',
            'peligro' => 'required|string|max:1000',
            'tipo_riesgo' => 'required|in:Interno,Externo',
            'otros_factores' => 'nullable|string|max:255',
            'clasificacion' => 'required|in:Seguridad,Salud',
            'tiempo_exposicion' => 'required|numeric|min:0|max:5',
            'personas_expuestas' => 'required|numeric|min:0|max:5',
            'probabilidad_ocurrencia' => 'required|numeric|min:0|max:5',
            'consecuencia_personas' => 'required|numeric|min:0|max:5',
            'consecuencia_infraestructura' => 'required|numeric|min:0|max:3',
        ]);

        // Calcular significancia según el Excel
        $probabilidadTotal = $request->tiempo_exposicion + $request->personas_expuestas + $request->probabilidad_ocurrencia;
        $consecuenciaTotal = $request->consecuencia_infraestructura + $request->consecuencia_personas;
        $significancia = $probabilidadTotal * $consecuenciaTotal;

        // Determinar nivel de riesgo según los límites del Excel
        $nivelRiesgo = $this->determinarNivelRiesgo($significancia);

        if (empty($request->otros_factores)) {
            $request->merge(['otros_factores' => 'No aplica']);
        }

        $riesgo = Risk::create([
            'lugar' => $request->lugar,
            'actividad' => $request->actividad,
            'peligro' => $request->peligro,
            'tipo_riesgo' => $request->tipo_riesgo,
            'otros_factores' => $request->otros_factores,
            'clasificacion' => $request->clasificacion,
            'tiempo_exposicion' => $request->tiempo_exposicion,
            'personas_expuestas' => $request->personas_expuestas,
            'probabilidad_ocurrencia' => $request->probabilidad_ocurrencia,
            'consecuencia_personas' => $request->consecuencia_personas,
            'consecuencia_infraestructura' => $request->consecuencia_infraestructura,
            'significancia' => $significancia,
            'nivel_riesgo' => $nivelRiesgo
        ]);

        // NOTIFICACIÓN AUTOMÁTICA - Nuevo riesgo creado
        $tipoNotificacion = $nivelRiesgo == 'alta' || $nivelRiesgo == 'muy-alta' ? 'urgent' : 'warning';
        
        NotificationController::createNotification(
            'Nuevo Riesgo Identificado',
            "Se ha registrado un nuevo riesgo en {$riesgo->lugar} - {$riesgo->actividad}. Nivel: " . $this->formatearNivelRiesgo($nivelRiesgo),
            $tipoNotificacion
        );

        return redirect()->route('risks.matrix')->with('success', 'Riesgo registrado correctamente');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lugar' => 'required|string|max:255',
            'actividad' => 'required|string|max:255',
            'peligro' => 'required|string|max:1000',
            'tipo_riesgo' => 'required|in:Interno,Externo',
            'otros_factores' => 'nullable|string|max:255',
            'clasificacion' => 'required|in:Seguridad,Salud',
            'tiempo_exposicion' => 'required|numeric|min:0|max:5',
            'personas_expuestas' => 'required|numeric|min:0|max:5',
            'probabilidad_ocurrencia' => 'required|numeric|min:0|max:5',
            'consecuencia_personas' => 'required|numeric|min:0|max:5',
            'consecuencia_infraestructura' => 'required|numeric|min:0|max:3',
        ]);

        $riesgo = Risk::findOrFail($id);

        if (empty($request->otros_factores)) {
            $request->merge(['otros_factores' => 'No aplica']);
        }

        // Recalcular significancia según el Excel
        $probabilidadTotal = $request->tiempo_exposicion + $request->personas_expuestas + $request->probabilidad_ocurrencia;
        $consecuenciaTotal = $request->consecuencia_infraestructura + $request->consecuencia_personas;
        $significancia = $probabilidadTotal * $consecuenciaTotal;

        // Determinar nivel de riesgo según los límites del Excel
        $nivelRiesgo = $this->determinarNivelRiesgo($significancia);

        $riesgo->update([
            'lugar' => $request->lugar,
            'actividad' => $request->actividad,
            'peligro' => $request->peligro,
            'tipo_riesgo' => $request->tipo_riesgo,
            'otros_factores' => $request->otros_factores,
            'clasificacion' => $request->clasificacion,
            'tiempo_exposicion' => $request->tiempo_exposicion,
            'personas_expuestas' => $request->personas_expuestas,
            'probabilidad_ocurrencia' => $request->probabilidad_ocurrencia,
            'consecuencia_personas' => $request->consecuencia_personas,
            'consecuencia_infraestructura' => $request->consecuencia_infraestructura,
            'significancia' => $significancia,
            'nivel_riesgo' => $nivelRiesgo
        ]);

        // NOTIFICACIÓN AUTOMÁTICA - Riesgo actualizado
        NotificationController::createNotification(
            'Riesgo Actualizado',
            "Se han modificado los datos del riesgo en {$riesgo->lugar} - {$riesgo->actividad}. Nuevo nivel: " . $this->formatearNivelRiesgo($nivelRiesgo),
            'info'
        );

        return redirect()->route('risks.matrix')->with('success', 'Riesgo actualizado correctamente');
    }

    public function destroy($id)
    {
        $riesgo = Risk::findOrFail($id);
        $lugar = $riesgo->lugar;
        $actividad = $riesgo->actividad;
        
        $riesgo->delete();

        // NOTIFICACIÓN AUTOMÁTICA - Riesgo eliminado
        NotificationController::createNotification(
            'Riesgo Eliminado',
            "Se ha eliminado el riesgo de {$lugar} - {$actividad} del sistema",
            'warning'
        );

        return redirect()->route('risks.matrix')->with('success', 'Riesgo eliminado correctamente');
    }

    private function determinarNivelRiesgo($significancia)
    {
        if ($significancia < 45) return 'baja';
        if ($significancia < 75) return 'media';
        if ($significancia < 120) return 'alta';
        return 'muy-alta';
    }

    private function formatearNivelRiesgo($nivel)
    {
        $niveles = [
            'baja' => 'Bajo',
            'media' => 'Medio', 
            'alta' => 'Alto',
            'muy-alta' => 'Muy Alto'
        ];
        
        return $niveles[$nivel] ?? $nivel;
    }

    public function exportPdf(Request $request)
    {
        // Obtener todos los riesgos agrupados por lugar y actividad
        $riesgos = Risk::orderBy('lugar')->orderBy('actividad')->get();
        
        // Agrupar riesgos para el PDF
        $riesgosAgrupados = [];
        foreach ($riesgos as $riesgo) {
            $clave = $riesgo->lugar . '|' . $riesgo->actividad;
            if (!isset($riesgosAgrupados[$clave])) {
                $riesgosAgrupados[$clave] = [
                    'lugar' => $riesgo->lugar,
                    'actividad' => $riesgo->actividad,
                    'riesgos' => []
                ];
            }
            $riesgosAgrupados[$clave]['riesgos'][] = $riesgo;
        }

        // Datos EXACTOS de las soluciones recomendadas con prioridades (del Excel)
        $solucionesRecomendadas = [
            [
                'descripcion' => 'Colocación de jaladera en credenza en S.S. Colocación de canaletas en cables sueltos. Mantenimiento de mobiliario.',
                'prioridad' => 'inmediata'
            ],
            [
                'descripcion' => 'Orden y mantenimiento de limpieza continúo en diversas áreas, principalmente las identificadas en el recodrrido. Colocación de señalética "prohibido estacionarse" y circular a estudiantes informando las áreas correspondientes para estacionarse.',
                'prioridad' => 'alta'
            ],
            [
                'descripcion' => 'Mantenimiento a infraestructura (Techo) para las goteras, colocación de señalética de precaución ante la presencia de agua. Mantenimiento y refuerzo de los plafones en las instalaciones del ITSN y señalética de prohibición de manipulación de los mismo plafones por parte de la comunidad estudiantil.',
                'prioridad' => 'media'
            ],
            [
                'descripcion' => 'Colocación de cintilla de precaución en zonas de riesgo mientras se de mantenimiento.',
                'prioridad' => 'inmediata'
            ],
            [
                'descripcion' => 'Colocación de señalética de precaución de pisos mojados. Tapar hueco en el suelo con material de albañearía en los diferentes salones. Colocación de cinta de precaución en tapadera de drenaje en estacionamiento.',
                'prioridad' => 'alta'
            ],
            [
                'descripcion' => 'Colocación de una varilla o tubo PTR de manera horizontal, uniéndose poste a poste en el barandar de las escaleras del edificio C.',
                'prioridad' => 'media'
            ],
            [
                'descripcion' => 'Colocación de señalética para restringir el a las bodegas y almacén. Tener accesos controlados bajo llave y persona (s) responsable (s).',
                'prioridad' => 'baja'
            ],
            [
                'descripcion' => 'Mantenimiento de lineas eléctricas y contacos en las diversas áreas, principalmente las señaladas en el acta.',
                'prioridad' => 'inmediata'
            ],
            [
                'descripcion' => 'Proporcionar equipo de protección (guantes) al personal de matenimiento y limpieza del instituto.',
                'prioridad' => 'alta'
            ],
            [
                'descripcion' => 'Mantenimiento de limpieza y programar fumigaciones anuales. Difundir el Plan de Respuesta ante emergencias del ITSN.',
                'prioridad' => 'media'
            ],
            [
                'descripcion' => 'Rotular recipientes advirtiendo su contenido en las bodegas, almacen y laboratorio de IIAS.',
                'prioridad' => 'baja'
            ]
        ];

        // Datos EXACTOS del seguimiento (del Excel)
        $seguimientoRecomendaciones = [
            [
                'avance' => 'Se ha mostrado avance respecto a los hallazgos anteriores',
                'causa' => ''
            ],
            [
                'avance' => 'Visualizar la prevención y no la corección de situaciones de riesgo con ayuda del Plan de Mantenimiento.',
                'causa' => ''
            ]
        ];

        $data = [
            'riesgos' => $riesgos,
            'riesgosAgrupados' => $riesgosAgrupados,
            'solucionesRecomendadas' => $solucionesRecomendadas,
            'seguimientoRecomendaciones' => $seguimientoRecomendaciones
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('risks.acta-pdf', $data);
        
        return $pdf->download('acta-verificacion-riesgos-' . date('Y-m-d') . '.pdf');
    }
}