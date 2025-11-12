<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener parámetros de filtro
        $filtroTipo = $request->get('tipo');
        $filtroEstado = $request->get('estado');
        $filtroUsuario = $request->get('usuario');
        
        $perPage = $request->get('per_page', 10);

        // Construir consulta base (PRIMERO esto)
        $query = Notification::query();

        // Aplicar filtros
        if ($filtroTipo) {
            $query->where('tipo', $filtroTipo);
        }

        if ($filtroEstado) {
            $query->where('estado', $filtroEstado);
        }

        if ($filtroUsuario) {
            $query->where('usuario_accion', $filtroUsuario);
        }

        // AHORA SÍ ejecutar la consulta paginada
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $unreadCount = Notification::where('estado', 'no_leida')->count();
        
        // Obtener todos los usuarios únicos que realizaron acciones
        $usuariosAccion = Notification::select('usuario_accion')
            ->distinct()
            ->whereNotNull('usuario_accion')
            ->orderBy('usuario_accion')
            ->pluck('usuario_accion')
            ->filter()
            ->toArray();
        
        // Pasar los filtros actuales a la vista
        $filtrosActuales = [
            'tipo' => $filtroTipo,
            'estado' => $filtroEstado,
            'usuario' => $filtroUsuario,
            'per_page' => $perPage
        ];
        
        return view('notifications.index', compact('notifications', 'unreadCount', 'usuariosAccion', 'filtrosActuales'));
    }

    /**
     * Clear all notifications.
     */
    public function clearAll()
    {
        try {
            $count = Notification::count();
            
            // Eliminar todas las notificaciones
            Notification::truncate();
            
            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$count} notificaciones correctamente",
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->update(['estado' => 'leida']);
            
            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como leída'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        try {
            $count = Notification::where('estado', 'no_leida')->count();
            Notification::where('estado', 'no_leida')->update(['estado' => 'leida']);
            
            return response()->json([
                'success' => true,
                'message' => "Se marcaron {$count} notificaciones como leídas",
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar todas como leídas'
            ], 500);
        }
    }

    /**
     * Mark a notification as unread.
     */
    public function markAsUnread($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->update(['estado' => 'no_leida']);
            
            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como no leída'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como no leída'
            ], 500);
        }
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy($id)
    {

        try {
            $notification = Notification::findOrFail($id);
            $titulo = $notification->titulo;
            $notification->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Notificación '{$titulo}' eliminada correctamente"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la notificación'
            ], 500);
        }
    }

    /**
     * Create a new notification.
     */
    public static function createNotification($titulo, $descripcion, $tipo = 'info', $remitente = 'Sistema')
    {
        // Obtener el usuario actual de la sesión
        $usuarioAccion = session('user_nombre') ?? 'Sistema';
        
        Notification::create([
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'estado' => 'no_leida',
            'remitente' => $remitente,
            'usuario_accion' => $usuarioAccion // Guardar quién realizó la acción
        ]);
        
        return true;
    }

    /**
     * Get unread notifications for header.
     */
    public static function getUnreadNotifications()
    {
        return Notification::where('estado', 'no_leida')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get unread notifications count.
     */
    public static function getUnreadCount()
    {
        return Notification::where('estado', 'no_leida')->count();
    }
}