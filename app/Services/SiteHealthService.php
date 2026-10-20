<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Plugin;
use App\Models\Post;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SiteHealthService
{
    public function check(): array
    {
        $groups = [
            'checkEnvironment',
            'checkStorage',
            'checkDatabase',
            'checkSecurity',
            'checkPerformance',
            'checkContent',
            'checkLogs',
            'checkIntegrations',
        ];

        $checks = [];

        foreach ($groups as $group) {
            try {
                $checks = array_merge($checks, $this->{$group}());
            } catch (\Throwable $e) {
                // Bir qrup xəta versə, bütün səhifə çökməsin
                $checks[] = $this->makeCheck(
                    'group_' . $group,
                    $this->categoryOf($group),
                    __('Check group failed'),
                    'warning',
                    __('Could not run this group of checks.'),
                    $e->getMessage()
                );
            }
        }

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

        // Kritik problem varsa skor 89-dan yuxarı çıxmasın
        if ($counts['critical'] > 0) {
            $score = min($score, 89);
        }

        return [
            'score' => max(0, min(100, $score)),
            'good' => $counts['good'],
            'warning' => $counts['warning'],
            'critical' => $counts['critical'],
            'total' => $total,
        ];
    }

    protected function categoryOf(string $method): string
    {
        return strtolower(substr($method, 5));
    }

    protected function makeCheck(string $id, string $category, string $label, string $status, $message, $value = null, ?string $recommendation = null): array
    {
        return [
            'id' => $id,
            'category' => $category,
            'label' => $label,
            'status' => $status,
            'message' => is_scalar($message) || $message === null ? (string) $message : json_encode($message),
            'value' => $value,
            'recommendation' => $recommendation,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Environment                                                         */
    /* ------------------------------------------------------------------ */

    protected function checkEnvironment(): array
    {
        $items = [];

        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.2.0', '>=');
        $items[] = $this->makeCheck(
            'php_version', 'environment', __('PHP Version'),
            $phpOk ? 'good' : 'critical',
            $phpVersion, $phpVersion,
            $phpOk ? null : __('Update PHP to 8.2 or higher.')
        );

        $laravelVersion = app()->version();
        $items[] = $this->makeCheck(
            'laravel_version', 'environment', __('Laravel Version'),
            version_compare($laravelVersion, '11.0.0', '>=') ? 'good' : 'warning',
            $laravelVersion, $laravelVersion
        );

        $appKey = config('app.key');
        $items[] = $this->makeCheck(
            'app_key', 'environment', __('Application Key'),
            $appKey ? 'good' : 'critical',
            $appKey ? __('Set') : __('Missing'),
            null, // açarın heç bir hissəsi göstərilmir / AI-yə göndərilmir
            $appKey ? null : __('Run php artisan key:generate')
        );

        $env = app()->environment();
        $isProd = $env === 'production';
        $items[] = $this->makeCheck(
            'app_env', 'environment', __('Environment'),
            $isProd ? 'good' : 'warning',
            $env, $env,
            $isProd ? null : __('Set APP_ENV=production when live.')
        );

        $debug = (bool) config('app.debug');
        $items[] = $this->makeCheck(
            'app_debug', 'environment', __('Debug Mode'),
            ($debug && $isProd) ? 'critical' : 'good',
            $debug ? __('Enabled') : __('Disabled'),
            $debug ? 'true' : 'false',
            ($debug && $isProd) ? __('Disable APP_DEBUG in production.') : null
        );

        $timezone = config('app.timezone');
        $items[] = $this->makeCheck(
            'timezone', 'environment', __('Timezone'),
            $timezone ? 'good' : 'warning',
            $timezone ?: __('Not set'), $timezone
        );

        $https = request()->isSecure() || str_starts_with((string) config('app.url', ''), 'https://');
        $items[] = $this->makeCheck(
            'https', 'environment', __('HTTPS'),
            $https ? 'good' : ($isProd ? 'critical' : 'warning'),
            $https ? __('Enabled') : __('Disabled'),
            $https ? 'on' : 'off',
            $https ? null : __('Enable HTTPS for security.')
        );

        if (!app()->runningInConsole()) {
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $reqHost = request()->getHost();
            $hostOk = !$appHost || strcasecmp($appHost, $reqHost) === 0;
            $items[] = $this->makeCheck(
                'app_url', 'environment', __('APP_URL'),
                $hostOk ? 'good' : 'warning',
                $hostOk ? __('Matches current host') : __('Differs from current host'),
                $appHost,
                $hostOk ? null : __('Set APP_URL in .env to :host.', ['host' => $reqHost])
            );
        }

        $memory = (string) ini_get('memory_limit');
        $memoryBytes = $this->parseSize($memory);
        $memOk = $memoryBytes === -1 || $memoryBytes >= 128 * 1024 * 1024;
        $items[] = $this->makeCheck(
            'memory_limit', 'environment', __('Memory Limit'),
            $memOk ? 'good' : 'warning',
            $memory, $memory,
            $memOk ? null : __('Set memory_limit to at least 128M.')
        );

        $uploadMax = (string) ini_get('upload_max_filesize');
        $uploadBytes = $this->parseSize($uploadMax);
        $uploadOk = $uploadBytes === -1 || $uploadBytes >= 2 * 1024 * 1024;
        $items[] = $this->makeCheck(
            'upload_max', 'environment', __('Upload Max Filesize'),
            $uploadOk ? 'good' : 'warning',
            $uploadMax, $uploadMax,
            $uploadOk ? null : __('Set upload_max_filesize to at least 2M for media uploads.')
        );

        $postMax = (string) ini_get('post_max_size');
        $postBytes = $this->parseSize($postMax);
        $postOk = $postBytes === -1 || $postBytes >= $uploadBytes;
        $items[] = $this->makeCheck(
            'post_max', 'environment', __('Post Max Size'),
            $postOk ? 'good' : 'warning',
            $postMax, $postMax,
            $postOk ? null : __('post_max_size should be equal to or larger than upload_max_filesize.')
        );

        $maxExec = (string) ini_get('max_execution_time');
        $execOk = (int) $maxExec === 0 || (int) $maxExec >= 60;
        $items[] = $this->makeCheck(
            'max_execution', 'environment', __('Max Execution Time'),
            $execOk ? 'good' : 'warning',
            $maxExec . 's', $maxExec,
            $execOk ? null : __('Increase max_execution_time to 60s or more.')
        );

        $required = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'curl'];
        $missing = array_values(array_filter($required, fn($e) => !extension_loaded($e)));
        $items[] = $this->makeCheck(
            'php_extensions', 'environment', __('Required PHP Extensions'),
            empty($missing) ? 'good' : 'critical',
            empty($missing) ? __('All installed') : __('Missing: :list', ['list' => implode(', ', $missing)]),
            $missing,
            empty($missing) ? null : __('Install and enable: :list', ['list' => implode(', ', $missing)])
        );

        $optionalMissing = [];
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            $optionalMissing[] = 'gd/imagick';
        }
        foreach (['zip', 'intl'] as $ext) {
            if (!extension_loaded($ext)) {
                $optionalMissing[] = $ext;
            }
        }
        $items[] = $this->makeCheck(
            'php_extensions_optional', 'environment', __('Recommended PHP Extensions'),
            empty($optionalMissing) ? 'good' : 'warning',
            empty($optionalMissing) ? __('All installed') : __('Missing: :list', ['list' => implode(', ', $optionalMissing)]),
            $optionalMissing,
            empty($optionalMissing) ? null : __('Install for image processing and archive support: :list', ['list' => implode(', ', $optionalMissing)])
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Storage                                                             */
    /* ------------------------------------------------------------------ */

    protected function checkStorage(): array
    {
        $items = [];

        $writable = [
            ['storage_writable', __('Storage Writable'), storage_path(), __('Make storage/ writable (chmod -R 775 storage).')],
            ['storage_uploads', __('Uploads Folder Writable'), storage_path('app/public'), __('Make storage/app/public writable.')],
            ['bootstrap_cache', __('Bootstrap Cache Writable'), base_path('bootstrap/cache'), __('Make bootstrap/cache writable.')],
        ];

        foreach ($writable as [$id, $label, $path, $hint]) {
            $ok = is_dir($path) && is_writable($path);
            $items[] = $this->makeCheck(
                $id, 'storage', $label,
                $ok ? 'good' : 'critical',
                $ok ? __('Writable') : __('Not writable'),
                $path,
                $ok ? null : $hint
            );
        }

        $envExists = File::exists(base_path('.env'));
        $items[] = $this->makeCheck(
            'env_file', 'storage', __('.env File'),
            $envExists ? 'good' : 'critical',
            $envExists ? __('Present') : __('Missing'),
            null,
            $envExists ? null : __('Create .env file from .env.example.')
        );

        $publicStorage = public_path('storage');
        $linkOk = file_exists($publicStorage) || is_link($publicStorage);
        $items[] = $this->makeCheck(
            'storage_link', 'storage', __('Public Storage Link'),
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
            if ($diskPercent < 5) {
                $diskStatus = 'critical';
            } elseif ($diskPercent < 15) {
                $diskStatus = 'warning';
            }
        }
        $items[] = $this->makeCheck(
            'disk_space', 'storage', __('Disk Free Space'),
            $diskStatus,
            ($diskFree && $diskTotal) ? $this->formatSize($diskFree) . ' / ' . $this->formatSize($diskTotal) : __('Unknown'),
            $diskPercent !== null ? $diskPercent . '% free' : null,
            $diskStatus === 'good' ? null : __('Free up disk space.')
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Database                                                            */
    /* ------------------------------------------------------------------ */

    protected function checkDatabase(): array
    {
        $items = [];

        try {
            $conn = DB::connection();
            $conn->getPdo();
            $connected = true;
        } catch (\Throwable $e) {
            $connected = false;
        }

        $items[] = $this->makeCheck(
            'db_connection', 'database', __('Database Connection'),
            $connected ? 'good' : 'critical',
            $connected ? __('Connected') : __('Failed'),
            null,
            $connected ? null : __('Check database credentials in .env.')
        );

        if (!$connected) {
            return $items;
        }

        $driver = $conn->getDriverName();
        $dbName = $conn->getDatabaseName();

        try {
            $version = $conn->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Throwable $e) {
            $version = '?';
        }
        $items[] = $this->makeCheck(
            'db_version', 'database', __('Database Engine'),
            'good', strtoupper($driver) . ' ' . $version, $version
        );

        try {
            $sizeMb = null;

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $row = DB::selectOne(
                    'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.TABLES WHERE table_schema = ?',
                    [$dbName]
                );
                $sizeMb = (float) ($row->size ?? 0);
            } elseif ($driver === 'pgsql') {
                $row = DB::selectOne('SELECT pg_database_size(current_database()) AS size');
                $sizeMb = round(((float) ($row->size ?? 0)) / 1024 / 1024, 2);
            } elseif ($driver === 'sqlite' && $dbName && is_file($dbName)) {
                $sizeMb = round(filesize($dbName) / 1024 / 1024, 2);
            }

            if ($sizeMb !== null) {
                $items[] = $this->makeCheck(
                    'db_size', 'database', __('Database Size'),
                    'good', $sizeMb . ' MB', $sizeMb
                );
            }
        } catch (\Throwable $e) {
        }

        try {
            $tableCount = count(Schema::getTables());
            $items[] = $this->makeCheck(
                'db_tables', 'database', __('Database Tables'),
                $tableCount > 0 ? 'good' : 'warning',
                $tableCount . ' ' . __('tables'), $tableCount
            );
        } catch (\Throwable $e) {
        }

        $hasPosts = Schema::hasTable('rj_posts');
        $items[] = $this->makeCheck(
            'db_schema', 'database', __('RJ CMS Schema'),
            $hasPosts ? 'good' : 'critical',
            $hasPosts ? __('Installed') : __('Missing tables'),
            null,
            $hasPosts ? null : __('Run php artisan migrate')
        );

        $pending = $this->pendingMigrations();
        $items[] = $this->makeCheck(
            'db_migrations', 'database', __('Migrations'),
            $pending === 0 ? 'good' : 'warning',
            $pending === 0 ? __('Up to date') : $pending . ' ' . __('pending'),
            $pending,
            $pending > 0 ? __('Run php artisan migrate') : null
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Security                                                            */
    /* ------------------------------------------------------------------ */

    protected function checkSecurity(): array
    {
        $items = [];

        $sessionDriver = (string) config('session.driver');
        $items[] = $this->makeCheck(
            'session_driver', 'security', __('Session Driver'),
            in_array($sessionDriver, ['database', 'redis', 'file', 'memcached', 'dynamodb'], true) ? 'good' : 'warning',
            $sessionDriver, $sessionDriver,
            in_array($sessionDriver, ['array', 'cookie'], true) ? __('Use database, redis or file sessions.') : null
        );

        $https = request()->isSecure() || str_starts_with((string) config('app.url', ''), 'https://');
        $sessionSecure = (bool) config('session.secure', false);
        $secureStatus = $sessionSecure ? 'good' : ($https ? 'warning' : 'good');
        $items[] = $this->makeCheck(
            'session_secure', 'security', __('Secure Cookies'),
            $secureStatus,
            $sessionSecure ? __('Enabled') : ($https ? __('Disabled') : __('Not required (no HTTPS)')),
            $sessionSecure,
            ($https && !$sessionSecure) ? __('Set SESSION_SECURE_COOKIE=true if using HTTPS.') : null
        );

        $httpOnly = (bool) config('session.http_only', true);
        $items[] = $this->makeCheck(
            'session_http_only', 'security', __('HttpOnly Cookies'),
            $httpOnly ? 'good' : 'warning',
            $httpOnly ? __('Enabled') : __('Disabled'),
            $httpOnly,
            $httpOnly ? null : __('Set SESSION_HTTP_ONLY=true.')
        );

        $sameSite = config('session.same_site');
        $items[] = $this->makeCheck(
            'session_same_site', 'security', __('SameSite Cookies'),
            in_array($sameSite, ['lax', 'strict'], true) ? 'good' : 'warning',
            $sameSite ?: __('Not set'),
            $sameSite,
            in_array($sameSite, ['lax', 'strict'], true) ? null : __('Set SESSION_SAME_SITE=lax.')
        );

        $adminCount = User::whereIn('role', ['admin', 'super_admin'])->count();
        $items[] = $this->makeCheck(
            'admin_user', 'security', __('Admin Users'),
            $adminCount > 0 ? 'good' : 'critical',
            $adminCount . ' ' . __('admin(s)'),
            $adminCount,
            $adminCount > 0 ? null : __('Create an admin account.')
        );

        $items[] = $this->makeCheck(
            'csrf', 'security', __('CSRF Protection'),
            'good', __('Enabled'), 'Laravel'
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Performance                                                         */
    /* ------------------------------------------------------------------ */

    protected function checkPerformance(): array
    {
        $items = [];
        $prod = $this->isProduction();

        $cacheDriver = (string) config('cache.default');
        $items[] = $this->makeCheck(
            'cache_driver', 'performance', __('Cache Driver'),
            in_array($cacheDriver, ['file', 'array', 'null'], true) ? 'warning' : 'good',
            $cacheDriver, $cacheDriver,
            in_array($cacheDriver, ['file', 'array', 'null'], true) ? __('Consider Redis or Memcached for better performance.') : null
        );

        $opcache = function_exists('opcache_get_status') && filter_var(ini_get('opcache.enable'), FILTER_VALIDATE_BOOLEAN);
        $items[] = $this->makeCheck(
            'opcache', 'performance', __('OPcache'),
            $opcache ? 'good' : 'warning',
            $opcache ? __('Enabled') : __('Disabled'),
            $opcache,
            $opcache ? null : __('Enable OPcache for PHP performance.')
        );

        $queue = (string) config('queue.default');
        $items[] = $this->makeCheck(
            'queue_driver', 'performance', __('Queue Driver'),
            $queue === 'sync' || $queue === 'null' ? 'warning' : 'good',
            $queue, $queue,
            $queue === 'sync' ? __('Use redis or database queue for background jobs.') : null
        );

        $routeCached = app()->routesAreCached();
        $items[] = $this->makeCheck(
            'route_cache', 'performance', __('Route Cache'),
            ($routeCached || !$prod) ? 'good' : 'warning',
            $routeCached ? __('Cached') : __('Not cached'),
            $routeCached,
            (!$routeCached && $prod) ? __('Run php artisan route:cache') : null
        );

        $configCached = app()->configurationIsCached();
        $items[] = $this->makeCheck(
            'config_cache', 'performance', __('Config Cache'),
            ($configCached || !$prod) ? 'good' : 'warning',
            $configCached ? __('Cached') : __('Not cached'),
            $configCached,
            (!$configCached && $prod) ? __('Run php artisan config:cache') : null
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Content                                                             */
    /* ------------------------------------------------------------------ */

    protected function checkContent(): array
    {
        $items = [];

        $postCount = Post::where('type', 'post')->count();
        $items[] = $this->makeCheck(
            'content_posts', 'content', __('Posts'),
            $postCount > 0 ? 'good' : 'warning',
            $postCount . ' ' . __('posts'), $postCount,
            $postCount === 0 ? __('Create your first post.') : null
        );

        $pageCount = Post::where('type', 'page')->count();
        $items[] = $this->makeCheck(
            'content_pages', 'content', __('Pages'),
            $pageCount > 0 ? 'good' : 'warning',
            $pageCount . ' ' . __('pages'), $pageCount
        );

        $activeTheme = Theme::active();
        $items[] = $this->makeCheck(
            'active_theme', 'content', __('Active Theme'),
            $activeTheme ? 'good' : 'critical',
            $activeTheme ? $activeTheme->name : __('None'),
            $activeTheme?->slug,
            $activeTheme ? null : __('Activate a theme in Appearance → Themes.')
        );

        $mediaCount = Media::count();
        $items[] = $this->makeCheck(
            'media_count', 'content', __('Media Files'),
            'good', $mediaCount . ' ' . __('files'), $mediaCount
        );

        $activePlugins = Plugin::where('status', 'active')->count();
        $items[] = $this->makeCheck(
            'active_plugins', 'content', __('Active Plugins'),
            'good', $activePlugins . ' ' . __('active'), $activePlugins
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Logs                                                                */
    /* ------------------------------------------------------------------ */

    protected function checkLogs(): array
    {
        $items = [];

        $files = glob(storage_path('logs/*.log')) ?: [];
        $totalSize = 0;
        $latest = null;
        $latestTime = 0;

        foreach ($files as $f) {
            $totalSize += (int) @filesize($f);
            $mt = (int) @filemtime($f);
            if ($mt >= $latestTime) {
                $latestTime = $mt;
                $latest = $f;
            }
        }

        $logSizeMb = round($totalSize / 1024 / 1024, 2);
        $logOk = $logSizeMb < 50;
        $items[] = $this->makeCheck(
            'log_size', 'logs', __('Log Files Size'),
            $logOk ? 'good' : 'warning',
            $logSizeMb . ' MB', $logSizeMb,
            $logOk ? null : __('Clear or rotate files in storage/logs (use the daily log channel).')
        );

        // Son 24 saatın səhvləri (faylın yalnız son hissəsi oxunur)
        $recentErrors = 0;
        if ($latest) {
            try {
                $content = $this->tailFile($latest, 512 * 1024);
                $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})[^\]]*\]\s+\S+\.(?:ERROR|CRITICAL|ALERT|EMERGENCY):/m';
                if (preg_match_all($pattern, $content, $m)) {
                    $since = time() - 86400;
                    foreach ($m[1] as $ts) {
                        $t = strtotime($ts);
                        if ($t !== false && $t >= $since) {
                            $recentErrors++;
                        }
                    }
                }
            } catch (\Throwable $e) {
            }
        }
        $items[] = $this->makeCheck(
            'log_errors', 'logs', __('Errors (last 24h)'),
            $recentErrors === 0 ? 'good' : ($recentErrors > 25 ? 'critical' : 'warning'),
            $recentErrors . ' ' . __('errors'), $recentErrors,
            $recentErrors > 0 ? __('Review storage/logs for details.') : null
        );

        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $items[] = $this->makeCheck(
            'failed_jobs', 'logs', __('Failed Jobs'),
            $failedJobs === 0 ? 'good' : 'warning',
            $failedJobs . ' ' . __('failed'), $failedJobs,
            $failedJobs > 0 ? __('Review and retry failed queue jobs (php artisan queue:retry all).') : null
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Integrations                                                        */
    /* ------------------------------------------------------------------ */

    protected function checkIntegrations(): array
    {
        $items = [];

        $aiEnabled = !empty(rj_get_option('ai_api_key'));
        $items[] = $this->makeCheck(
            'ai_api', 'integrations', __('AI Integration'),
            $aiEnabled ? 'good' : 'warning',
            $aiEnabled ? __('Configured') : __('Not configured'),
            $aiEnabled,
            $aiEnabled ? null : __('Configure OpenRouter API key in Settings → AI.')
        );

        $mailHost = rj_get_option('mail_host');
        $items[] = $this->makeCheck(
            'mail_config', 'integrations', __('Mail Configuration'),
            $mailHost ? 'good' : 'warning',
            $mailHost ? __('Configured') : __('Not configured'),
            (bool) $mailHost,
            $mailHost ? null : __('Configure SMTP in Settings → Email.')
        );

        return $items;
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    protected function pendingMigrations(): int
    {
        try {
            $migrator = app('migrator');
            $files = $migrator->getMigrationFiles(database_path('migrations'));

            if (!Schema::hasTable(config('database.migrations.table', 'migrations'))) {
                return count($files);
            }

            $ran = $migrator->getRepository()->getRan();

            return count(array_diff(array_keys($files), $ran));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    protected function tailFile(string $path, int $bytes): string
    {
        $size = (int) @filesize($path);
        $handle = @fopen($path, 'rb');

        if (!$handle) {
            return '';
        }

        if ($size > $bytes) {
            fseek($handle, -$bytes, SEEK_END);
        }

        $content = stream_get_contents($handle);
        fclose($handle);

        return (string) $content;
    }

    protected function parseSize(string $size): int
    {
        $size = trim($size);

        if ($size === '' || $size === '-1') {
            return -1;
        }

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