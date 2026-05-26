<?php
/**
 * WEB-BASED COMPOSER INSTALLER — DELETE AFTER USE
 * Downloads composer.phar and runs "composer install" via PHP exec.
 */

if (($_GET['token'] ?? '') !== 'wedding-setup-2025') {
    die('add ?token=wedding-setup-2025');
}

$root    = dirname(__DIR__);
$pharPath = $root . '/composer.phar';

// ── Helpers ───────────────────────────────────────────────────────────────────
function tag(string $txt, string $color = '#e8e8f0'): void {
    echo "<div style='color:$color;margin:2px 0'>" . nl2br(htmlspecialchars($txt)) . "</div>\n";
    flush();
    ob_flush();
}
function ok(string $t): void  { tag("✓ $t", '#86efac'); }
function err(string $t): void { tag("✗ $t", '#fca5a5'); }
function inf(string $t): void { tag("ℹ $t", '#93c5fd'); }

// Find a working exec-like function
function shellRun(string $cmd, string $cwd): array {
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $proc = @proc_open($cmd, $descriptors, $pipes, $cwd);
    if (!is_resource($proc)) return ['', 1];
    fclose($pipes[0]);
    $out  = stream_get_contents($pipes[1]);
    $out .= stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($proc);
    return [$out, $code];
}

function execAvailable(): bool {
    if (!function_exists('proc_open')) return false;
    if (in_array('proc_open', array_map('trim', explode(',', ini_get('disable_functions'))))) return false;
    return true;
}

$action = $_GET['action'] ?? '';

