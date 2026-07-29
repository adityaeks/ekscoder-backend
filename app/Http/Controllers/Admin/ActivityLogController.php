<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of user logs.
     */
    public function index(Request $request)
    {
        $query = UserLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', strtolower($request->action));
        }

        $logs = $query->get();

        $modules = UserLog::select('module')->distinct()->pluck('module');
        $actions = UserLog::select('action')->distinct()->pluck('action');

        return view('admin.logs.index', compact('logs', 'modules', 'actions'));
    }

    /**
     * Clear all activity logs.
     */
    public function clear()
    {
        UserLog::truncate();

        UserLog::log(
            action: 'delete',
            module: 'UserLog',
            description: 'Cleared all system activity logs'
        );

        return redirect()->back()->with('success', 'User activity logs have been cleared successfully!');
    }
}
