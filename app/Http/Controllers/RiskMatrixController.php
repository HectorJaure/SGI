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

        if ($request->has('lugar') && !empty($request->lugar)) {
            $query->where('lugar', 'LIKE', '%' . $request->lugar . '%');
        }

        if ($request->has('actividad') && !empty($request->actividad)) {
            $query->where('actividad', 'LIKE', '%' . $request->actividad . '%');
        }

        if ($request->has('tipo_riesgo') && !empty($request->tipo_riesgo)) {
            $query->where('tipo_riesgo', $request->tipo_riesgo);
        }

        if ($request->has('clasificacion') && !empty($request->clasificacion)) {
            $query->where('clasificacion', $request->clasificacion);
        }

        if ($request->has('nivel_riesgo') && !empty($request->nivel_riesgo)) {
            $query->where('nivel_riesgo', $request->nivel_riesgo);
        }

        $riesgosQuery = $query->orderBy('lugar')->orderBy('actividad');

        $lugares = Risk::distinct('lugar')
            ->whereNotNull('lugar')
            ->where('lugar', '!=', '')
            ->orderBy('lugar')
            ->pluck('lugar')
            ->filter();

        $actividades = Risk::distinct('actividad')
            ->whereNotNull('actividad')
            ->where('actividad', '!=', '')
            ->orderBy('actividad')
            ->pluck('actividad')
            ->filter();

        $contadores = [
            'bajo' => Risk::where('nivel_riesgo', 'baja')->count(),
            'medio' => Risk::where('nivel_riesgo', 'media')->count(),
            'alto' => Risk::where('nivel_riesgo', 'alta')->count(),
        ];

        $totalRiesgos = Risk::count();

        $perPage = $request->get('per_page', 'all');
        
        if ($perPage === 'all') {
            $riesgos = $riesgosQuery->get();
        } else {
            $riesgos = $riesgosQuery->paginate($perPage);
            $riesgos->appends($request->except('page'));
        }

        $riesgosParaAgrupar = $perPage === 'all' ? $riesgos : ($riesgos instanceof \Illuminate\Pagination\LengthAwarePaginator ? $riesgos->getCollection() : $riesgos);
        
        $riesgosAgrupados = [];
        foreach ($riesgosParaAgrupar as $riesgo) {
            $keyLugar = $riesgo->lugar;
            
            if (!isset($riesgosAgrupados[$keyLugar])) {
                $riesgosAgrupados[$keyLugar] = [
                    'lugar' => $riesgo->lugar,
                    'actividades' => []
                ];
            }

            $keyActividad = $riesgo->actividad;
            if (!isset($riesgosAgrupados[$keyLugar]['actividades'][$keyActividad])) {
                $riesgosAgrupados[$keyLugar]['actividades'][$keyActividad] = [
                    'actividad' => $riesgo->actividad,
                    'riesgos' => []
                ];
            }
            
            $riesgosAgrupados[$keyLugar]['actividades'][$keyActividad]['riesgos'][] = $riesgo;
        }

        return view('risks.matrix', compact(
            'riesgos', 
            'riesgosAgrupados', 
            'lugares', 
            'actividades', 
            'contadores',
            'totalRiesgos',
            'perPage'
        ));
    }

    // ... el resto de los métodos se mantiene igual
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

        $riesgoExistente = Risk::where('lugar', $request->lugar)
            ->where('actividad', $request->actividad)
            ->where('peligro', $request->peligro)
            ->first();

        if ($riesgoExistente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe un riesgo registrado con el mismo lugar, actividad y descripción del peligro.');
        }

        $probabilidadTotal = $request->tiempo_exposicion + $request->personas_expuestas + $request->probabilidad_ocurrencia;
        $consecuenciaTotal = $request->consecuencia_infraestructura + $request->consecuencia_personas;
        $significancia = $probabilidadTotal * $consecuenciaTotal;

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

        $tipoNotificacion = $nivelRiesgo == 'alta' ? 'urgent' : 'warning';
        
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

        $riesgoExistente = Risk::where('lugar', $request->lugar)
            ->where('actividad', $request->actividad)
            ->where('peligro', $request->peligro)
            ->where('id', '!=', $id)
            ->first();

        if ($riesgoExistente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe otro riesgo registrado con el mismo lugar, actividad y descripción del peligro.');
        }

        $probabilidadTotal = $request->tiempo_exposicion + $request->personas_expuestas + $request->probabilidad_ocurrencia;
        $consecuenciaTotal = $request->consecuencia_infraestructura + $request->consecuencia_personas;
        $significancia = $probabilidadTotal * $consecuenciaTotal;

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
        return 'alta';
    }

    private function formatearNivelRiesgo($nivel)
    {
        $niveles = [
            'baja' => 'Bajo',
            'media' => 'Medio', 
            'alta' => 'Alto'
        ];
        
        return $niveles[$nivel] ?? $nivel;
    }
}