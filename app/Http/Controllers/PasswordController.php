<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No encontramos ningún usuario con ese correo electrónico.'
        ]);

        $user = User::where('email', $request->email)->first();

        $token = Str::random(64);
        
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token]);

        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
            
            return back()->with('success', '¡Hemos enviado un enlace de recuperación a tu correo electrónico!');
            
        } catch (\Exception $e) {
            \Log::error('Error en envío de correo: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Error en el sistema de correo. Contacta al administrador.'
            ]);
        }
    }

    public function showResetForm($token)
    {
        $passwordReset = DB::table('password_resets')
            ->whereRaw('created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)')
            ->get()
            ->first(function ($reset) use ($token) {
                return Hash::check($token, $reset->token);
            });

        if (!$passwordReset) {
            return redirect()->route('password.request')->with('error', 'Token inválido o expirado.');
        }

        $user = User::where('email', $passwordReset->email)->first();

        return view('auth.reset-password', [
            'token' => $token,
            'userEmail' => $passwordReset->email,
            'userName' => $user->username
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
        ]);

        $passwordReset = DB::table('password_resets')
            ->whereRaw('created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)')
            ->get()
            ->first(function ($reset) use ($request) {
                return Hash::check($request->token, $reset->token);
            });

        if (!$passwordReset) {
            return back()->withErrors(['password' => 'Link inválido o expirado.']);
        }

        $user = User::where('email', $passwordReset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $passwordReset->email)->delete();

        return redirect()->route('login')->with('success', '¡Tu contraseña ha sido restablecida exitosamente!');
    }
}