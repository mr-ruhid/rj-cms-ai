<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiBuilderService;
use Illuminate\Http\Request;

class AiBuilderController extends Controller
{
    protected AiBuilderService $ai;

    public function __construct(AiBuilderService $ai)
    {
        $this->ai = $ai;
    }

    public function status()
    {
        return response()->json([
            'enabled' => $this->ai->enabled(),
            'model' => rj_get_option('ai_default_model', 'openai/gpt-4o-mini'),
        ]);
    }

    public function generateText(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:2000',
            'context' => 'nullable|string|max:2000',
            'tone' => 'nullable|in:professional,friendly,casual,formal,playful,luxury',
            'length' => 'nullable|in:short,medium,long',
            'language' => 'nullable|string|max:10',
        ]);

        $result = $this->ai->generateText($validated['prompt'], $validated);

        return response()->json($result);
    }

    public function improveText(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:10000',
            'mode' => 'nullable|in:improve,shorten,expand,fix,formal,casual',
            'language' => 'nullable|string|max:10',
        ]);

        $result = $this->ai->improveText($validated['text'], $validated);

        return response()->json($result);
    }

    public function translateText(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:10000',
            'target_locale' => 'required|string|max:10',
            'source_locale' => 'nullable|string|max:10',
        ]);

        $result = $this->ai->translateText(
            $validated['text'],
            $validated['target_locale'],
            $validated['source_locale'] ?? ''
        );

        return response()->json($result);
    }

    public function suggestSection(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:2000',
            'language' => 'nullable|string|max:10',
        ]);

        $result = $this->ai->suggestSection($validated['description'], $validated);

        return response()->json($result);
    }
}