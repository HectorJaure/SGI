<?php

namespace App\Http\Controllers;

use App\Models\RequisitoLegal;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;

class RequisitoLegalController extends Controller
{
    public function index(Request $request)
    {
        $query = RequisitoLegal::query();
        
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
        
        $perPage = $request->get('per_page', 10);
        $requisitos = $query->paginate($perPage);
        
        // OBTENER LAS NORMAS PARA EL FILTRO
        $normas = RequisitoLegal::distinct('norma')->pluck('norma')->filter();
        
        // OBTENER LOS TIPOS DE REQUISITO PARA EL FILTRO
        $tiposRequisito = RequisitoLegal::distinct('tipo_requisito')->pluck('tipo_requisito')->filter();
        
        return view('requisitos-legales.index', compact('requisitos', 'normas', 'tiposRequisito'));
    }

    public function create()
    {
        return view('requisitos-legales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'norma' => 'required|string|max:255',
            'titulo' => 'required|string|max:255',
            'tipo_requisito' => 'required|string|max:255',
            'numero_requisito' => 'required|string|max:50|regex:/^[\d\.]+$/',
            'descripcion' => 'required|string',
            'peligro_asociado' => 'required|string|max:255',
            'cumplimiento' => 'required|in:si,no',
            'evidencia' => 'nullable|string',
            'acciones_no' => 'nullable|string',
            'fecha_cumplimiento' => 'required|date|after_or_equal:today',
            'responsables' => 'required|string|max:255',
            'frecuencia_control' => 'required|string|max:100',
            'responsable_control' => 'required|string|max:255',
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
            'norma' => 'required|string|max:255',
            'titulo' => 'required|string|max:255',
            'tipo_requisito' => 'required|string|max:255',
            'numero_requisito' => 'required|string|max:50|regex:/^[\d\.]+$/',
            'descripcion' => 'required|string',
            'peligro_asociado' => 'required|string|max:255',
            'cumplimiento' => 'required|in:si,no',
            'evidencia' => 'nullable|string',
            'acciones_no' => 'nullable|string',
            'fecha_cumplimiento' => 'required|date|after_or_equal:today',
            'responsables' => 'required|string|max:255',
            'frecuencia_control' => 'required|string|max:100',
            'responsable_control' => 'required|string|max:255',
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