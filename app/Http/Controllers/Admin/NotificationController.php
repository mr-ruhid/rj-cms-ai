<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Notification::for($user)->orderByDesc('created_at');

        if ($filter = $request->input('filter')) {
            if ($filter === 'unread') {
                $query->unread();
            } elseif ($filter === 'read') {
                $query->read();
            }
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $notifications = $query->paginate(30)->withQueryString();

        $counts = [
            'all' => Notification::for($user)->count(),
            'unread' => Notification::for($user)->unread()->count(),
            'read' => Notification::for($user)->read()->count(),
        ];

        $types = Notification::types();

        return view('admin.notifications.index', compact('notifications', 'counts', 'types'));
    }

    public function unreadCount()
    {
        $user = auth()->user();

        return response()->json([
            'count' => Notification::unreadCount($user),
        ]);
    }

    public function recent()
    {
        $user = auth()->user();

        $items = Notification::for($user)
            ->recent(10)
            ->get();

        return response()->json([
            'unread' => Notification::unreadCount($user),
            'notifications' => $items->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'icon' => $n->icon,
                'color' => $n->color,
                'action_url' => $n->action_url,
                'action_label' => $n->action_label,
                'is_read' => $n->isRead(),
                'ago' => $n->ago(),
            ]),
        ]);
    }

    public function show(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return view('admin.notifications.show', compact('notification'));
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false], 403);
            }
            abort(403);
        }

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread' => Notification::unreadCount(auth()->user()),
            ]);
        }

        return back()->with('success', __('Marked as read.'));
    }

    public function markAsUnread(Request $request, Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false], 403);
            }
            abort(403);
        }

        $notification->markAsUnread();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread' => Notification::unreadCount(auth()->user()),
            ]);
        }

        return back()->with('success', __('Marked as unread.'));
    }

    public function markAllRead(Request $request)
    {
        $count = Notification::markAllRead(auth()->user());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => __(':n notification(s) marked as read.', ['n' => $count]),
            ]);
        }

        return back()->with('success', __(':n notification(s) marked as read.', ['n' => $count]));
    }

    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false], 403);
            }
            abort(403);
        }

        $notification->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Notification deleted.'));
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:rj_notifications,id',
            'action' => 'required|in:read,unread,delete',
        ]);

        $user = auth()->user();

        $query = Notification::for($user)->whereIn('id', $validated['ids']);

        $count = match ($validated['action']) {
            'read' => $query->update(['read_at' => now()]),
            'unread' => $query->update(['read_at' => null]),
            'delete' => $query->delete(),
            default => 0,
        };

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'unread' => Notification::unreadCount($user),
            ]);
        }

        return back()->with('success', __(':n notification(s) updated.', ['n' => $count]));
    }

    public function settings()
    {
        $user = auth()->user();

        $events = NotificationSetting::events();
        $channels = NotificationSetting::channels();
        $settings = NotificationSetting::forUser($user);

        return view('admin.notifications.settings', compact('events', 'channels', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        $user = auth()->user();

        foreach ($validated['settings'] as $event => $channels) {
            if (!is_array($channels)) continue;

            foreach ($channels as $channel => $enabled) {
                NotificationSetting::setEnabled($user, (string) $event, (string) $channel, (bool) $enabled);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Notification settings saved.'));
    }
}