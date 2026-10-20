<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiHealthService;
use App\Services\SiteHealthService;
use Illuminate\Http\Request;

class ToolsHealthController extends Controller
{
    protected SiteHealthService $site;
    protected AiHealthService $ai;

    public function __construct(SiteHealthService $site, AiHealthService $ai)
    {
        $this->site = $site;
        $this->ai = $ai;
    }

    public function index()
    {
        $checks = $this->site->check();
        $summary = $this->site->summary($checks);

        $categories = $this->groupByCategory($checks);

        $aiEnabled = $this->ai->enabled();
        $aiModel = $this->ai->model();

        return view('admin.tools.health', compact(
            'checks',
            'summary',
            'categories',
            'aiEnabled',
            'aiModel'
        ));
    }

    public function analyze(Request $request)
    {
        try {
            $checks = $this->site->check();
            $summary = $this->site->summary($checks);

            $result = $this->ai->analyze($checks, $summary);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? __('AI analysis failed.'),
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            \Illuminate\Support\Facades\Cache::forget('rj_options');
            \Illuminate\Support\Facades\Cache::forget('rj_active_theme');
            \Illuminate\Support\Facades\Cache::forget('rj_widget_registry');

            return response()->json([
                'success' => true,
                'message' => __('Cache cleared. Reloading checks...'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function groupByCategory(array $checks): array
    {
        $labels = [
            'environment' => ['label' => __('Environment'), 'icon' => 'server'],
            'storage' => ['label' => __('Storage & Filesystem'), 'icon' => 'folder'],
            'database' => ['label' => __('Database'), 'icon' => 'database'],
            'security' => ['label' => __('Security'), 'icon' => 'shield'],
            'performance' => ['label' => __('Performance'), 'icon' => 'bolt'],
            'content' => ['label' => __('Content'), 'icon' => 'document'],
            'logs' => ['label' => __('Logs & Errors'), 'icon' => 'list'],
            'integrations' => ['label' => __('Integrations'), 'icon' => 'plug'],
        ];

        $grouped = [];

        foreach ($checks as $check) {
            $cat = $check['category'];
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [
                    'key' => $cat,
                    'label' => $labels[$cat]['label'] ?? ucfirst($cat),
                    'icon' => $labels[$cat]['icon'] ?? 'circle',
                    'items' => [],
                    'counts' => ['good' => 0, 'warning' => 0, 'critical' => 0],
                ];
            }
            $grouped[$cat]['items'][] = $check;
            $grouped[$cat]['counts'][$check['status']]++;
        }

        return $grouped;
    }
}