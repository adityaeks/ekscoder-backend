<?php

use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
    });
});

require __DIR__.'/auth.php';
