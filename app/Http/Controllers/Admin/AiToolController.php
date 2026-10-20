<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;

class AiToolController extends Controller
{
    protected OpenRouterService $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    public function index()
    {
        $postTypes = \App\Models\PostType::active()->get();
        $locales = rj_available_locales();

        return view('admin.ai.tools', compact('postTypes', 'locales'));
    }

    public function generateContent(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:200',
            'tone' => 'nullable|string|max:50',
        ]);

        $prompt = "Generate a blog post about: {$validated['topic']}. Tone: " . ($validated['tone'] ?? 'professional') . ". Return ONLY valid JSON with these keys: title, content (HTML format), excerpt, seo_title, seo_description.";

        try {
            $response = $this->openRouter->chat([
                ['role' => 'system', 'content' => 'You are an expert content writer. Return only valid JSON, no markdown.'],
                ['role' => 'user', 'content' => $prompt]
            ], [
                'temperature' => 0.7,
                'model' => rj_get_option('ai_builder_model', 'openai/gpt-4o-mini')
            ]);

            $content = $response['choices'][0]['message']['content'] ?? '{}';
            
            if (str_starts_with(trim($content), '```')) {
                $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                $content = preg_replace('/\s*```$/', '', $content);
            }

            $data = json_decode($content, true);

            if (!$data || empty($data['title'])) {
                return response()->json(['success' => false, 'message' => __('Failed to parse AI response.')], 422);
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkTranslate(Request $request)
    {
        $validated = $request->validate([
            'target_locale' => 'required|string',
            'post_type' => 'required|string',
        ]);

        $posts = Post::where('type', $validated['post_type'])
            ->where('status', 'published')
            ->get();

        $count = 0;

        foreach ($posts as $post) {
            if ($post->translations()->where('locale', $validated['target_locale'])->exists()) {
                continue;
            }
            
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => __(':n items are ready to be translated. (Queue functionality to be implemented).', ['n' => $count])
        ]);
    }

    public function seoAudit()
    {
        $posts = Post::where('status', 'published')->get();
        $missing = [];

        foreach ($posts as $post) {
            $seoTitle = $post->seo_title;
            $seoDesc = $post->seo_description;

            if (empty($seoTitle) || empty($seoDesc)) {
                $missing[] = [
                    'id' => $post->id,
                    'title' => $post->title ?: __('(no title)'),
                    'url' => route('admin.posts.edit', $post),
                ];
            }
        }

        return response()->json(['success' => true, 'missing' => $missing]);
    }
}