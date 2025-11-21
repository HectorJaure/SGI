<?php

namespace App\Http\Controllers;

use App\Models\RequisitoLegal;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;

class RequisitoLegalController extends Controller
{
    public function index(Request $request)
    {

    $query = RequisitoLegal::query()->orderBy('categoria_norma')->orderBy('norma');
        
        // Búsqueda general
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', '%'.$request->search.'%')
                  ->orWhere('descripcion', 'like', '%'.$request->search.'%')
                  ->orWhere('norma', 'like', '%'.$request->search.'%');
            });
        }
        
        // Filtro por cumplimiento
        if ($request->has('cumplimiento') && $request->cumplimiento != '') {
            $query->where('cumplimiento', $request->cumplimiento);
        }
        
        // Filtro por norma
        if ($request->has('norma') && $request->norma != '') {
            $query->where('norma', $request->norma);
        }
        
        // Filtro por categoría de norma
        if ($request->has('categoria_norma') && $request->categoria_norma != '') {
            $query->where('categoria_norma', $request->categoria_norma);
        }
        
        // Filtro por tipo de requisito
        if ($request->has('tipo_requisito') && $request->tipo_requisito != '') {
            $query->where('tipo_requisito', $request->tipo_requisito);
        }
        
        // Filtro por peligro asociado
        if ($request->has('peligro_asociado') && $request->peligro_asociado != '') {
            $query->where('peligro_asociado', 'like', '%'.$request->peligro_asociado.'%');
        }
        
        // Filtro por fecha de cumplimiento
        if ($request->has('fecha_cumplimiento') && $request->fecha_cumplimiento != '') {
            $query->whereDate('fecha_cumplimiento', $request->fecha_cumplimiento);
        }
        
        // Filtro por responsable
        if ($request->has('responsable') && $request->responsable != '') {
            $query->where('responsables', 'like', '%'.$request->responsable.'%');
        }
        
        // Determinar si mostrar todos o paginar
        $perPage = $request->get('per_page', 10);
        
        if ($perPage === 'all') {
            $requisitos = $query->get();
        } else {
            $requisitos = $query->paginate($perPage);
            $requisitos->appends($request->except('page'));
        }
        
        // OBTENER LAS NORMAS PARA EL FILTRO
        $normas = RequisitoLegal::distinct('norma')->pluck('norma')->filter();
        
        // OBTENER LOS TIPOS DE REQUISITO PARA EL FILTRO
        $tiposRequisito = RequisitoLegal::distinct('tipo_requisito')->pluck('tipo_requisito')->filter();
        
        // DEFINIR CATEGORÍAS
        $categoriasNorma = [
            'seguridad' => 'Normas de Seguridad',
            'salud' => 'Normas de Salud', 
            'organizacion' => 'Normas de Organización'
        ];
        
        // AGRUPAR REQUISITOS POR CATEGORÍA
        $requisitosAgrupados = $this->agruparPorCategoriaNorma($requisitos);
        
        return view('requisitos-legales.index', compact('requisitos', 'normas', 'tiposRequisito', 'requisitosAgrupados', 'categoriasNorma'));
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
            } else {
                // Si no tiene categoría, asignar a seguridad por defecto
                $agrupados['seguridad']['requisitos']->push($requisito);
            }
        }

        return $agrupados;
    }

    public function create()
    {
        return view('requisitos-legales.create');
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

        // NOTIFICACIÓN AUTOMÁTICA - Nuevo requisito legal
        $tipoNotificacion = $requisito->cumplimiento == 'no' ? 'warning' : 'success';
        
        NotificationController::createNotification(
            'Nuevo Requisito Legal Registrado',
            "Se ha agregado el requisito: {$requisito->titulo} ({$requisito->norma}). Cumplimiento: " . ($requisito->cumplimiento == 'si' ? 'Cumplido' : 'Pendiente'),
            $tipoNotificacion
        );

        return redirect()->route('requisitos-legales.index')
                         ->with('success', 'Requisito creado correctamente.');
    }

    public function edit($id)
    {
        $requisito = RequisitoLegal::findOrFail($id);
        return view('requisitos-legales.edit', compact('requisito'));
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

        // NOTIFICACIÓN AUTOMÁTICA - Requisito legal actualizado
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

        // NOTIFICACIÓN AUTOMÁTICA - Requisito legal eliminado
        NotificationController::createNotification(
            'Requisito Legal Eliminado',
            "Se ha eliminado el requisito: {$titulo} ({$norma}) del sistema",
            'warning'
        );

        return redirect()->route('requisitos-legales.index')
                         ->with('success', 'Requisito eliminado correctamente.');
    }
}