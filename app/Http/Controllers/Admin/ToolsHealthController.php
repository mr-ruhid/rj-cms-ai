<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiHealthService;
use App\Services\SiteHealthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $report = $this->report();

        $boot = [
            'summary' => $report['summary'],
            'categories' => $report['categories'],
            'checkedAt' => $report['checked_at'],
            'aiEnabled' => $this->ai->enabled(),
            'aiModel' => $this->ai->model(),
        ];

        return view('admin.tools.health', [
            'boot' => $boot,
            'aiEnabled' => $boot['aiEnabled'],
        ]);
    }

    public function analyze(Request $request)
    {
        try {
            $checks = $this->site->check();
            $summary = $this->site->summary($checks);

            // Eyni nəticə üçün 15 dəqiqə keş (AI xərcini azaldır). force=1 ilə yenilənir.
            $signature = array_map(fn($c) => $c['id'] . ':' . $c['status'], $checks);
            $key = 'rj_ai_health_' . md5(json_encode($signature) . $this->ai->model() . app()->getLocale());

            if (!$request->boolean('force') && ($cached = Cache::get($key))) {
                return response()->json([
                    'success' => true,
                    'cached' => true,
                    'data' => $cached,
                ]);
            }

            $result = $this->ai->analyze($checks, $summary);

            if (!($result['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? __('AI analysis failed.'),
                ], 422);
            }

            Cache::put($key, $result['data'], now()->addMinutes(15));

            return response()->json([
                'success' => true,
                'cached' => false,
                'data' => $result['data'],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => __('AI analysis failed.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            Cache::forget('rj_options');
            Cache::forget('rj_active_theme');
            Cache::forget('rj_widget_registry');

            $report = $this->report();

            return response()->json([
                'success' => true,
                'message' => __('Checks updated.'),
                'summary' => $report['summary'],
                'categories' => $report['categories'],
                'checked_at' => $report['checked_at'],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function report(): array
    {
        $checks = $this->site->check();

        return [
            'summary' => $this->site->summary($checks),
            'categories' => $this->groupByCategory($checks),
            'checked_at' => now()->format('Y-m-d H:i:s'),
        ];
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

        // Kateqoriyalar həmişə eyni sıra ilə
        foreach ($labels as $key => $meta) {
            $grouped[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'items' => [],
                'counts' => ['good' => 0, 'warning' => 0, 'critical' => 0],
            ];
        }

        foreach ($checks as $check) {
            $cat = $check['category'];

            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [
                    'key' => $cat,
                    'label' => ucfirst($cat),
                    'icon' => 'circle',
                    'items' => [],
                    'counts' => ['good' => 0, 'warning' => 0, 'critical' => 0],
                ];
            }

            $grouped[$cat]['items'][] = $check;
            $grouped[$cat]['counts'][$check['status']] = ($grouped[$cat]['counts'][$check['status']] ?? 0) + 1;
        }

        $rank = ['critical' => 0, 'warning' => 1, 'good' => 2];

        foreach ($grouped as &$group) {
            // Problemli yoxlamalar yuxarıda
            usort($group['items'], fn($a, $b) => ($rank[$a['status']] ?? 3) <=> ($rank[$b['status']] ?? 3));
        }
        unset($group);

        return array_values(array_filter($grouped, fn($g) => !empty($g['items'])));
    }
}