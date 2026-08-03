<?php

use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\VpsServerController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');


use App\Models\ProjectOrder;
use App\Models\UserLog;

Route::get('/dashboard', function () {
    $projects = Project::orderBy('order', 'asc')->get();
    $allOrders = ProjectOrder::latest()->get();

    $stats = [
        'total_projects'   => $projects->count(),
        'active_projects'  => $projects->where('is_active', true)->count(),
        'featured_projects'=> $projects->where('featured', true)->count(),
        'total_pipeline'   => $allOrders->where('status', '!=', 'cancelled')->sum('budget'),
        'total_paid'       => $allOrders->where('status', '!=', 'cancelled')->sum('paid_amount'),
        'active_orders'    => $allOrders->whereNotIn('status', ['completed', 'cancelled'])->count(),
        'completed_orders' => $allOrders->where('status', 'completed')->count(),
    ];

    $recentOrders = $allOrders->take(5);
    $recentLogs   = UserLog::latest()->take(6)->get();

    return view('dashboard', compact('stats', 'projects', 'recentOrders', 'recentLogs'));
})->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\Admin\ProjectOrderController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MonitoredSiteController;
use App\Http\Controllers\Admin\CloudflareZoneController;
use App\Http\Controllers\Admin\CloudflareDnsController;
use App\Http\Controllers\Admin\CloudflarePinController;
use App\Http\Controllers\Admin\FinancialController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Projects Management
        Route::patch('projects/{project}/toggle-active', [ProjectController::class, 'toggleActive'])
            ->name('projects.toggle-active')
            ->middleware('can:projects.toggle-active');

        Route::resource('projects', ProjectController::class)->middleware([
            'index'   => 'can:projects.view',
            'show'    => 'can:projects.view',
            'create'  => 'can:projects.create',
            'store'   => 'can:projects.create',
            'edit'    => 'can:projects.edit',
            'update'  => 'can:projects.edit',
            'destroy' => 'can:projects.delete',
        ]);

        // Project Orders Kanban Board
        Route::patch('orders/{order}/update-status', [ProjectOrderController::class, 'updateStatus'])
            ->name('orders.update-status')
            ->middleware('can:orders.update-status');

        Route::resource('orders', ProjectOrderController::class)->middleware([
            'index'   => 'can:orders.view',
            'show'    => 'can:orders.view',
            'create'  => 'can:orders.create',
            'store'   => 'can:orders.create',
            'edit'    => 'can:orders.edit',
            'update'  => 'can:orders.edit',
            'destroy' => 'can:orders.delete',
        ]);

        // User Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])
            ->name('logs.index')
            ->middleware('can:logs.view');

        Route::delete('logs/clear', [ActivityLogController::class, 'clear'])
            ->name('logs.clear')
            ->middleware('can:logs.clear');

        // User Access Management
        Route::resource('users', UserController::class)->middleware([
            'index'   => 'can:users.view',
            'show'    => 'can:users.view',
            'create'  => 'can:users.create',
            'store'   => 'can:users.create',
            'edit'    => 'can:users.edit',
            'update'  => 'can:users.edit',
            'destroy' => 'can:users.delete',
        ]);

        Route::post('permissions', [RoleController::class, 'storePermission'])
            ->name('permissions.store')
            ->middleware('can:roles.create');

        Route::delete('permissions/{permission}', [RoleController::class, 'destroyPermission'])
            ->name('permissions.destroy')
            ->middleware('can:roles.delete');

        Route::resource('roles', RoleController::class)->middleware([
            'index'   => 'can:roles.view',
            'show'    => 'can:roles.view',
            'create'  => 'can:roles.create',
            'store'   => 'can:roles.create',
            'edit'    => 'can:roles.edit',
            'update'  => 'can:roles.edit',
            'destroy' => 'can:roles.delete',
        ]);

        // Website Health & Uptime Monitoring
        Route::post('sites/{site}/check', [MonitoredSiteController::class, 'check'])
            ->name('sites.check')
            ->middleware('can:sites.check');

        Route::resource('sites', MonitoredSiteController::class)->middleware([
            'index'   => 'can:sites.view',
            'show'    => 'can:sites.view',
            'create'  => 'can:sites.create',
            'store'   => 'can:sites.create',
            'edit'    => 'can:sites.edit',
            'update'  => 'can:sites.edit',
            'destroy' => 'can:sites.delete',
        ]);

        // VPS Server Monitoring
        Route::resource('vps', VpsServerController::class)->parameters([
            'vps' => 'vps'
        ]);



        // Cloudflare PIN Security Routes
        Route::get('cloudflare-pin', [CloudflarePinController::class, 'showPinForm'])
            ->name('cloudflare-pin.show')
            ->middleware('can:cloudflare.view');

        Route::post('cloudflare-pin', [CloudflarePinController::class, 'verifyPin'])
            ->name('cloudflare-pin.verify')
            ->middleware('can:cloudflare.view');

        Route::post('cloudflare-pin/lock', [CloudflarePinController::class, 'lockPin'])
            ->name('cloudflare-pin.lock')
            ->middleware('can:cloudflare.view');

        // Cloudflare Integration Routes (Protected by PIN)
        Route::middleware('cloudflare.pin')->group(function () {
            Route::resource('cloudflare-zones', CloudflareZoneController::class)
                ->only(['index', 'store', 'show', 'destroy'])
                ->middleware([
                    'index'   => 'can:cloudflare.view',
                    'show'    => 'can:cloudflare.view',
                    'store'   => 'can:cloudflare.create',
                    'destroy' => 'can:cloudflare.delete',
                ]);

            Route::post('cloudflare-zones/{zone}/purge-cache', [CloudflareZoneController::class, 'purgeCache'])
                ->name('cloudflare-zones.purge-cache')
                ->middleware('can:cloudflare.purge');

            Route::post('cloudflare-zones/{zone}/security', [CloudflareZoneController::class, 'updateSecurity'])
                ->name('cloudflare-zones.update-security')
                ->middleware('can:cloudflare.edit');

            Route::post('cloudflare-zones/{zone}/dns', [CloudflareDnsController::class, 'store'])
                ->name('cloudflare-dns.store')
                ->middleware('can:cloudflare.edit');

            Route::put('cloudflare-zones/{zone}/dns/{record}', [CloudflareDnsController::class, 'update'])
                ->name('cloudflare-dns.update')
                ->middleware('can:cloudflare.edit');

            Route::patch('cloudflare-zones/{zone}/dns/{record}/proxy', [CloudflareDnsController::class, 'toggleProxy'])
                ->name('cloudflare-dns.toggle-proxy')
                ->middleware('can:cloudflare.edit');

            Route::delete('cloudflare-zones/{zone}/dns/{record}', [CloudflareDnsController::class, 'destroy'])
                ->name('cloudflare-dns.destroy')
                ->middleware('can:cloudflare.edit');
        });

        // Financial Management Routes
        Route::get('finance', [FinancialController::class, 'index'])
            ->name('finance.index')
            ->middleware('can:finance.view');

        Route::post('finance', [FinancialController::class, 'store'])
            ->name('finance.store')
            ->middleware('can:finance.manage');

        Route::delete('finance/{transaction}', [FinancialController::class, 'destroy'])
            ->name('finance.destroy')
            ->middleware('can:finance.manage');

        Route::post('finance/categories', [FinancialController::class, 'storeCategory'])
            ->name('finance.categories.store')
            ->middleware('can:finance.manage');

        Route::delete('finance/categories/{category}', [FinancialController::class, 'destroyCategory'])
            ->name('finance.categories.destroy')
            ->middleware('can:finance.manage');
    });
});

// Dynamic VPS Agent Installation Script Route
Route::get('/vps-agent/{token}/install.sh', [VpsServerController::class, 'installScript'])->name('vps.install-script');

require __DIR__.'/auth.php';

