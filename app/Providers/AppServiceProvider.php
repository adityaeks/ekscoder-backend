<?php

namespace App\Providers;

use App\Models\UserLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

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
        // Implicitly grant 'Super Admin' role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        \App\Models\ProjectOrder::observe(\App\Observers\ProjectOrderObserver::class);

        // Record User Login in Activity Logs
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            UserLog::create([
                'user_id'     => $user?->id,
                'user_name'   => $user?->name ?? 'User',
                'action'      => 'login',
                'module'      => 'Auth',
                'model_id'    => $user?->id,
                'description' => "User '{$user?->name}' logged in to the website",
                'changes'     => [
                    'email' => $user?->email,
                    'guard' => $event->guard,
                ],
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        });

        // Record User Logout in Activity Logs
        Event::listen(Logout::class, function (Logout $event) {
            $user = $event->user;
            if ($user) {
                UserLog::create([
                    'user_id'     => $user->id,
                    'user_name'   => $user->name,
                    'action'      => 'logout',
                    'module'      => 'Auth',
                    'model_id'    => $user->id,
                    'description' => "User '{$user->name}' logged out from the website",
                    'ip_address'  => Request::ip(),
                    'user_agent'  => Request::userAgent(),
                ]);
            }
        });
    }
}
