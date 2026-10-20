<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Models\Media;
use App\Services\FreeAiService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'posts' => Post::where('type', 'post')->count(),
            'pages' => Post::where('type', 'page')->count(),
            'comments' => Comment::count(),
            'users' => User::count(),
            'media' => Media::count(),
        ];

        $recentPosts = Post::with('author')->orderByDesc('created_at')->limit(5)->get();

        $recentComments = Comment::with('post')->orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'recentComments'));
    }

    public function askAi(Request $request, FreeAiService $aiService)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $result = $aiService->ask($validated['question']);

        return response()->json($result);
    }
}