<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\NotificationController;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $perPage = $request->get('per_page', 10);
        $users = User::paginate($perPage);

        // Obtener departamentos únicos para el datalist
        $departamentos = User::distinct('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->orderBy('departamento')
            ->pluck('departamento')
            ->filter();
        
        return view('users.index', compact('users', 'departamentos'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener departamentos existentes para el datalist
        $departamentos = User::distinct('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->orderBy('departamento')
            ->pluck('departamento')
            ->filter();

        return view('users.create', compact('departamentos'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'departamento' => 'nullable|string|max:255',
            'password' => 'required|min:6|confirmed',
            'rol' => ['required', Rule::in(['Administrador', 'Usuario'])],
            'telefono' => 'nullable|string|max:20|regex:/^[\d\s\-\+\(\)]+$/',
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'username' => $request->username,
            'email' => $request->email,
            'departamento' => $request->departamento,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'telefono' => $request->telefono,
        ]);

        // NOTIFICACIÓN AUTOMÁTICA - Nuevo usuario (incluye quién lo creó)
        $usuarioActual = session('user_nombre') ?? 'Administrador';
        NotificationController::createNotification(
            'Nuevo Usuario Registrado',
            "El usuario {$usuarioActual} ha creado el usuario: {$user->nombre} ({$user->rol})",
            'success'
        );

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener departamentos existentes para el datalist
        $departamentos = User::distinct('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->orderBy('departamento')
            ->pluck('departamento')
            ->filter();

        return view('users.edit', compact('user', 'departamentos'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'departamento' => 'nullable|string|max:255',
            'rol' => ['required', Rule::in(['Administrador', 'Usuario'])],
            'password' => 'nullable|min:6|confirmed',
            'current_password' => 'required',
            'telefono' => 'nullable|string|max:20|regex:/^[\d\s\-\+\(\)]+$/'
        ]);

        // Verificar contraseña actual
        $currentUserId = session('user_id');
        $currentUser = User::find($currentUserId);
        
        if (!$currentUser || !Hash::check($request->current_password, $currentUser->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $data = [
            'nombre' => $request->nombre,
            'username' => $request->username,
            'email' => $request->email,
            'departamento' => $request->departamento,
            'rol' => $request->rol,
            'telefono' => $request->telefono,
        ];
        
        // Actualizar contraseña solo si se proporcionó una nueva
        if ($request->filled('password') && !empty(trim($request->password))) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // NOTIFICACIÓN AUTOMÁTICA - Usuario actualizado (incluye quién lo actualizó)
        $usuarioActual = session('user_nombre') ?? 'Administrador';
        NotificationController::createNotification(
            'Usuario Actualizado',
            "El usuario {$usuarioActual} ha modificado los datos del usuario: {$user->nombre}",
            'info'
        );

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }
    
    public function verifyPassword(Request $request)
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        try {
            // Obtener usuario actual desde la sesión
            $currentUserId = session('user_id');
            $currentUser = User::find($currentUserId);

            // Verificar que el usuario esté autenticado
            if (!$currentUserId || !$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión expirada. Por favor, inicie sesión nuevamente.'
                ], 401);
            }

            // Validación básica
            if (empty($request->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, ingrese su contraseña.'
                ], 422);
            }

            // Verificar contraseña
            if (Hash::check($request->password, $currentUser->password)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contraseña verificada correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña ingresada es incorrecta.'
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Por favor, intente más tarde.'
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'Administrador') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        try {
            // Obtener usuario actual desde la sesión
            $currentUserId = session('user_id');
            $currentUser = User::find($currentUserId);

            // Verificar que el usuario esté autenticado
            if (!$currentUserId || !$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Validar contraseña
            if (empty($request->current_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es requerida'
                ], 422);
            }

            // Verificar contraseña
            if (!Hash::check($request->current_password, $currentUser->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta'
                ], 401);
            }

            // Evitar auto-eliminación
            if ($currentUser->id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ], 422);
            }

            $nombreUsuario = $user->nombre;
        $user->delete();

        // NOTIFICACIÓN AUTOMÁTICA - Usuario eliminado (incluye quién lo eliminó)
        $usuarioActual = session('user_nombre') ?? 'Administrador';
        NotificationController::createNotification(
            'Usuario Eliminado',
            "El usuario {$usuarioActual} ha eliminado el usuario: {$nombreUsuario} del sistema",
            'warning'
        );

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario'
            ], 500);
        }
    }
}