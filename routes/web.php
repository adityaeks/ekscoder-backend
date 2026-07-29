<?php

use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $projects = Project::orderBy('order', 'asc')->get();
    $stats = [
        'total' => $projects->count(),
        'active' => $projects->where('is_active', true)->count(),
        'featured' => $projects->where('featured', true)->count(),
    ];
    return view('dashboard', compact('stats', 'projects'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Projects Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::patch('projects/{project}/toggle-active', [ProjectController::class, 'toggleActive'])->name('projects.toggle-active');
        Route::resource('projects', ProjectController::class);
    });
});

require __DIR__.'/auth.php';
