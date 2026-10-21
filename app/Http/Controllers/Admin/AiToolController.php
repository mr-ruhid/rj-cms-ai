<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\OpenRouterService;
use App\Services\PostService;
use Illuminate\Http\Request;

class AiToolController extends Controller
{
    protected OpenRouterService $openRouter;
    protected PostService $postService;

    public function __construct(OpenRouterService $openRouter, PostService $postService)
    {
        $this->openRouter = $openRouter;
        $this->postService = $postService;
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

            $content = $response['content'] ?? '{}';
            
            if (str_starts_with(trim($content), '```')) {
                $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                $content = preg_replace('/\s*```$/', '', $content);
            }

            $data = json_decode($content, true);

            if (!$data || empty($data['title'])) {
                return response()->json(['success' => false, 'message' => __('Failed to parse AI response.')], 422);
            }

            $data['type'] = 'post';
            $data['status'] = 'draft';
            $data['translations'] = [
                config('app.locale') => [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'excerpt' => $data['excerpt'] ?? '',
                    'seo_title' => $data['seo_title'] ?? '',
                    'seo_description' => $data['seo_description'] ?? '',
                ]
            ];

            $post = $this->postService->create($data, $request);

            return response()->json([
                'success' => true, 
                'message' => __('Post created successfully as a draft.'),
                'redirect' => route('admin.posts.edit', $post->id)
            ]);
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
        set_time_limit(0);

        foreach ($posts as $post) {
            if ($post->translations()->where('locale', $validated['target_locale'])->exists()) {
                continue;
            }

            try {
                $prompt = "Translate the following content to language code: {$validated['target_locale']}. Return ONLY valid JSON with keys: title, content, excerpt.\n\n" . 
                    json_encode([
                        'title' => $post->title,
                        'content' => strip_tags($post->content),
                        'excerpt' => $post->excerpt
                    ], JSON_UNESCAPED_UNICODE);

                $response = $this->openRouter->chat([
                    ['role' => 'system', 'content' => 'You are a translator. Return only valid JSON, no markdown.'],
                    ['role' => 'user', 'content' => $prompt]
                ], [
                    'temperature' => 0.3,
                    'model' => rj_get_option('ai_builder_model', 'openai/gpt-4o-mini')
                ]);

                $content = $response['content'] ?? '{}';
                
                if (str_starts_with(trim($content), '```')) {
                    $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
                    $content = preg_replace('/\s*```$/', '', $content);
                }

                $translated = json_decode($content, true);

                if ($translated && !empty($translated['title'])) {
                    $this->postService->update($post, [
                        'translations' => [
                            $validated['target_locale'] => $translated
                        ]
                    ], $request);
                    
                    $count++;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => __(':n items have been successfully translated.', ['n' => $count])
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