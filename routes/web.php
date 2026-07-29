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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Projects Management
        Route::patch('projects/{project}/toggle-active', [ProjectController::class, 'toggleActive'])->name('projects.toggle-active');
        Route::resource('projects', ProjectController::class);

        // Project Orders Kanban Board
        Route::patch('orders/{order}/update-status', [ProjectOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::resource('orders', ProjectOrderController::class);

        // User Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::delete('logs/clear', [ActivityLogController::class, 'clear'])->name('logs.clear');
    });
});

require __DIR__.'/auth.php';
