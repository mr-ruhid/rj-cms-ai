<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class DiscussionSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('discussion');

        $data = [
            'commentOrder' => $this->commentOrder(),
            'moderationLevels' => $this->moderationLevels(),
            'avatarSources' => $this->avatarSources(),
            'commentPages' => $this->commentPages(),
        ];

        return view('admin.settings.discussion', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'default_comment_status' => ['required', 'in:open,closed'],
            'comment_registration' => ['nullable', 'boolean'],
            'close_comments_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'thread_comments' => ['nullable', 'boolean'],
            'thread_comments_depth' => ['nullable', 'integer', 'min:1', 'max:10'],
            'page_comments' => ['nullable', 'boolean'],
            'comments_per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
            'default_comments_page' => ['required', 'in:newest,oldest'],
            'comment_order' => ['required', 'in:asc,desc'],
            'comments_notify' => ['nullable', 'boolean'],
            'moderation_notify' => ['nullable', 'boolean'],
            'comment_moderation' => ['nullable', 'boolean'],
            'comment_previously_approved' => ['nullable', 'boolean'],
            'moderation_level' => ['required', 'in:none,manual,all'],
            'disallowed_keys' => ['nullable', 'string', 'max:5000'],
            'comment_max_links' => ['nullable', 'integer', 'min:0', 'max:100'],
            'show_avatars' => ['nullable', 'boolean'],
            'avatar_source' => ['nullable', 'in:gravatar,initials,disabled'],
            'avatar_rating' => ['nullable', 'in:G,PG,R,X'],
            'avatar_size' => ['nullable', 'integer', 'min:16', 'max:256'],
        ]);

        $booleanKeys = [
            'comment_registration',
            'thread_comments',
            'page_comments',
            'comments_notify',
            'moderation_notify',
            'comment_moderation',
            'comment_previously_approved',
            'show_avatars',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        $typeMap = [
            'close_comments_days' => 'number',
            'thread_comments_depth' => 'number',
            'comments_per_page' => 'number',
            'comment_max_links' => 'number',
            'avatar_size' => 'number',
        ];

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'discussion', $typeMap[$name] ?? 'text');
        }

        return back()->with('success', __('Discussion settings saved.'));
    }

    protected function commentOrder(): array
    {
        return [
            'asc' => __('Older first'),
            'desc' => __('Newer first'),
        ];
    }

    protected function moderationLevels(): array
    {
        return [
            'none' => __('No moderation'),
            'manual' => __('Hold first comment only'),
            'all' => __('Hold all comments'),
        ];
    }

    protected function avatarSources(): array
    {
        return [
            'gravatar' => __('Gravatar'),
            'initials' => __('Initials'),
            'disabled' => __('Disabled'),
        ];
    }

    protected function commentPages(): array
    {
        return [
            'newest' => __('Newest first'),
            'oldest' => __('Oldest first'),
        ];
    }
}
