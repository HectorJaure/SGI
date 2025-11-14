<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\RiskMatrixController;
use App\Http\Controllers\RequisitoLegalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationActController;
use Illuminate\Support\Facades\Route;

// Rutas PÚBLICAS
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Recuperación de contraseña
Route::get('/forgot-password', [PasswordController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');

// Rutas PROTEGIDAS
Route::middleware([\App\Http\Middleware\AuthenticateSession::class])->group(function () {
    
    // Dashboard (accesible para todos los usuarios autenticados)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Riesgos (accesibles para todos los usuarios autenticados)
    Route::get('/risks/matrix', [RiskMatrixController::class, 'matrix'])->name('risks.matrix');
    Route::get('/risks/create', [RiskMatrixController::class, 'create'])->name('risks.create');
    Route::post('/risks', [RiskMatrixController::class, 'store'])->name('risks.store');
    Route::get('/risks/{id}/edit', [RiskMatrixController::class, 'edit'])->name('risks.edit');
    Route::put('/risks/{id}', [RiskMatrixController::class, 'update'])->name('risks.update');
    Route::delete('/risks/{id}', [RiskMatrixController::class, 'destroy'])->name('risks.destroy');
    Route::get('/export/verification-act', [VerificationActController::class, 'exportVerificationAct'])->name('export.verification-act');


    // Requisitos Legales (accesibles para todos los usuarios autenticados)
    Route::get('/requisitos-legales', [RequisitoLegalController::class, 'index'])->name('requisitos-legales.index');
    Route::get('/requisitos-legales/crear', [RequisitoLegalController::class, 'create'])->name('requisitos-legales.create');
    Route::post('/requisitos-legales', [RequisitoLegalController::class, 'store'])->name('requisitos-legales.store');
    Route::get('/requisitos-legales/{id}/editar', [RequisitoLegalController::class, 'edit'])->name('requisitos-legales.edit');
    Route::put('/requisitos-legales/{id}', [RequisitoLegalController::class, 'update'])->name('requisitos-legales.update');
    Route::delete('/requisitos-legales/{id}', [RequisitoLegalController::class, 'destroy'])->name('requisitos-legales.destroy');

    // Ruta independiente para el instructivo
    Route::get('/instructivo', function () {return view('instructivo');})->name('instructivo');

    // Grupo de rutas SOLO PARA ADMINISTRADORES
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        
        // Gestión de Usuarios (solo administradores)
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/crear', [UserController::class, 'create'])->name('users.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
        Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/verify-password', [UserController::class, 'verifyPassword'])->name('verify.password');
        
        // Notificaciones (solo administradores)
        Route::prefix('notificaciones')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/{id}/marcar-leida', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::post('/{id}/marcar-no-leida', [NotificationController::class, 'markAsUnread'])->name('notifications.markAsUnread');
            Route::post('/marcar-todas-leidas', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
            Route::delete('/', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');
        });
    });
});