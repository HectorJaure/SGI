<?php

namespace App\Http\Controllers;

use App\Models\RequisitoLegal;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RequisitosImport;

class RequisitoLegalController extends Controller
{
    public function index(Request $request)
    {
        $query = RequisitoLegal::query()->orderBy('categoria_norma')->orderBy('norma');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', '%'.$request->search.'%')
                  ->orWhere('descripcion', 'like', '%'.$request->search.'%')
                  ->orWhere('norma', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->has('cumplimiento') && $request->cumplimiento != '') {
            if ($request->cumplimiento == 'null') {
                $query->whereNull('cumplimiento');
            } else {
                $query->where('cumplimiento', $request->cumplimiento);
            }
        }

        if ($request->has('norma') && $request->norma != '') {
            $query->where('norma', $request->norma);
        }

        if ($request->has('categoria_norma') && $request->categoria_norma != '') {
            $query->where('categoria_norma', $request->categoria_norma);
        }
        
        if ($request->has('tipo_requisito') && $request->tipo_requisito != '') {
            $query->where('tipo_requisito', $request->tipo_requisito);
        }
        
        if ($request->has('peligro_asociado') && $request->peligro_asociado != '') {
            $query->where('peligro_asociado', 'like', '%'.$request->peligro_asociado.'%');
        }

        if ($request->has('fecha_cumplimiento') && $request->fecha_cumplimiento != '') {
            $query->whereDate('fecha_cumplimiento', $request->fecha_cumplimiento);
        }

        if ($request->has('responsable') && $request->responsable != '') {
            $query->where('responsables', 'like', '%'.$request->responsable.'%');
        }

        $normas = RequisitoLegal::distinct('norma')->pluck('norma')->filter();

        $tiposRequisito = RequisitoLegal::distinct('tipo_requisito')->pluck('tipo_requisito')->filter();

        $categoriasNorma = [
            'seguridad' => 'Normas de Seguridad',
            'salud' => 'Normas de Salud', 
            'organizacion' => 'Normas de Organización'
        ];

        $totalRequisitos = $query->count();
        $cumplidos = (clone $query)->where('cumplimiento', 'si')->count();
        $noCumplidos = (clone $query)->where('cumplimiento', 'no')->count();
        $sinEvaluar = (clone $query)->whereNull('cumplimiento')->count();

        $perPage = $request->get('per_page', 'all');
        
        if ($perPage === 'all') {
            $requisitos = $query->get();
        } else {
            $requisitos = $query->paginate($perPage);
            $requisitos->appends($request->except('page'));
        }

        $requisitosParaAgrupar = $perPage === 'all' ? $requisitos : ($requisitos instanceof \Illuminate\Pagination\LengthAwarePaginator ? $requisitos->getCollection() : $requisitos);
        
        $requisitosAgrupados = $this->agruparPorCategoriaNorma($requisitosParaAgrupar);
        
        return view('requisitos-legales.index', compact(
            'requisitos', 
            'normas', 
            'tiposRequisito', 
            'requisitosAgrupados', 
            'categoriasNorma',
            'totalRequisitos',
            'cumplidos',
            'noCumplidos',
            'sinEvaluar',
            'perPage'
        ));
    }
    
    private function agruparPorCategoriaNorma($requisitos)
    {
        $agrupados = [
            'seguridad' => [
                'nombre' => 'Normas de Seguridad',
                'requisitos' => collect([]),
            ],
            'salud' => [
                'nombre' => 'Normas de Salud',
                'requisitos' => collect([]),
            ],
            'organizacion' => [
                'nombre' => 'Normas de Organización',
                'requisitos' => collect([]),
            ]
        ];

        foreach ($requisitos as $requisito) {
            if (isset($agrupados[$requisito->categoria_norma])) {
                $agrupados[$requisito->categoria_norma]['requisitos']->push($requisito);
            } 
        }

        return $agrupados;
    }

