<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['causer', 'subject'])->orderByDesc('created_at');

        if ($logName = $request->input('log_name')) {
            $query->where('log_name', $logName);
        }

        if ($event = $request->input('event')) {
            $query->where('event', $event);
        }

        if ($userId = $request->input('causer_id')) {
            $query->where('causer_id', (int) $userId);
        }

        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('event', 'like', '%' . $search . '%')
                  ->orWhere('ip_address', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        $logNames = ActivityLog::query()
            ->select('log_name')
            ->distinct()
            ->pluck('log_name')
            ->filter()
            ->values();

        $events = ActivityLog::query()
            ->select('event')
            ->distinct()
            ->pluck('event')
            ->filter()
            ->values();

        $counts = [
            'all' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'week' => ActivityLog::where('created_at', '>=', now()->subWeek())->count(),
            'month' => ActivityLog::where('created_at', '>=', now()->subMonth())->count(),
        ];

        return view('admin.activity-log.index', compact('logs', 'logNames', 'events', 'counts'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['causer', 'subject']);

        return view('admin.activity-log.show', compact('activityLog'));
    }

    public function destroy(Request $request, ActivityLog $activityLog)
    {
        $activityLog->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Log entry deleted.'));
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:rj_activity_log,id',
            'action' => 'required|in:delete',
        ]);

        $count = ActivityLog::whereIn('ids', $validated['ids'])->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => $count]);
        }

        return back()->with('success', __(':n log(s) deleted.', ['n' => $count]));
    }

    public function clear(Request $request)
    {
        $validated = $request->validate([
            'days' => 'nullable|integer|min:1|max:365',
            'log_name' => 'nullable|string|max:100',
        ]);

        $query = ActivityLog::query();

        if (!empty($validated['days'])) {
            $query->where('created_at', '<', now()->subDays((int) $validated['days']));
        }

        if (!empty($validated['log_name'])) {
            $query->where('log_name', $validated['log_name']);
        }

        $count = $query->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => __(':n log(s) cleared.', ['n' => $count]),
            ]);
        }

        return back()->with('success', __(':n log(s) cleared.', ['n' => $count]));
    }
}