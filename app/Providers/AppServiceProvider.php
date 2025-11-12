<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir notificaciones no leídas con todas las vistas
        view()->composer('*', function ($view) {
            if (session('logged_in')) {
                $unreadNotifications = Notification::where('estado', 'no_leida')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                    
                $unreadCount = Notification::where('estado', 'no_leida')->count();
                
                $view->with([
                    'unreadNotifications' => $unreadNotifications,
                    'unreadNotificationsCount' => $unreadCount
                ]);
            }
        });
    }
}