$css = 'font-family:monospace;background:#0a0a0f;color:#e8e8f0;padding:28px;font-size:13px;line-height:1.9';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Composer Installer</title>
<style>
  body { <?= $css ?> }
  h1  { color:#c9a84c; font-size:20px; margin-bottom:4px; }
  h3  { color:#c9a84c; font-size:14px; margin:20px 0 8px; }
  .warn { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); padding:10px 14px; border-radius:8px; color:#fca5a5; font-size:13px; margin-bottom:20px; }
  .box { background:#111827; border:1px solid #2a2a42; border-radius:10px; padding:16px 18px; margin-bottom:16px; }
  .btn { display:inline-block; padding:9px 22px; background:#c9a84c; color:#0a0a0f; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; margin:6px 6px 6px 0; }
  .btn-sm { padding:6px 14px; font-size:12px; }
  .btn-outline { background:transparent; border:1px solid #c9a84c; color:#c9a84c; }
</style>
</head>
<body>
<h1>📦 Composer Installer</h1>
<p style="color:#6b7280;margin-bottom:20px">Installs PHP dependencies (vendor/) without a terminal</p>
<div class="warn">⚠️ Delete <code>public/composer-install.php</code> after vendor/ is installed.</div>

<?php

// ── Step: check ───────────────────────────────────────────────────────────────
if ($action === '' || $action === 'check'):
?>
<div class="box">
<h3>System Check</h3>
<?php
    ok("PHP " . PHP_VERSION);
    execAvailable() ? ok("proc_open() is available — Composer can run") : err("proc_open() is disabled on this host — see manual option below");

    $phpBin = PHP_BINARY ?: 'php';
    [$ver, $code] = execAvailable() ? shellRun("$phpBin -r \"echo PHP_VERSION;\"", $root) : ['', 1];
    $code === 0 ? ok("PHP binary works: $phpBin") : err("PHP binary '$phpBin' not callable — will try 'php' fallback");

    file_exists($root . '/composer.json') ? ok("composer.json found") : err("composer.json missing");
    file_exists($root . '/composer.lock') ? ok("composer.lock found") : err("composer.lock missing");
    file_exists($pharPath) ? ok("composer.phar already present") : inf("composer.phar not yet downloaded (Step 1 will fetch it)");
    is_dir($root . '/vendor') ? inf("vendor/ already exists (Step 2 will update it)") : inf("vendor/ missing — Steps 1+2 will create it");
?>
</div>

<a class="btn" href="?token=wedding-setup-2025&action=download">Step 1: Download Composer</a>
<a class="btn btn-outline" href="?token=wedding-setup-2025&action=install">Step 2: Run composer install</a>
<a class="btn btn-outline btn-sm" href="?token=wedding-setup-2025&action=check">Re-check</a>

<?php

// ── Step: download composer.phar ─────────────────────────────────────────────
elseif ($action === 'download'):
?>
<div class="box">
<h3>Downloading Composer…</h3>
<?php
    if (file_exists($pharPath)) {
        ok("composer.phar already exists (" . number_format(filesize($pharPath)) . " bytes) — skipping download");
    } else {
        inf("Fetching from https://getcomposer.org/composer-stable.phar …");
        flush(); ob_flush();

        $ctx = stream_context_create(['http' => [
            'timeout'       => 60,
            'user_agent'    => 'PHP/' . PHP_VERSION,
            'follow_location' => true,
        ]]);

        $bytes = @file_get_contents('https://getcomposer.org/composer-stable.phar', false, $ctx);
        if ($bytes === false) {
            // fallback mirror
            inf("Primary failed — trying mirror…");
            $bytes = @file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar', false, $ctx);
        }

        if ($bytes === false) {
            err("Download failed. Your host may block outbound HTTP.");
            echo "<div style='color:#fca5a5;margin-top:12px'>👉 Manual fix: download <a href='https://getcomposer.org/composer-stable.phar' style='color:#c9a84c'>composer.phar</a> on your PC and upload it to the <strong>root</strong> of your wedding folder (next to composer.json).</div>";
        } else {
            file_put_contents($pharPath, $bytes);
            ok("Downloaded! Size: " . number_format(filesize($pharPath)) . " bytes");
        }
    }
?>
</div>
<a class="btn" href="?token=wedding-setup-2025&action=install">Step 2: Run composer install →</a>

<?php

// ── Step: composer install ────────────────────────────────────────────────────
elseif ($action === 'install'):
    set_time_limit(300); // 5 minutes
?>
<div class="box">
<h3>Running composer install…</h3>
<p style="color:#6b7280;font-size:12px;margin-bottom:12px">This may take 1–3 minutes. Do not close the page.</p>
<?php
    if (!file_exists($pharPath)) {
        err("composer.phar not found — run Step 1 first");
    } elseif (!execAvailable()) {
        err("proc_open() is disabled on this host.");
        echo "<br>";
        inf("Manual option: On your local PC, run:");
        echo "<pre style='background:#111;padding:12px;border-radius:8px;color:#c9a84c;margin-top:8px'>composer install --no-dev --optimize-autoloader\nzip -r vendor.zip vendor/</pre>";
        inf("Then upload vendor.zip via File Manager and extract it.");
    } else {
        $phpBin = PHP_BINARY ?: 'php';
        $cmd    = escapeshellarg($phpBin) . ' ' . escapeshellarg($pharPath)
                . ' install --no-dev --optimize-autoloader --no-interaction --no-ansi 2>&1';

        inf("Running: php composer.phar install --no-dev --optimize-autoloader");
        inf("Working dir: $root");
        flush(); ob_flush();

        [$out, $code] = shellRun($cmd, $root);

        // Print output line by line, colorise
        foreach (explode("\n", $out) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (str_contains($line, 'error') || str_contains($line, 'Error') || $code !== 0 && str_contains($line, '-')) {
                tag("  $line", '#fca5a5');
            } elseif (str_contains($line, 'Installing') || str_contains($line, 'Generating')) {
                tag("  $line", '#86efac');
            } else {
                tag("  $line", '#93c5fd');
            }
        }

        echo "<br>";
        if ($code === 0) {
            ok("composer install completed successfully!");
            ok("vendor/ directory created with all dependencies.");
            echo "<br><a class='btn' href='/login'>→ Go to /login</a>";
        } else {
            err("composer install exited with code $code");
            inf("Check the output above for the specific error.");
        }
    }
?>
</div>
<a class="btn btn-outline btn-sm" href="?token=wedding-setup-2025&action=check">← Back to check</a>

<?php endif; ?>

<hr style="border-color:#2a2a42;margin:28px 0">
<p style="color:#ef4444;font-size:13px">
  ✅ After vendor/ is installed — delete <code>public/composer-install.php</code> and <code>public/check.php</code> and <code>public/setup.php</code> from File Manager.
</p>

</body>
</html>
