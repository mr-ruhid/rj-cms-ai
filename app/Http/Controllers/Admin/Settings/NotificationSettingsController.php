<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        $defaults = [
            'admin_email' => rj_get_option('admin_email', config('mail.from.address', 'admin@example.com')),
            'events' => [
                'new_comment' => true,
                'new_user' => true,
                'post_published' => false,
                'update_available' => true,
                'failed_login' => true,
                'form_submission' => true,
            ],
        ];

        $rawSettings = rj_get_option('notifications_settings');
        $settings = $rawSettings ? json_decode($rawSettings, true) : $defaults;
        
        // Əgər köhnə ayarlarda hansısa event yoxdursa, default dəyərləri birləşdiririk
        $settings['events'] = array_merge($defaults['events'], $settings['events'] ?? []);

        return view('admin.settings.notifications', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'admin_email' => 'required|email|max:255',
            'events' => 'nullable|array',
            'events.*' => 'boolean',
        ]);

        $events = [
            'new_comment' => $request->boolean('events.new_comment'),
            'new_user' => $request->boolean('events.new_user'),
            'post_published' => $request->boolean('events.post_published'),
            'update_available' => $request->boolean('events.update_available'),
            'failed_login' => $request->boolean('events.failed_login'),
            'form_submission' => $request->boolean('events.form_submission'),
        ];

        $settings = [
            'admin_email' => $validated['admin_email'],
            'events' => $events,
        ];

        rj_set_option('notifications_settings', json_encode($settings));
        rj_flush_options();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Notification settings saved.'),
            ]);
        }

        return back()->with('success', __('Notification settings saved.'));
    }
}