    public function create()
    {
        $normasExistentes = RequisitoLegal::distinct('norma')->pluck('norma')->filter();
        $tiposRequisitoExistentes = RequisitoLegal::distinct('tipo_requisito')->pluck('tipo_requisito')->filter();
        
        return view('requisitos-legales.create', compact('normasExistentes', 'tiposRequisitoExistentes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'norma' => 'required|string',
            'categoria_norma' => 'required|in:seguridad,salud,organizacion',
            'titulo' => 'required|string',
            'tipo_requisito' => 'required|string',
            'numero_requisito' => 'required|string|regex:/^[\d\.]+$/',
            'descripcion' => 'required|string',
            'peligro_asociado' => 'nullable|string',
            'cumplimiento' => 'nullable|in:si,no',
            'evidencia' => 'nullable|string',
            'acciones_no' => 'nullable|string',
            'fecha_cumplimiento' => 'nullable|date|after_or_equal:today',
            'responsables' => 'nullable|string',
            'frecuencia_control' => 'nullable|string',
            'responsable_control' => 'nullable|string',
        ]);

        $requisito = RequisitoLegal::create($validated);

        $tipoNotificacion = $requisito->cumplimiento == 'no' ? 'warning' : 'success';
        
        NotificationController::createNotification(
            'Nuevo Requisito Legal Agregado',
            "Se ha agregado el requisito: {$requisito->titulo} ({$requisito->norma}). Cumplimiento: " . ($requisito->cumplimiento == 'si' ? 'Cumplido' : 'Pendiente'),
            $tipoNotificacion
        );

        return redirect()->route('requisitos-legales.index')
                         ->with('success', 'Requisito creado correctamente.');
    }

    public function edit($id)
    {
        $requisito = RequisitoLegal::findOrFail($id);
        $normasExistentes = RequisitoLegal::distinct('norma')->pluck('norma')->filter();
        $tiposRequisitoExistentes = RequisitoLegal::distinct('tipo_requisito')->pluck('tipo_requisito')->filter();
        
        return view('requisitos-legales.edit', compact('requisito', 'normasExistentes', 'tiposRequisitoExistentes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'norma' => 'required|string',
            'categoria_norma' => 'required|in:seguridad,salud,organizacion',
            'titulo' => 'required|string',
            'tipo_requisito' => 'required|string',
            'numero_requisito' => 'required|string|regex:/^[\d\.]+$/',
            'descripcion' => 'required|string',
            'peligro_asociado' => 'nullable|string',
            'cumplimiento' => 'nullable|in:si,no',
            'evidencia' => 'nullable|string',
            'acciones_no' => 'nullable|string',
            'fecha_cumplimiento' => 'nullable|date|after_or_equal:today',
            'responsables' => 'nullable|string',
            'frecuencia_control' => 'nullable|string',
            'responsable_control' => 'nullable|string',
        ]);
        
        $requisito = RequisitoLegal::findOrFail($id);
        $requisito->update($validated);

        $tipoNotificacion = $requisito->cumplimiento == 'no' ? 'warning' : 'success';
        
        NotificationController::createNotification(
            'Requisito Legal Actualizado',
            "Se han actualizado los datos del requisito: {$requisito->titulo}. Estado: " . ($requisito->cumplimiento == 'si' ? 'Cumplido' : 'Pendiente'),
            $tipoNotificacion
        );

        return redirect()->route('requisitos-legales.index')
                         ->with('success', 'Requisito actualizado correctamente.');
    }

    public function destroy($id)
    {
        $requisito = RequisitoLegal::findOrFail($id);
        $titulo = $requisito->titulo;
        $norma = $requisito->norma;
        
        $requisito->delete();

        NotificationController::createNotification(
            'Requisito Legal Eliminado',
            "Se ha eliminado el requisito: {$titulo} ({$norma}) del sistema",
            'warning'
        );

        return redirect()->route('requisitos-legales.index')
                         ->with('success', 'Requisito eliminado correctamente.');
    }

    public function importarExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new RequisitosImport, $request->file('archivo'));

        return back()->with('success', 'Importación completada correctamente');
    }
}