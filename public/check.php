<?php
/**
 * RAW DIAGNOSTIC — DELETE AFTER USE
 * Shows bare PHP errors before Laravel even loads.
 */
if (($_GET['token'] ?? '') !== 'wedding-setup-2025') {
    die('add ?token=wedding-setup-2025');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

$root = dirname(__DIR__);

function chk(string $label, bool $pass, string $detail = ''): void {
    $icon   = $pass ? '✓' : '✗';
    $color  = $pass ? '#86efac' : '#fca5a5';
    $detail = $detail ? " <span style='color:#6b7280'>— $detail</span>" : '';
    echo "<div style='color:$color;margin:4px 0'>$icon $label$detail</div>\n";
}

$css = 'font-family:monospace;background:#0a0a0f;color:#e8e8f0;padding:28px;font-size:13px;line-height:1.8';
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'></head><body style='$css'>";
echo "<h2 style='color:#c9a84c;margin-bottom:16px'>Raw Diagnostic</h2>";

// 1. PHP basics
echo "<h3 style='color:#c9a84c;margin:16px 0 6px'>PHP</h3>";
chk("PHP " . PHP_VERSION, PHP_MAJOR_VERSION >= 8 && PHP_MINOR_VERSION >= 2);
chk("pdo_mysql", extension_loaded('pdo_mysql'));
chk("mbstring",  extension_loaded('mbstring'));
chk("openssl",   extension_loaded('openssl'));
chk("tokenizer", extension_loaded('tokenizer'));
chk("fileinfo",  extension_loaded('fileinfo'));

// 2. Critical files
echo "<h3 style='color:#c9a84c;margin:16px 0 6px'>Critical Files</h3>";
$files = [
    'vendor/autoload.php',
    'bootstrap/app.php',
    'bootstrap/providers.php',
    'public/.htaccess',
    '.env',
];
foreach ($files as $f) {
    $full = $root . '/' . ltrim($f, '/');
    chk($f, file_exists($full), file_exists($full) ? number_format(filesize($full)) . ' bytes' : 'MISSING');
}

// 3. Storage dirs
echo "<h3 style='color:#c9a84c;margin:16px 0 6px'>Storage Directories</h3>";
$dirs = [
    'storage/logs',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache/data',
    'bootstrap/cache',
];
foreach ($dirs as $d) {
    $full = $root . '/' . $d;
    $exists   = is_dir($full);
    $writable = $exists && is_writable($full);
    if (!$exists) {
        @mkdir($full, 0755, true);
        $exists   = is_dir($full);
        $writable = $exists && is_writable($full);
    }
    chk($d, $writable, $writable ? 'writable' : ($exists ? 'exists but NOT writable' : 'missing + could not create'));
}

// 4. Try writing to log manually
echo "<h3 style='color:#c9a84c;margin:16px 0 6px'>Log Write Test</h3>";
$logDir  = $root . '/storage/logs';
$logFile = $logDir . '/laravel.log';
$testMsg = "[" . date('Y-m-d H:i:s') . "] diagnostic.INFO: test write\n";
if (@file_put_contents($logFile, $testMsg, FILE_APPEND) !== false) {
    chk("Can write to storage/logs/laravel.log", true);
} else {
    chk("Can write to storage/logs/laravel.log", false, "Directory exists: " . (is_dir($logDir) ? 'yes' : 'no') . ", writable: " . (is_writable($logDir) ? 'yes' : 'no'));
}

// 5. Try loading vendor/autoload.php and catch any errors
echo "<h3 style='color:#c9a84c;margin:16px 0 6px'>Vendor Autoload</h3>";
$autoload = $root . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "<div style='color:#fca5a5'>✗ vendor/autoload.php missing — composer install was not run, or vendor/ was not uploaded</div>";
} else {
    try {
        require $autoload;
        chk("vendor/autoload.php loaded OK", true);

        // Try loading .env with Dotenv
        if (class_exists('\Dotenv\Dotenv')) {
            chk("Dotenv class found", true);
        }

        // Try booting the app
        echo "<h3 style='color:#c9a84c;margin:16px 0 6px'>Laravel Boot Test</h3>";
        try {
            $app = require $root . '/bootstrap/app.php';
            chk("bootstrap/app.php loaded", true);

            $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
            chk("HTTP kernel created", true);
        } catch (\Throwable $e) {
            echo "<div style='color:#fca5a5'>✗ Laravel boot failed: <br><pre style='color:#fca5a5;white-space:pre-wrap'>" . htmlspecialchars($e->getMessage() . "\n\nFile: " . $e->getFile() . ":" . $e->getLine()) . "</pre></div>";
        }

    } catch (\Throwable $e) {
        echo "<div style='color:#fca5a5'>✗ vendor/autoload.php failed: <pre style='white-space:pre-wrap'>" . htmlspecialchars($e->getMessage()) . "</pre></div>";
    }
}

// 6. Show .env key values
echo "<h3 style='color:#c9a84c;margin:16px 0 6px'>.env Snapshot</h3>";
$envFile = $root . '/.env';
if (file_exists($envFile)) {
    $important = ['APP_ENV','APP_KEY','APP_URL','DB_CONNECTION','DB_HOST','DB_DATABASE','DB_USERNAME','SESSION_DRIVER','CACHE_STORE'];
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        foreach ($important as $key) {
            if (str_starts_with($line, $key . '=')) {
                $display = (str_contains($key, 'KEY') || str_contains($key, 'PASSWORD'))
                    ? $key . '=[set]'
                    : htmlspecialchars($line);
                echo "<div style='color:#93c5fd'>$display</div>";
            }
        }
    }
} else {
    echo "<div style='color:#fca5a5'>✗ .env not found</div>";
}

echo "</body></html>";
