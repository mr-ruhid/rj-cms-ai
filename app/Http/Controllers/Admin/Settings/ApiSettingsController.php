<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiSettingsController extends Controller
{
    public function index()
    {
        $rawKeys = rj_get_option('api_keys', '[]');
        $apiKeys = json_decode($rawKeys, true) ?: [];
        
        $availableEndpoints = [
            ['method' => 'GET', 'uri' => '/api/v1/posts', 'desc' => 'List all posts'],
            ['method' => 'GET', 'uri' => '/api/v1/posts/{id}', 'desc' => 'Get a single post'],
            ['method' => 'POST', 'uri' => '/api/v1/posts', 'desc' => 'Create a new post'],
            ['method' => 'PUT', 'uri' => '/api/v1/posts/{id}', 'desc' => 'Update a post'],
            ['method' => 'DELETE', 'uri' => '/api/v1/posts/{id}', 'desc' => 'Delete a post'],
            ['method' => 'GET', 'uri' => '/api/v1/pages', 'desc' => 'List all pages'],
            ['method' => 'GET', 'uri' => '/api/v1/categories', 'desc' => 'List all categories'],
            ['method' => 'GET', 'uri' => '/api/v1/media', 'desc' => 'List all media'],
            ['method' => 'GET', 'uri' => '/api/v1/users', 'desc' => 'List all users'],
        ];

        return view('admin.settings.api', compact('apiKeys', 'availableEndpoints'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $rawKeys = rj_get_option('api_keys', '[]');
        $apiKeys = json_decode($rawKeys, true) ?: [];

        $newKey = [
            'id' => (string) Str::uuid(),
            'name' => $validated['name'],
            'public_key' => 'rj_pub_' . Str::random(32),
            'private_key' => 'rj_sec_' . Str::random(48),
            'created_at' => now()->toDateTimeString(),
            'last_used_at' => null,
        ];

        $apiKeys[] = $newKey;

        rj_set_option('api_keys', json_encode($apiKeys));
        rj_flush_options();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('API Key generated successfully.'),
                'key' => $newKey,
            ]);
        }

        return back()->with('success', __('API Key generated successfully.'));
    }

    public function revoke(Request $request, string $id)
    {
        $rawKeys = rj_get_option('api_keys', '[]');
        $apiKeys = json_decode($rawKeys, true) ?: [];

        $filtered = array_values(array_filter($apiKeys, fn($k) => $k['id'] !== $id));

        if (count($filtered) === count($apiKeys)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Key not found.')], 404);
            }
            return back()->with('error', __('Key not found.'));
        }

        rj_set_option('api_keys', json_encode($filtered));
        rj_flush_options();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('API Key revoked successfully.'),
            ]);
        }

        return back()->with('success', __('API Key revoked successfully.'));
    }
}