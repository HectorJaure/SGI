<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use Illuminate\Http\Request;

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

        if ($request->has('otros_factores') && $request->otros_factores != '') {
            $query->where('otros_factores', $request->otros_factores);
        }

        if ($request->has('otros_factores') && $request->otros_factores != '') {
            $queryForCounters->where('otros_factores', $request->otros_factores);
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

        // Verificar si ya existe un riesgo con los mismos 3 campos
        $riesgoExistente = Risk::where('lugar', $request->lugar)
            ->where('actividad', $request->actividad)
            ->where('peligro', $request->peligro)
            ->first();

        if ($riesgoExistente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe un riesgo registrado con el mismo lugar, actividad y descripción del peligro.');
        }

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

        // Verificar si ya existe otro riesgo con los mismos 3 campos (excluyendo el actual)
        $riesgoExistente = Risk::where('lugar', $request->lugar)
            ->where('actividad', $request->actividad)
            ->where('peligro', $request->peligro)
            ->where('id', '!=', $id) // Excluir el riesgo actual
            ->first();

        if ($riesgoExistente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe otro riesgo registrado con el mismo lugar, actividad y descripción del peligro.');
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

}