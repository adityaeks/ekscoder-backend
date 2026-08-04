<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\ProjectOrder;
use App\Models\MonitoredSite;
use App\Services\DomainExpirationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of calendar events & integrated system deadlines.
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        // Normalize year and month boundaries
        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endOfMonth   = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear   = Carbon::createFromDate($year, 12, 31)->endOfYear();

        // 1. Custom User Calendar Events
        $customEvents = CalendarEvent::whereBetween('start_date', [$startOfYear, $endOfYear])
            ->orWhereBetween('end_date', [$startOfYear, $endOfYear])
            ->get();

        // 2. Project Orders Deadlines & Start Dates (Exclude cancelled orders)
        $projectOrders = ProjectOrder::where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startOfYear, $endOfYear) {
                $query->whereBetween('start_date', [$startOfYear->toDateString(), $endOfYear->toDateString()])
                      ->orWhereBetween('deadline', [$startOfYear->toDateString(), $endOfYear->toDateString()]);
            })
            ->get();

        // 3. Monitored Sites Domain Expiration
        $monitoredSites = MonitoredSite::all();
        $domainService = app(DomainExpirationService::class);

        // Standardize event list for rendering in Blade & JS
        $formattedEvents = [];
        $allEventsByDateYear = [];

        $categoryIcons = [
            'event'    => '📌',
            'task'     => '📝',
            'meeting'  => '🤝',
            'reminder' => '🔔',
        ];

        foreach ($customEvents as $event) {
            $icon = $categoryIcons[$event->category] ?? '📌';
            $evtObj = [
                'id'           => 'custom_' . $event->id,
                'raw_id'       => $event->id,
                'title'        => $icon . ' ' . $event->title,
                'description'  => $event->description,
                'start'        => $event->start_date->format('Y-m-d\TH:i'),
                'start_date'   => $event->start_date->format('Y-m-d'),
                'end_date'     => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                'color'        => $event->color ?? '#6366f1',
                'category'     => $event->category,
                'type'         => 'custom',
                'is_completed' => (bool) $event->is_completed,
                'editable'     => true,
                'badge'        => ucfirst($event->category),
            ];

            if ($event->start_date->between($startOfMonth, $endOfMonth)) {
                $formattedEvents[] = $evtObj;
            }
            $allEventsByDateYear[$event->start_date->format('Y-m-d')][] = $evtObj;
        }

        foreach ($projectOrders as $order) {
            if ($order->start_date && $order->start_date->between($startOfYear, $endOfYear)) {
                $evtObj = [
                    'id'           => 'order_start_' . $order->id,
                    'title'        => '🚀 ' . $order->title,
                    'description'  => "Klien: {$order->client_name} | Budget: {$order->formatted_budget} | Status: " . ucfirst($order->status),
                    'start_date'   => $order->start_date->format('Y-m-d'),
                    'color'        => '#06b6d4',
                    'category'     => 'project',
                    'type'         => 'project_start',
                    'is_completed' => $order->status === 'completed',
                    'editable'     => false,
                    'url'          => route('admin.orders.index'),
                    'badge'        => 'Order Start',
                ];
                if ($order->start_date->between($startOfMonth, $endOfMonth)) {
                    $formattedEvents[] = $evtObj;
                }
                $allEventsByDateYear[$order->start_date->format('Y-m-d')][] = $evtObj;
            }

            if ($order->deadline && $order->deadline->between($startOfYear, $endOfYear)) {
                $evtObj = [
                    'id'           => 'order_deadline_' . $order->id,
                    'title'        => '⏰ ' . $order->title,
                    'description'  => "Klien: {$order->client_name} | Priority: " . ucfirst($order->priority),
                    'start_date'   => $order->deadline->format('Y-m-d'),
                    'color'        => '#f43f5e',
                    'category'     => 'project',
                    'type'         => 'project_deadline',
                    'is_completed' => $order->status === 'completed',
                    'editable'     => false,
                    'url'          => route('admin.orders.index'),
                    'badge'        => 'Order Deadline',
                ];
                if ($order->deadline->between($startOfMonth, $endOfMonth)) {
                    $formattedEvents[] = $evtObj;
                }
                $allEventsByDateYear[$order->deadline->format('Y-m-d')][] = $evtObj;
            }
        }

        foreach ($monitoredSites as $site) {
            if (!empty($site->url)) {
                $host = parse_url($site->url, PHP_URL_HOST) ?? $site->url;
                $expInfo = $domainService->getExpirationInfo($host);
                if (!empty($expInfo['expires_at'])) {
                    $expDate = Carbon::parse($expInfo['expires_at']);
                    if ($expDate->between($startOfYear, $endOfYear)) {
                        $evtObj = [
                            'id'           => 'domain_exp_' . $site->id,
                            'title'        => '🌐 ' . $site->name,
                            'description'  => "Domain: {$host} | Expired: {$expInfo['formatted']} ({$expInfo['human']})",
                            'start_date'   => $expDate->format('Y-m-d'),
                            'color'        => '#f59e0b',
                            'category'     => 'domain',
                            'type'         => 'domain_expiration',
                            'is_completed' => false,
                            'editable'     => false,
                            'url'          => route('admin.sites.index'),
                            'badge'        => 'Domain Expired',
                        ];
                        if ($expDate->between($startOfMonth, $endOfMonth)) {
                            $formattedEvents[] = $evtObj;
                        }
                        $allEventsByDateYear[$expDate->format('Y-m-d')][] = $evtObj;
                    }
                }
            }
        }

        // Build 12-Month Annual Data Array
        $todayDate = Carbon::now()->format('Y-m-d');
        $yearMonthsData = [];

        for ($m = 1; $m <= 12; $m++) {
            $mCarbon = Carbon::createFromDate($year, $m, 1);
            $mStart  = $mCarbon->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
            $mEnd    = $mCarbon->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

            $days = [];
            $iter = $mStart->copy();

            while ($iter <= $mEnd) {
                $dStr = $iter->format('Y-m-d');
                $days[] = [
                    'day'              => $iter->day,
                    'date'             => $dStr,
                    'is_current_month' => ($iter->month == $m),
                    'is_today'         => ($dStr === $todayDate),
                    'events'           => $allEventsByDateYear[$dStr] ?? [],
                ];
                $iter->addDay();
            }

            $yearMonthsData[] = [
                'month_num'  => $m,
                'name'       => $mCarbon->translatedFormat('F'),
                'days'       => $days,
            ];
        }

        return view('admin.calendar.index', compact(
            'currentMonth',
            'year',
            'month',
            'startOfMonth',
            'endOfMonth',
            'formattedEvents',
            'yearMonthsData'
        ));
    }

    /**
     * Store a newly created calendar event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string|max:20',
            'category'    => 'required|in:event,task,meeting,reminder',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['color']   = $validated['color'] ?? '#6366f1';

        CalendarEvent::create($validated);

        return redirect()->back()->with('success', 'Agenda berhasil ditambahkan!');
    }

    /**
     * Update the specified calendar event.
     */
    public function update(Request $request, CalendarEvent $calendar)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string|max:20',
            'category'    => 'required|in:event,task,meeting,reminder',
        ]);

        $calendar->update($validated);

        return redirect()->back()->with('success', 'Agenda berhasil diperbarui!');
    }

    /**
     * Toggle event completion status.
     */
    public function toggleComplete(CalendarEvent $calendar)
    {
        $calendar->update([
            'is_completed' => !$calendar->is_completed,
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_completed' => $calendar->is_completed,
            ]);
        }

        return redirect()->back()->with('success', 'Status agenda berhasil diperbarui!');
    }

    /**
     * Remove the specified calendar event.
     */
    public function destroy(CalendarEvent $calendar)
    {
        $calendar->delete();

        return redirect()->back()->with('success', 'Agenda berhasil dihapus!');
    }
}
