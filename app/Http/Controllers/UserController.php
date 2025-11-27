<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\NotificationController;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $perPage = $request->get('per_page', 10);
        
        $query = User::query();

        if ($request->has('departamento') && !empty($request->departamento)) {
            $query->where('departamento', 'LIKE', '%' . $request->departamento . '%');
        }

        if ($request->has('rol') && !empty($request->rol)) {
            $query->where('rol', $request->rol);
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                ->orWhere('username', 'LIKE', "%{$searchTerm}%");
            });
        }

        $users = $query->paginate($perPage);

        $users->appends($request->except('page'));

        $departamentos = User::distinct('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->orderBy('departamento')
            ->pluck('departamento')
            ->filter();
        
        return view('users.index', compact('users', 'departamentos'));
    }

    public function create()
    {
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $departamentos = User::distinct('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->orderBy('departamento')
            ->pluck('departamento')
            ->filter();

        return view('users.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'departamento' => 'nullable|string|max:255',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
            'rol' => ['required', Rule::in(['Administrador', 'Usuario'])],
            'telefono' => 'nullable|string|max:20|regex:/^[\d\s\-\+\(\)]+$/',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
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

        $usuarioActual = session('user_nombre') ?? 'Administrador';
        NotificationController::createNotification(
            'Nuevo Usuario Registrado',
            "El usuario {$usuarioActual} ha creado el usuario: {$user->nombre} ({$user->rol})",
            'success'
        );

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    public function edit(User $user)
    {
        if (session('user_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $departamentos = User::distinct('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->orderBy('departamento')
            ->pluck('departamento')
            ->filter();

        return view('users.edit', compact('user', 'departamentos'));
    }

    public function update(Request $request, User $user)
    {
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
            'password' => [
                'nullable',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
            'current_password' => 'required',
            'telefono' => 'nullable|string|max:20|regex:/^[\d\s\-\+\(\)]+$/'
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
        ]);

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

        if ($request->filled('password') && !empty(trim($request->password))) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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
        if (session('user_rol') !== 'Administrador') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        try {
            $currentUserId = session('user_id');
            $currentUser = User::find($currentUserId);

            if (!$currentUserId || !$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión expirada. Por favor, inicie sesión nuevamente.'
                ], 401);
            }

            if (empty($request->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, ingrese su contraseña.'
                ], 422);
            }

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

    public function destroy(Request $request, User $user)
    {
        if (session('user_rol') !== 'Administrador') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        try {
            $currentUserId = session('user_id');
            $currentUser = User::find($currentUserId);

            if (!$currentUserId || !$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            if (empty($request->current_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es requerida'
                ], 422);
            }

            if (!Hash::check($request->current_password, $currentUser->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta'
                ], 401);
            }

            if ($currentUser->id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ], 422);
            }

            $nombreUsuario = $user->nombre;
        $user->delete();

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