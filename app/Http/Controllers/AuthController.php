<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('logged_in')) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Buscar usuario por username o email
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'usuario' => $user->username,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_nombre' => $user->nombre,
                'user_rol' => $user->rol,
                'logged_in' => true,
                'last_activity' => time()
            ]);

            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Usuario o contraseña incorrectos.');
    }

    public function logout(Request $request)
    {
        session()->flush();
        $request->session()->regenerate();
        return redirect()->route('login');
    }
}