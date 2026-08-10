<?php

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/projects', function () {
    $projects = Project::where('is_active', true)
        ->orderBy('order', 'asc')
        ->get();

    return response()->json($projects);
});

Route::post('/vps/ping', [\App\Http\Controllers\Api\VpsApiController::class, 'ping']);

// Public Blog API Endpoints
Route::prefix('posts')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\BlogPostApiController::class, 'index'])->name('api.posts.index');
    Route::get('/featured', [\App\Http\Controllers\Api\BlogPostApiController::class, 'featured'])->name('api.posts.featured');
    Route::get('/{slug}', [\App\Http\Controllers\Api\BlogPostApiController::class, 'show'])->name('api.posts.show');
});

Route::get('/blog-categories', [\App\Http\Controllers\Api\BlogPostApiController::class, 'categories'])->name('api.blog-categories.index');


