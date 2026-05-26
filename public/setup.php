<?php
/**
 * STANDALONE SETUP — no Laravel bootstrap needed.
 * Upload to public/setup.php → visit → DELETE after done.
 */

define('SETUP_TOKEN', 'wedding-setup-2025');
define('ENV_PATH', __DIR__ . '/../.env');
define('ROOT', dirname(__DIR__));

// ── Auth ────────────────────────────────────────────────────────────────────
if (($_GET['token'] ?? '') !== SETUP_TOKEN) {
    http_response_code(403);
    die('<h2 style="font-family:sans-serif;color:#c00">Access denied — add ?token=wedding-setup-2025 to the URL</h2>');
}

// ── Helpers ─────────────────────────────────────────────────────────────────
function readEnv(): array {
    $env = [];
    if (!file_exists(ENV_PATH)) return $env;
    foreach (file(ENV_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\n\r\"'");
    }
    return $env;
}

function writeEnvKey(string $key, string $value): void {
    $content = file_get_contents(ENV_PATH);
    if (preg_match('/^' . preg_quote($key, '/') . '=/m', $content)) {
        $content = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $key . '=' . $value, $content);
    } else {
        $content .= "\n{$key}={$value}";
    }
    file_put_contents(ENV_PATH, $content);
}

function pdo(array $env): PDO {
    $dsn = "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_DATABASE']};charset=utf8mb4";
    return new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

function ok(string $msg): string  { return "<span style='color:#86efac'>✓ $msg</span>"; }
function err(string $msg): string { return "<span style='color:#fca5a5'>✗ $msg</span>"; }
function info(string $msg): string{ return "<span style='color:#93c5fd'>ℹ $msg</span>"; }

// ── Actions ──────────────────────────────────────────────────────────────────
$action = $_GET['action'] ?? '';
$output = '';

// 1. Environment check
if ($action === 'info') {
    $env = readEnv();
    $lines = [
        info("PHP version: " . PHP_VERSION),
        PHP_MAJOR_VERSION >= 8 && PHP_MINOR_VERSION >= 2 ? ok("PHP 8.2+ requirement met") : err("PHP must be 8.2+, you have " . PHP_VERSION),
        extension_loaded('pdo_mysql') ? ok("pdo_mysql extension loaded") : err("pdo_mysql missing — enable in PHP Extensions"),
        extension_loaded('mbstring')  ? ok("mbstring loaded") : err("mbstring missing"),
        extension_loaded('openssl')   ? ok("openssl loaded") : err("openssl missing"),
        file_exists(ENV_PATH)         ? ok(".env file found") : err(".env not found — copy .env.example to .env"),
        is_writable(ENV_PATH)         ? ok(".env is writable") : err(".env is not writable"),
        is_writable(ROOT . '/storage') ? ok("storage/ is writable") : err("storage/ not writable — chmod 755 in File Manager"),
        is_writable(ROOT . '/bootstrap/cache') ? ok("bootstrap/cache/ is writable") : err("bootstrap/cache/ not writable"),
        !empty($env['APP_KEY'])        ? ok("APP_KEY is set") : info("APP_KEY not set yet — run Step 2"),
        info("DB_HOST: " . ($env['DB_HOST'] ?? '?')),
        info("DB_DATABASE: " . ($env['DB_DATABASE'] ?? '?')),
        info("DB_USERNAME: " . ($env['DB_USERNAME'] ?? '?')),
        info("APP_URL: " . ($env['APP_URL'] ?? '?')),
    ];
    // Test DB connection
    try {
        $db = pdo($env);
        $lines[] = ok("Database connection successful");
    } catch (\Throwable $e) {
        $lines[] = err("DB connection failed: " . $e->getMessage());
    }
    $output = implode("\n", $lines);
}

// 2. Generate APP_KEY
elseif ($action === 'key') {
    try {
        if (!file_exists(ENV_PATH)) throw new \Exception(".env file not found");
        if (!is_writable(ENV_PATH))  throw new \Exception(".env is not writable");
        $key = 'base64:' . base64_encode(random_bytes(32));
        writeEnvKey('APP_KEY', $key);
        $output = ok("APP_KEY generated and written to .env") . "\n" . info("Key: $key");
    } catch (\Throwable $e) {
        $output = err($e->getMessage());
    }
}

// 3. Run migrations (raw SQL — no Laravel)
elseif ($action === 'migrate') {
    $env = readEnv();
    $tables = [
        'migrations' => "CREATE TABLE IF NOT EXISTS `migrations` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `migration` varchar(255) NOT NULL,
            `batch` int NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'users' => "CREATE TABLE IF NOT EXISTS `users` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `email_verified_at` timestamp NULL DEFAULT NULL,
            `password` varchar(255) NOT NULL,
            `remember_token` varchar(100) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `users_email_unique` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'password_reset_tokens' => "CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
            `email` varchar(255) NOT NULL,
            `token` varchar(255) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'sessions' => "CREATE TABLE IF NOT EXISTS `sessions` (
            `id` varchar(255) NOT NULL,
            `user_id` bigint unsigned DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text,
            `payload` longtext NOT NULL,
            `last_activity` int NOT NULL,
            PRIMARY KEY (`id`),
            KEY `sessions_user_id_index` (`user_id`),
            KEY `sessions_last_activity_index` (`last_activity`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'cache' => "CREATE TABLE IF NOT EXISTS `cache` (
            `key` varchar(255) NOT NULL,
            `value` mediumtext NOT NULL,
            `expiration` int NOT NULL,
            PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'cache_locks' => "CREATE TABLE IF NOT EXISTS `cache_locks` (
            `key` varchar(255) NOT NULL,
            `owner` varchar(255) NOT NULL,
            `expiration` int NOT NULL,
            PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'jobs' => "CREATE TABLE IF NOT EXISTS `jobs` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `queue` varchar(255) NOT NULL,
            `payload` longtext NOT NULL,
            `attempts` tinyint unsigned NOT NULL,
            `reserved_at` int unsigned DEFAULT NULL,
            `available_at` int unsigned NOT NULL,
            `created_at` int unsigned NOT NULL,
            PRIMARY KEY (`id`),
            KEY `jobs_queue_index` (`queue`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'weddings' => "CREATE TABLE IF NOT EXISTS `weddings` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `groom_name` varchar(255) NOT NULL,
            `bride_name` varchar(255) NOT NULL,
            `akad_date` date DEFAULT NULL,
            `akad_time` time DEFAULT NULL,
            `resepsi_date` date DEFAULT NULL,
            `resepsi_time` time DEFAULT NULL,
            `venue_name` varchar(255) DEFAULT NULL,
            `venue_address` text,
            `gmaps_link` varchar(255) DEFAULT NULL,
            `rsvp_whatsapp` varchar(255) DEFAULT NULL,
            `love_quote` text,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'guests' => "CREATE TABLE IF NOT EXISTS `guests` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `wedding_id` bigint unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `unique_token` char(36) NOT NULL,
            `invite_url` varchar(255) DEFAULT NULL,
            `open_count` int unsigned NOT NULL DEFAULT '0',
            `last_opened_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `guests_unique_token_unique` (`unique_token`),
            KEY `guests_wedding_id_foreign` (`wedding_id`),
            CONSTRAINT `guests_wedding_id_foreign` FOREIGN KEY (`wedding_id`)
                REFERENCES `weddings` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        'rsvps' => "CREATE TABLE IF NOT EXISTS `rsvps` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `guest_id` bigint unsigned NOT NULL,
            `wedding_id` bigint unsigned NOT NULL,
            `status` enum('hadir','tidak hadir','ragu') NOT NULL DEFAULT 'ragu',
            `message` text,
            `submitted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `rsvps_guest_id_foreign` (`guest_id`),
            KEY `rsvps_wedding_id_foreign` (`wedding_id`),
            CONSTRAINT `rsvps_guest_id_foreign` FOREIGN KEY (`guest_id`)
                REFERENCES `guests` (`id`) ON DELETE CASCADE,
            CONSTRAINT `rsvps_wedding_id_foreign` FOREIGN KEY (`wedding_id`)
                REFERENCES `weddings` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    $lines = [];
    try {
        $db = pdo($env);
        $db->exec("SET FOREIGN_KEY_CHECKS=0");
        foreach ($tables as $name => $sql) {
            $db->exec($sql);
            $lines[] = ok("Table `$name` ready");
        }
        $db->exec("SET FOREIGN_KEY_CHECKS=1");
        $output = implode("\n", $lines);
    } catch (\Throwable $e) {
        $output = implode("\n", $lines) . "\n" . err($e->getMessage());
    }
}

// 4. Seed admin user
elseif ($action === 'seed') {
    $env = readEnv();
    try {
        $db = pdo($env);
        $email = 'admin@wedding.local';
        $hash  = password_hash('wedding2025', PASSWORD_BCRYPT, ['cost' => 12]);
        $now   = date('Y-m-d H:i:s');
        $stmt  = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $db->prepare("UPDATE users SET password=?, updated_at=? WHERE email=?")
               ->execute([$hash, $now, $email]);
            $output = ok("Admin user updated (email: $email)");
        } else {
            $db->prepare("INSERT INTO users (name,email,password,created_at,updated_at) VALUES (?,?,?,?,?)")
               ->execute(['Admin', $email, $hash, $now, $now]);
            $output = ok("Admin user created") . "\n" . info("Email: $email") . "\n" . info("Password: wedding2025");
        }
    } catch (\Throwable $e) {
        $output = err($e->getMessage());
    }
}

// 5. Fix permissions + create missing directories
elseif ($action === 'fixperms') {
    $lines = [];
    $dirs = [
        ROOT . '/storage',
        ROOT . '/storage/app',
        ROOT . '/storage/app/public',
        ROOT . '/storage/framework',
        ROOT . '/storage/framework/cache',
        ROOT . '/storage/framework/cache/data',
        ROOT . '/storage/framework/sessions',
        ROOT . '/storage/framework/testing',
        ROOT . '/storage/framework/views',
        ROOT . '/storage/logs',
        ROOT . '/bootstrap/cache',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
            $lines[] = ok("Created: " . str_replace(ROOT . '/', '', $dir));
        }
        if (@chmod($dir, 0755)) {
            $lines[] = ok("chmod 755: " . str_replace(ROOT . '/', '', $dir));
        } else {
            $lines[] = err("Cannot chmod: " . str_replace(ROOT . '/', '', $dir) . " — do it manually in File Manager");
        }
    }
    // Clear stale cache files
    foreach ([ROOT . '/bootstrap/cache', ROOT . '/storage/framework/views', ROOT . '/storage/framework/cache/data'] as $dir) {
        foreach (glob($dir . '/*.php') ?: [] as $f) { @unlink($f); }
        foreach (glob($dir . '/*.cache') ?: [] as $f) { @unlink($f); }
    }
    $lines[] = info("Stale cache files cleared.");
    $output = implode("\n", $lines);
}

// 6. Show Laravel error log
elseif ($action === 'log') {
    $logFile = ROOT . '/storage/logs/laravel.log';
    if (!file_exists($logFile)) {
        $output = info("No log file yet at storage/logs/laravel.log — visit /login first to trigger the error, then come back here.");
    } else {
        $content = file_get_contents($logFile);
        // Get last ~6000 chars (most recent errors)
        $content = substr($content, -6000);
        $output = htmlspecialchars($content);
    }
}

// 7. Show .env (passwords redacted)
elseif ($action === 'showenv') {
    $lines = [];
    if (!file_exists(ENV_PATH)) {
        $output = err(".env file not found");
    } else {
        foreach (file(ENV_PATH, FILE_IGNORE_NEW_LINES) as $line) {
            // Redact passwords/keys
            if (preg_match('/^(DB_PASSWORD|APP_KEY|MAIL_PASSWORD|AWS_SECRET)/i', $line)) {
                $line = preg_replace('/=.+/', '=[REDACTED]', $line);
            }
            $lines[] = htmlspecialchars($line);
        }
        $output = implode("\n", $lines);
    }
}

// 8. Clear cache only
elseif ($action === 'cache') {
    $lines = [];
    $dirs = [
        ROOT . '/bootstrap/cache',
        ROOT . '/storage/framework/views',
        ROOT . '/storage/framework/cache/data',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
        foreach (glob($dir . '/*.php') ?: [] as $f) { @unlink($f); }
        foreach (glob($dir . '/*.cache') ?: [] as $f) { @unlink($f); }
        $lines[] = ok("Cleared: " . basename($dir));
    }
    $lines[] = info("Laravel will rebuild cache automatically on next request.");
    $output = implode("\n", $lines);
}

// ── HTML ─────────────────────────────────────────────────────────────────────
$token = SETUP_TOKEN;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Wedding App Setup</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', sans-serif; background: #0a0a0f; color: #e8e8f0; padding: 28px 20px; }
.wrap { max-width: 680px; margin: 0 auto; }
h1 { font-size: 22px; color: #c9a84c; margin-bottom: 4px; }
.subtitle { font-size: 13px; color: #6b7280; margin-bottom: 24px; }
.warn { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.35); padding: 11px 16px; border-radius: 8px; color: #fca5a5; font-size: 13px; margin-bottom: 24px; }
.steps { display: flex; flex-direction: column; gap: 12px; }
.step { background: #1a1a2e; border: 1px solid #2a2a42; border-radius: 12px; overflow: hidden; }
.step-header { display: flex; align-items: center; gap: 14px; padding: 16px 20px; }
.step-num { width: 28px; height: 28px; border-radius: 50%; background: rgba(201,168,76,.15); color: #c9a84c; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.step-info { flex: 1; }
.step-title { font-size: 15px; font-weight: 600; }
.step-desc { font-size: 12px; color: #6b7280; margin-top: 2px; }
.btn { display: inline-block; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: background .15s; border: none; }
.btn-gold { background: #c9a84c; color: #0a0a0f; }
.btn-gold:hover { background: #e8c97a; }
.output { padding: 14px 20px; border-top: 1px solid #2a2a42; font-family: monospace; font-size: 13px; line-height: 1.8; white-space: pre-wrap; }
.step.active { border-color: #c9a84c; }
.divider { border: none; border-top: 1px solid #2a2a42; margin: 24px 0; }
.delete-note { margin-top: 24px; padding: 14px 18px; background: rgba(239,68,68,.08); border: 1px solid rgba(239,68,68,.25); border-radius: 10px; font-size: 13px; color: #fca5a5; line-height: 1.7; }
</style>
</head>
<body>
<div class="wrap">
<h1>🔧 Wedding App Setup</h1>
<p class="subtitle">Standalone installer — no Laravel bootstrap needed</p>

<div class="warn">⚠️ Delete <code style="background:rgba(255,255,255,.08);padding:1px 5px;border-radius:4px">public/setup.php</code> immediately after finishing all steps.</div>

<div class="steps">

<?php
$steps = [
    'info'     => ['1', 'Check Environment',          'Verify PHP, extensions, DB connection, and .env'],
    'key'      => ['2', 'Generate APP_KEY',            'Write a fresh encryption key to your .env'],
    'migrate'  => ['3', 'Run Migrations',              'Create all database tables via raw SQL'],
    'seed'     => ['4', 'Create Admin User',           'Insert admin@wedding.local / wedding2025'],
    'fixperms' => ['5', 'Fix Permissions & Dirs',      'Create storage dirs, chmod 755, clear stale cache'],
    'cache'    => ['6', 'Clear Cache',                 'Remove compiled cache files'],
    'log'      => ['🔍', 'Show Error Log',             'Read storage/logs/laravel.log — use after a 500 error'],
    'showenv'  => ['📄', 'Show .env (redacted)',       'Verify your .env values (passwords hidden)'],
];
foreach ($steps as $act => [$num, $title, $desc]):
    $isActive = $action === $act;
?>
<div class="step <?= $isActive ? 'active' : '' ?>">
    <div class="step-header">
        <div class="step-num"><?= $num ?></div>
        <div class="step-info">
            <div class="step-title"><?= $title ?></div>
            <div class="step-desc"><?= $desc ?></div>
        </div>
        <a class="btn btn-gold" href="?token=<?= $token ?>&action=<?= $act ?>">Run</a>
    </div>
    <?php if ($isActive && $output): ?>
    <div class="output"><?= $output ?></div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

</div>

<hr class="divider">

<div class="delete-note">
    ✅ <strong>After all steps succeed:</strong> go to cPanel File Manager → navigate to <code>public/setup.php</code> → Delete it.<br>
    Then visit <strong>https://wedding.zif.my.id/login</strong> and sign in.
</div>
</div>
</body>
</html>
