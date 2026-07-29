<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectOrder;
use Illuminate\Http\Request;

class ProjectOrderController extends Controller
{
    /**
     * Display Kanban Board with project orders grouped by status.
     */
    public function index()
    {
        $allOrders = ProjectOrder::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();

        $statuses = [
            'requirement' => ['label' => 'Requirement & DP', 'badge' => 'badge-amber', 'icon' => '📝'],
            'in_progress' => ['label' => 'In Progress', 'badge' => 'badge-cyan', 'icon' => '⚡'],
            'review'      => ['label' => 'Review / Testing', 'badge' => 'badge-accent', 'icon' => '🔍'],
            'completed'   => ['label' => 'Completed', 'badge' => 'badge-green', 'icon' => '✅'],
            'cancelled'   => ['label' => 'Cancelled / Hold', 'badge' => 'badge-rose', 'icon' => '⛔'],
        ];

        $groupedOrders = [];
        foreach ($statuses as $key => $meta) {
            $groupedOrders[$key] = $allOrders->where('status', $key)->values();
        }

        $stats = [
            'total_pipeline' => $allOrders->where('status', '!=', 'cancelled')->sum('budget'),
            'total_paid'     => $allOrders->where('status', '!=', 'cancelled')->sum('paid_amount'),
            'active_orders'  => $allOrders->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'completed_count'=> $allOrders->where('status', 'completed')->count(),
        ];

        return view('admin.orders.index', compact('groupedOrders', 'statuses', 'stats'));
    }

    /**
     * Store a newly created project order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:requirement,in_progress,review,completed,cancelled',
            'priority' => 'required|string|in:low,medium,high',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
        ]);

        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;
        $validated['start_date'] = $validated['start_date'] ?? now()->toDateString();
        $validated['order'] = ProjectOrder::where('status', $validated['status'])->count() + 1;

        ProjectOrder::create($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Project order created successfully!');
    }

    /**
     * Update specified project order.
     */
    public function update(Request $request, ProjectOrder $order)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:requirement,in_progress,review,completed,cancelled',
            'priority' => 'required|string|in:low,medium,high',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
        ]);

        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Project order updated successfully!');
    }

    /**
     * Update status & order via AJAX drag and drop.
     */
    public function updateStatus(Request $request, ProjectOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:requirement,in_progress,review,completed,cancelled',
            'order' => 'nullable|integer',
        ]);

        $order->update([
            'status' => $validated['status'],
            'order' => $validated['order'] ?? $order->order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated to ' . strtoupper($validated['status']),
            'order' => $order,
        ]);
    }

    /**
     * Remove specified project order.
     */
    public function destroy(ProjectOrder $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Project order deleted successfully!');
    }
}
