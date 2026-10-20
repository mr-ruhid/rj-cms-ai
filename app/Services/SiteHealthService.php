<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Option;
use App\Models\Plugin;
use App\Models\Post;
use App\Models\Theme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SiteHealthService
{
    public function check(): array
    {
        $checks = [];

        $checks = array_merge($checks, $this->checkEnvironment());
        $checks = array_merge($checks, $this->checkStorage());
        $checks = array_merge($checks, $this->checkDatabase());
        $checks = array_merge($checks, $this->checkSecurity());
        $checks = array_merge($checks, $this->checkPerformance());
        $checks = array_merge($checks, $this->checkContent());
        $checks = array_merge($checks, $this->checkLogs());
        $checks = array_merge($checks, $this->checkIntegrations());

        return $checks;
    }

    public function summary(array $checks): array
    {
        $counts = ['good' => 0, 'warning' => 0, 'critical' => 0];

        foreach ($checks as $c) {
            $counts[$c['status']] = ($counts[$c['status']] ?? 0) + 1;
        }

        $total = count($checks);
        $score = 100;

        if ($total > 0) {
            $score = (int) round((($counts['good'] * 100) + ($counts['warning'] * 60)) / $total);
        }

        return [
            'score' => max(0, min(100, $score)),
            'good' => $counts['good'],
            'warning' => $counts['warning'],
            'critical' => $counts['critical'],
            'total' => $total,
        ];
    }

    protected function check(string $id, string $category, string $label, string $status, string $message, $value = null, ?string $recommendation = null): array
    {
        return [
            'id' => $id,
            'category' => $category,
            'label' => $label,
            'status' => $status,
            'message' => $message,
            'value' => $value,
            'recommendation' => $recommendation,
        ];
    }

    protected function checkEnvironment(): array
    {
        $items = [];

        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.2.0', '>=');
        $items[] = $this->check(
            'php_version',
            'environment',
            __('PHP Version'),
            $phpOk ? 'good' : 'critical',
            $phpVersion,
            $phpVersion,
            $phpOk ? null : __('Update PHP to 8.2 or higher.')
        );

        $laravelVersion = app()->version();
        $items[] = $this->check(
            'laravel_version',
            'environment',
            __('Laravel Version'),
            version_compare($laravelVersion, '11.0.0', '>=') ? 'good' : 'warning',
            $laravelVersion,
            $laravelVersion
        );

        $appKey = config('app.key');
        $items[] = $this->check(
            'app_key',
            'environment',
            __('Application Key'),
            $appKey ? 'good' : 'critical',
            $appKey ? __('Set') : __('Missing'),
            $appKey ? substr($appKey, 0, 12) . '...' : null,
            $appKey ? null : __('Run php artisan key:generate')
        );

        $env = app()->environment();
        $items[] = $this->check(
            'app_env',
            'environment',
            __('Environment'),
            $env === 'production' ? 'good' : 'warning',
            $env,
            $env,
            $env === 'production' ? null : __('Set APP_ENV=production when live.')
        );

        $debug = config('app.debug');
        $isProd = $env === 'production';
        $items[] = $this->check(
            'app_debug',
            'environment',
            __('Debug Mode'),
            (!$debug || !$isProd) ? 'good' : 'critical',
            $debug ? __('Enabled') : __('Disabled'),
            $debug ? 'true' : 'false',
            ($debug && $isProd) ? __('Disable APP_DEBUG in production.') : null
        );

        $timezone = config('app.timezone');
        $items[] = $this->check(
            'timezone',
            'environment',
            __('Timezone'),
            $timezone ? 'good' : 'warning',
            $timezone ?? __('Not set'),
            $timezone
        );

        $https = request()->isSecure() || str_starts_with(config('app.url', ''), 'https://');
        $items[] = $this->check(
            'https',
            'environment',
            __('HTTPS'),
            $https ? 'good' : ($isProd ? 'critical' : 'warning'),
            $https ? __('Enabled') : __('Disabled'),
            $https ? 'on' : 'off',
            $https ? null : __('Enable HTTPS for security.')
        );

        $memory = ini_get('memory_limit');
        $memoryBytes = $this->parseSize($memory);
        $memOk = $memoryBytes === -1 || $memoryBytes >= 128 * 1024 * 1024;
        $items[] = $this->check(
            'memory_limit',
            'environment',
            __('Memory Limit'),
            $memOk ? 'good' : 'warning',
            $memory,
            $memory,
            $memOk ? null : __('Set memory_limit to at least 128M.')
        );

        $uploadMax = ini_get('upload_max_filesize');
        $uploadBytes = $this->parseSize($uploadMax);
        $uploadOk = $uploadBytes >= 2 * 1024 * 1024;
        $items[] = $this->check(
            'upload_max',
            'environment',
            __('Upload Max Filesize'),
            $uploadOk ? 'good' : 'warning',
            $uploadMax,
            $uploadMax,
            $uploadOk ? null : __('Set upload_max_filesize to at least 2M for media uploads.')
        );

        $postMax = ini_get('post_max_size');
        $items[] = $this->check(
            'post_max',
            'environment',
            __('Post Max Size'),
            $this->parseSize($postMax) >= $uploadBytes ? 'good' : 'warning',
            $postMax,
            $postMax
        );

        $maxExec = ini_get('max_execution_time');
        $execOk = (int) $maxExec === 0 || (int) $maxExec >= 60;
        $items[] = $this->check(
            'max_execution',
            'environment',
            __('Max Execution Time'),
            $execOk ? 'good' : 'warning',
            $maxExec . 's',
            $maxExec,
            $execOk ? null : __('Increase max_execution_time to 60s or more.')
        );

        return $items;
    }

    protected function checkStorage(): array
    {
        $items = [];

        $storagePath = storage_path();
        $storageWritable = is_writable($storagePath);
        $items[] = $this->check(
            'storage_writable',
            'storage',
            __('Storage Writable'),
            $storageWritable ? 'good' : 'critical',
            $storageWritable ? __('Writable') : __('Not writable'),
            $storagePath,
            $storageWritable ? null : __('Make storage/ writable (chmod 755).')
        );

        $cachePath = base_path('bootstrap/cache');
        $cacheWritable = is_writable($cachePath);
        $items[] = $this->check(
            'bootstrap_cache',
            'storage',
            __('Bootstrap Cache Writable'),
            $cacheWritable ? 'good' : 'critical',
            $cacheWritable ? __('Writable') : __('Not writable'),
            $cachePath,
            $cacheWritable ? null : __('Make bootstrap/cache writable.')
        );

        $envExists = File::exists(base_path('.env'));
        $items[] = $this->check(
            'env_file',
            'storage',
            __('.env File'),
            $envExists ? 'good' : 'critical',
            $envExists ? __('Present') : __('Missing'),
            null,
            $envExists ? null : __('Create .env file from .env.example.')
        );

        $publicStorage = public_path('storage');
        $linkOk = file_exists($publicStorage) || is_link($publicStorage);
        $items[] = $this->check(
            'storage_link',
            'storage',
            __('Public Storage Link'),
            $linkOk ? 'good' : 'warning',
            $linkOk ? __('Linked') : __('Missing'),
            null,
            $linkOk ? null : __('Run php artisan storage:link')
        );

        $diskFree = @disk_free_space(base_path());
        $diskTotal = @disk_total_space(base_path());
        $diskPercent = ($diskFree && $diskTotal) ? round(($diskFree / $diskTotal) * 100, 1) : null;
        $diskStatus = 'good';
        if ($diskPercent !== null) {
            if ($diskPercent < 5) $diskStatus = 'critical';
            elseif ($diskPercent < 15) $diskStatus = 'warning';
        }
        $items[] = $this->check(
            'disk_space',
            'storage',
            __('Disk Free Space'),
            $diskStatus,
            $diskFree ? $this->formatSize($diskFree) . ' / ' . $this->formatSize($diskTotal) : __('Unknown'),
            $diskPercent !== null ? $diskPercent . '% free' : null,
            $diskStatus === 'good' ? null : __('Free up disk space.')
        );

        return $items;
    }

    protected function checkDatabase(): array
    {
        $items = [];

        try {
            DB::connection()->getPdo();
            $connected = true;
        } catch (\Throwable $e) {
            $connected = false;
        }

        $items[] = $this->check(
            'db_connection',
            'database',
            __('Database Connection'),
            $connected ? 'good' : 'critical',
            $connected ? __('Connected') : __('Failed'),
            null,
            $connected ? null : __('Check database credentials in .env.')
        );

        if (!$connected) {
            return $items;
        }

        $driver = config('database.default');
        $version = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        $items[] = $this->check(
            'db_version',
            'database',
            __('Database Engine'),
            'good',
            strtoupper($driver) . ' ' . $version,
            $version
        );

        try {
            $dbName = config('database.connections.' . $driver . '.database');
            $sizeRow = DB::selectOne("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            $dbSize = (float) ($sizeRow->size ?? 0);
            $items[] = $this->check(
                'db_size',
                'database',
                __('Database Size'),
                $dbSize < 500 ? 'good' : ($dbSize < 2000 ? 'warning' : 'warning'),
                $dbSize . ' MB',
                $dbSize
            );
        } catch (\Throwable $e) {
        }

        try {
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            $items[] = $this->check(
                'db_tables',
                'database',
                __('Database Tables'),
                $tableCount > 0 ? 'good' : 'warning',
                $tableCount . ' ' . __('tables'),
                $tableCount
            );
        } catch (\Throwable $e) {
        }

        $hasPosts = Schema::hasTable('rj_posts');
        $items[] = $this->check(
            'db_schema',
            'database',
            __('RJ CMS Schema'),
            $hasPosts ? 'good' : 'critical',
            $hasPosts ? __('Installed') : __('Missing tables'),
            null,
            $hasPosts ? null : __('Run php artisan migrate')
        );

        $pending = $this->pendingMigrations();
        $items[] = $this->check(
            'db_migrations',
            'database',
            __('Migrations'),
            $pending === 0 ? 'good' : 'warning',
            $pending === 0 ? __('Up to date') : $pending . ' ' . __('pending'),
            $pending,
            $pending > 0 ? __('Run php artisan migrate') : null
        );

        return $items;
    }

    protected function checkSecurity(): array
    {
        $items = [];

        $sessionDriver = config('session.driver');
        $items[] = $this->check(
            'session_driver',
            'security',
            __('Session Driver'),
            in_array($sessionDriver, ['database', 'redis', 'file'], true) ? 'good' : 'warning',
            $sessionDriver,
            $sessionDriver
        );

        $sessionSecure = config('session.secure', false);
        $items[] = $this->check(
            'session_secure',
            'security',
            __('Secure Cookies'),
            $sessionSecure ? 'good' : 'warning',
            $sessionSecure ? __('Enabled') : __('Disabled'),
            $sessionSecure,
            $sessionSecure ? null : __('Set SESSION_SECURE_COOKIE=true if using HTTPS.')
        );

        $adminCount = \App\Models\User::where('role', 'admin')->orWhere('role', 'super_admin')->count();
        $items[] = $this->check(
            'admin_user',
            'security',
            __('Admin Users'),
            $adminCount > 0 ? 'good' : 'critical',
            $adminCount . ' ' . __('admin(s)'),
            $adminCount,
            $adminCount > 0 ? null : __('Create an admin account.')
        );

        $weakPasswords = \App\Models\User::whereNotNull('password')
            ->get()
            ->filter(fn($u) => strlen($u->password) < 4)
            ->count();

        $items[] = $this->check(
            'csrf',
            'security',
            __('CSRF Protection'),
            'good',
            __('Enabled'),
            'Laravel'
        );

        $hsts = request()->header('Strict-Transport-Security') !== null;
        $items[] = $this->check(
            'hsts',
            'security',
            __('HSTS Header'),
            $hsts ? 'good' : 'warning',
            $hsts ? __('Sent') : __('Not sent'),
            $hsts,
            $hsts ? null : __('Configure web server to send Strict-Transport-Security header.')
        );

        return $items;
    }

    protected function checkPerformance(): array
    {
        $items = [];

        $cacheDriver = config('cache.default');
        $cacheOk = in_array($cacheDriver, ['redis', 'memcached', 'database', 'file'], true);
        $items[] = $this->check(
            'cache_driver',
            'performance',
            __('Cache Driver'),
            $cacheDriver === 'file' ? 'warning' : 'good',
            $cacheDriver,
            $cacheDriver,
            $cacheDriver === 'file' ? __('Consider Redis or Memcached for better performance.') : null
        );

        $opcache = function_exists('opcache_get_status') && ini_get('opcache.enable');
        $items[] = $this->check(
            'opcache',
            'performance',
            __('OPcache'),
            $opcache ? 'good' : 'warning',
            $opcache ? __('Enabled') : __('Disabled'),
            $opcache,
            $opcache ? null : __('Enable OPcache for PHP performance.')
        );

        $queue = config('queue.default');
        $items[] = $this->check(
            'queue_driver',
            'performance',
            __('Queue Driver'),
            in_array($queue, ['redis', 'database'], true) ? 'good' : 'warning',
            $queue,
            $queue,
            $queue === 'sync' ? __('Use redis or database queue for background jobs.') : null
        );

        $routeCached = app()->routesAreCached();
        $items[] = $this->check(
            'route_cache',
            'performance',
            __('Route Cache'),
            $routeCached ? 'good' : ($this->isProduction() ? 'warning' : 'good'),
            $routeCached ? __('Cached') : __('Not cached'),
            $routeCached,
            (!$routeCached && $this->isProduction()) ? __('Run php artisan route:cache') : null
        );

        $configCached = app()->configurationIsCached();
        $items[] = $this->check(
            'config_cache',
            'performance',
            __('Config Cache'),
            $configCached ? 'good' : ($this->isProduction() ? 'warning' : 'good'),
            $configCached ? __('Cached') : __('Not cached'),
            $configCached,
            (!$configCached && $this->isProduction()) ? __('Run php artisan config:cache') : null
        );

        return $items;
    }

    protected function checkContent(): array
    {
        $items = [];

        $postCount = Post::where('type', 'post')->count();
        $pageCount = Post::where('type', 'page')->count();
        $items[] = $this->check(
            'content_posts',
            'content',
            __('Posts'),
            $postCount > 0 ? 'good' : 'warning',
            $postCount . ' ' . __('posts'),
            $postCount,
            $postCount === 0 ? __('Create your first post.') : null
        );

        $items[] = $this->check(
            'content_pages',
            'content',
            __('Pages'),
            $pageCount > 0 ? 'good' : 'warning',
            $pageCount . ' ' . __('pages'),
            $pageCount
        );

        $activeTheme = Theme::active();
        $items[] = $this->check(
            'active_theme',
            'content',
            __('Active Theme'),
            $activeTheme ? 'good' : 'critical',
            $activeTheme ? $activeTheme->name : __('None'),
            $activeTheme?->slug,
            $activeTheme ? null : __('Activate a theme in Appearance → Themes.')
        );

        $mediaCount = Media::count();
        $items[] = $this->check(
            'media_count',
            'content',
            __('Media Files'),
            'good',
            $mediaCount . ' ' . __('files'),
            $mediaCount
        );

        $activePlugins = Plugin::where('status', 'active')->count();
        $items[] = $this->check(
            'active_plugins',
            'content',
            __('Active Plugins'),
            'good',
            $activePlugins . ' ' . __('active'),
            $activePlugins
        );

        return $items;
    }

    protected function checkLogs(): array
    {
        $items = [];

        $logPath = storage_path('logs/laravel.log');
        $logExists = File::exists($logPath);
        $logSize = $logExists ? File::size($logPath) : 0;

        $logSizeMb = round($logSize / 1024 / 1024, 2);
        $logOk = $logSizeMb < 50;
        $items[] = $this->check(
            'log_size',
            'logs',
            __('Log File Size'),
            $logOk ? 'good' : 'warning',
            $logSizeMb . ' MB',
            $logSizeMb,
            $logOk ? null : __('Clear or rotate laravel.log.')
        );

        $recentErrors = 0;
        if ($logExists && $logSize < 5 * 1024 * 1024) {
            $content = File::get($logPath);
            $recentErrors = substr_count($content, '.ERROR:');
        }
        $items[] = $this->check(
            'log_errors',
            'logs',
            __('Recent Errors'),
            $recentErrors === 0 ? 'good' : ($recentErrors < 10 ? 'warning' : 'warning'),
            $recentErrors . ' ' . __('errors'),
            $recentErrors,
            $recentErrors > 0 ? __('Review storage/logs/laravel.log') : null
        );

        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $items[] = $this->check(
            'failed_jobs',
            'logs',
            __('Failed Jobs'),
            $failedJobs === 0 ? 'good' : 'warning',
            $failedJobs . ' ' . __('failed'),
            $failedJobs,
            $failedJobs > 0 ? __('Review and retry failed queue jobs.') : null
        );

        return $items;
    }

    protected function checkIntegrations(): array
    {
        $items = [];

        $aiEnabled = !empty(rj_get_option('ai_api_key'));
        $items[] = $this->check(
            'ai_api',
            'integrations',
            __('AI Integration'),
            $aiEnabled ? 'good' : 'warning',
            $aiEnabled ? __('Configured') : __('Not configured'),
            $aiEnabled,
            $aiEnabled ? null : __('Configure OpenRouter API key in Settings → AI.')
        );

        $mailHost = rj_get_option('mail_host');
        $items[] = $this->check(
            'mail_config',
            'integrations',
            __('Mail Configuration'),
            $mailHost ? 'good' : 'warning',
            $mailHost ? $mailHost : __('Not configured'),
            $mailHost,
            $mailHost ? null : __('Configure SMTP in Settings → Email.')
        );

        return $items;
    }

    protected function pendingMigrations(): int
    {
        try {
            $migrator = app('migrator');
            $files = $migrator->getMigrationFiles(database_path('migrations'));
            $ran = $migrator->getRepository()->getRan();
            return count(array_diff(array_keys($files), $ran));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    protected function parseSize(string $size): int
    {
        $size = trim($size);
        $last = strtolower(substr($size, -1));
        $value = (int) $size;

        return match ($last) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }

    protected function formatSize(float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    protected function isProduction(): bool
    {
        return app()->environment() === 'production';
    }
}