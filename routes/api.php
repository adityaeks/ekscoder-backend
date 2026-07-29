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
