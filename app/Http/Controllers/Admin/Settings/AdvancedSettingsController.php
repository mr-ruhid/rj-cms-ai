<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Option;

class AdvancedSettingsController extends BaseSettingsController
{
    public function index()
    {
        $options = $this->getGroup('advanced');

        $data = [
            'environment' => $this->environmentInfo(),
            'logLevels' => $this->logLevels(),
        ];

        return view('admin.settings.advanced', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'debug_mode' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:1000'],
            'maintenance_retry' => ['nullable', 'integer', 'min:0', 'max:86400'],

            'log_level' => ['required', 'in:emergency,alert,critical,error,warning,notice,info,debug'],
            'log_retention_days' => ['nullable', 'integer', 'min:1', 'max:365'],

            'custom_head_code' => ['nullable', 'string', 'max:10000'],
            'custom_footer_code' => ['nullable', 'string', 'max:10000'],
            'custom_css' => ['nullable', 'string', 'max:50000'],

            'error_reporting' => ['nullable', 'boolean'],
            'track_visitors' => ['nullable', 'boolean'],
            'allow_file_editing' => ['nullable', 'boolean'],
        ]);

        $booleanKeys = [
            'debug_mode',
            'maintenance_mode',
            'error_reporting',
            'track_visitors',
            'allow_file_editing',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        $typeMap = [
            'maintenance_retry' => 'number',
            'log_retention_days' => 'number',
        ];

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'advanced', $typeMap[$name] ?? 'text');
        }

        return back()->with('success', __('Advanced settings saved.'));
    }

    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (file_exists($logPath)) {
                file_put_contents($logPath, '');
            }

            return back()->with('success', __('Log file cleared.'));
        } catch (\Throwable $e) {
            return back()->with('error', __('Failed to clear logs: :message', ['message' => $e->getMessage()]));
        }
    }

    protected function environmentInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'mysql_version' => $this->mysqlVersion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_os' => PHP_OS_FAMILY,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'environment' => app()->environment(),
        ];
    }

    protected function mysqlVersion(): string
    {
        try {
            $result = DB::select('SELECT VERSION() as version');

            return $result[0]->version ?? 'Unknown';
        } catch (\Throwable $e) {
            return 'Unknown';
        }
    }

    protected function logLevels(): array
    {
        return [
            'emergency' => 'Emergency',
            'alert' => 'Alert',
            'critical' => 'Critical',
            'error' => 'Error',
            'warning' => 'Warning',
            'notice' => 'Notice',
            'info' => 'Info',
            'debug' => 'Debug',
        ];
    }
}
