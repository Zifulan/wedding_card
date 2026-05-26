<?php
/**
 * TEMPORARY SETUP SCRIPT — DELETE AFTER USE
 * Upload this to public/setup.php, visit it once, then DELETE it.
 */

// Basic protection — change this token before uploading
define('SETUP_TOKEN', 'wedding-setup-2025');

if (($_GET['token'] ?? '') !== SETUP_TOKEN) {
    die('Access denied. Add ?token=wedding-setup-2025 to the URL.');
}

// Boot Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$results = [];

function run(string $command, array $args = []): string {
    ob_start();
    try {
        $exitCode = Artisan::call($command, $args);
        $out = Artisan::output();
        return trim($out) ?: "✓ Done (exit: $exitCode)";
    } catch (\Throwable $e) {
        return "✗ Error: " . $e->getMessage();
    } finally {
        ob_end_clean();
    }
}

$action = $_GET['action'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Wedding App Setup</title>
<style>
    body { font-family: monospace; background:#0a0a0f; color:#e8e8f0; padding:30px; max-width:700px; margin:0 auto; }
    h1 { color:#c9a84c; font-size:22px; margin-bottom:6px; }
    h2 { color:#c9a84c; font-size:15px; margin:24px 0 8px; }
    .warn { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.4); padding:12px 16px; border-radius:8px; color:#fca5a5; margin-bottom:20px; font-size:13px; }
    .output { background:#111; border:1px solid #2a2a42; border-radius:8px; padding:16px; white-space:pre-wrap; font-size:13px; color:#86efac; margin-bottom:16px; }
    .btn { display:inline-block; padding:10px 22px; background:#c9a84c; color:#0a0a0f; border-radius:8px; text-decoration:none; font-weight:700; font-size:14px; margin:6px 6px 6px 0; }
    .btn-outline { background:transparent; border:1px solid #c9a84c; color:#c9a84c; }
    .btn-danger { background:#ef4444; color:white; }
    p { font-size:13px; color:#6b7280; line-height:1.7; }
    code { background:#1a1a2e; padding:2px 6px; border-radius:4px; color:#c9a84c; }
</style>
</head>
<body>
<h1>🔧 Wedding App Setup</h1>
<div class="warn">⚠ SECURITY WARNING — Delete this file immediately after setup is complete.</div>

<?php if ($action === 'key'): ?>
<h2>Generating App Key</h2>
<div class="output"><?= htmlspecialchars(run('key:generate', ['--force' => true])) ?></div>

<?php elseif ($action === 'migrate'): ?>
<h2>Running Migrations</h2>
<div class="output"><?= htmlspecialchars(run('migrate', ['--force' => true])) ?></div>

<?php elseif ($action === 'seed'): ?>
<h2>Seeding Admin User</h2>
<div class="output"><?= htmlspecialchars(run('db:seed', ['--class' => 'AdminSeeder', '--force' => true])) ?></div>

<?php elseif ($action === 'cache'): ?>
<h2>Caching Config / Routes / Views</h2>
<div class="output"><?= htmlspecialchars(
    run('config:cache') . "\n" .
    run('route:cache') . "\n" .
    run('view:cache')
) ?></div>

<?php elseif ($action === 'clear'): ?>
<h2>Clearing All Cache</h2>
<div class="output"><?= htmlspecialchars(
    run('config:clear') . "\n" .
    run('route:clear') . "\n" .
    run('view:clear') . "\n" .
    run('cache:clear')
) ?></div>

<?php elseif ($action === 'info'): ?>
<h2>Environment Info</h2>
<div class="output"><?= htmlspecialchars(
    "PHP: " . PHP_VERSION . "\n" .
    "Laravel: " . app()->version() . "\n" .
    "APP_KEY set: " . (config('app.key') ? 'YES' : 'NO') . "\n" .
    "DB: " . config('database.default') . " @ " . config('database.connections.mysql.host') . "\n" .
    "DB Name: " . config('database.connections.mysql.database') . "\n" .
    "APP_URL: " . config('app.url') . "\n" .
    "Storage writable: " . (is_writable(storage_path()) ? 'YES' : 'NO ← fix permissions')
) ?></div>

<?php else: ?>
<p>Run each step in order. Click a button to execute that command.</p>
<?php endif; ?>

<h2>Setup Steps</h2>
<a class="btn <?= $action==='info' ? '' : 'btn-outline' ?>" href="?token=<?= SETUP_TOKEN ?>&action=info">1. Check Environment</a>
<a class="btn <?= $action==='key' ? '' : 'btn-outline' ?>"  href="?token=<?= SETUP_TOKEN ?>&action=key">2. Generate App Key</a>
<a class="btn <?= $action==='migrate' ? '' : 'btn-outline' ?>" href="?token=<?= SETUP_TOKEN ?>&action=migrate">3. Run Migrations</a>
<a class="btn <?= $action==='seed' ? '' : 'btn-outline' ?>" href="?token=<?= SETUP_TOKEN ?>&action=seed">4. Seed Admin User</a>
<a class="btn <?= $action==='cache' ? '' : 'btn-outline' ?>" href="?token=<?= SETUP_TOKEN ?>&action=cache">5. Cache Config/Routes</a>

<br><br>
<hr style="border-color:#2a2a42; margin:20px 0">
<p>Troubleshooting:</p>
<a class="btn btn-outline" href="?token=<?= SETUP_TOKEN ?>&action=clear">Clear All Cache</a>

<br><br>
<hr style="border-color:#2a2a42; margin:20px 0">
<p style="color:#ef4444">
    ✅ After setup is complete — <strong>DELETE this file</strong> from File Manager!<br>
    Path: <code>public/setup.php</code>
</p>
</body>
</html>
