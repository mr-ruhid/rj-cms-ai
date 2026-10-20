<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Models\Theme;
use Illuminate\Support\Facades\DB;

class ToolsDiagnosticsController extends Controller
{
    public function index()
    {
        $groups = [
            'server' => $this->getServerInfo(),
            'database' => $this->getDatabaseInfo(),
            'rj_cms' => $this->getCmsInfo(),
            'paths' => $this->getPathsInfo(),
            'security' => $this->getSecurityInfo(),
        ];

        return view('admin.tools.diagnostics', compact('groups'));
    }

    protected function getServerInfo(): array
    {
        return [
            'Operating System' => PHP_OS . ' (' . php_uname('m') . ')',
            'Web Server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'PHP Version' => PHP_VERSION,
            'PHP SAPI' => PHP_SAPI,
            'PHP Timezone' => date_default_timezone_get(),
            'Memory Limit' => ini_get('memory_limit'),
            'Max Upload Size' => ini_get('upload_max_filesize'),
            'Max Post Size' => ini_get('post_max_size'),
            'Max Execution Time' => ini_get('max_execution_time') . 's',
            'cURL Extension' => extension_loaded('curl') ? 'Enabled' : 'Disabled',
            'GD/Imagick' => extension_loaded('gd') || extension_loaded('imagick') ? 'Enabled' : 'Disabled',
            'ZIP Extension' => extension_loaded('zip') ? 'Enabled' : 'Disabled',
        ];
    }

    protected function getDatabaseInfo(): array
    {
        $driver = config('database.default');
        $version = 'Unknown';

        try {
            $version = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Throwable $e) {
        }

        return [
            'Driver' => $driver,
            'Version' => $version,
            'Host' => config("database.connections.{$driver}.host", 'localhost'),
            'Database Name' => config("database.connections.{$driver}.database"),
            'Table Prefix' => config("database.connections.{$driver}.prefix") ?: 'rj_',
            'Charset' => config("database.connections.{$driver}.charset", 'utf8mb4'),
            'Collation' => config("database.connections.{$driver}.collation", 'utf8mb4_unicode_ci'),
        ];
    }

    protected function getCmsInfo(): array
    {
        $activeTheme = Theme::active();
        $activePlugins = Plugin::where('status', 'active')->count();

        return [
            'RJ CMS Version' => rj_get_option('rj_version', '1.0.0'),
            'Active Theme' => $activeTheme ? $activeTheme->name . ' (v' . $activeTheme->version . ')' : 'None',
            'Active Plugins' => $activePlugins . ' plugin(s)',
            'App Environment' => app()->environment(),
            'App Debug' => config('app.debug') ? 'Enabled' : 'Disabled',
            'App Locale' => config('app.locale'),
            'Cache Driver' => config('cache.default'),
            'Session Driver' => config('session.driver'),
            'Queue Driver' => config('queue.default'),
        ];
    }

    protected function getPathsInfo(): array
    {
        return [
            'App URL' => config('app.url'),
            'Base Path' => base_path(),
            'Public Path' => public_path(),
            'Storage Path' => storage_path(),
            'Cache Path' => base_path('bootstrap/cache'),
            'Log Path' => storage_path('logs'),
        ];
    }

    protected function getSecurityInfo(): array
    {
        return [
            'HTTPS' => request()->isSecure() ? 'Enabled' : 'Disabled',
            'APP_KEY' => config('app.key') ? 'Set' : 'Missing',
            'Session Secure' => config('session.secure') ? 'Yes' : 'No',
            'Session HttpOnly' => config('session.http_only') ? 'Yes' : 'No',
            'Session SameSite' => config('session.same_site', 'null'),
        ];
    }
}