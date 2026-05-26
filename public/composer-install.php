<?php
/**
 * COMPOSER INSTALLER (CRON METHOD) — DELETE AFTER USE
 */
if (($_GET['token'] ?? '') !== 'wedding-setup-2025') {
    die('add ?token=wedding-setup-2025');
}

$root      = dirname(__DIR__);
$pharPath  = $root . '/composer.phar';
$outFile   = $root . '/composer-output.txt';
$doneFlag  = $root . '/composer-done.flag';
$action    = $_GET['action'] ?? '';

function tag(string $txt, string $color = '#e8e8f0'): void {
    echo "<div style='color:$color;margin:2px 0'>" . nl2br(htmlspecialchars($txt)) . "</div>\n";
}
function ok(string $t): void  { tag("✓ $t", '#86efac'); }
function err(string $t): void { tag("✗ $t", '#fca5a5'); }
function inf(string $t): void { tag("ℹ $t", '#93c5fd'); }

function shellRun(string $cmd, string $cwd, array $env = []): array {
    $descriptors = [0 => ['pipe','r'], 1 => ['pipe','w'], 2 => ['pipe','w']];
    $proc = @proc_open($cmd, $descriptors, $pipes, $cwd, $env ?: null);
    if (!is_resource($proc)) return ['proc_open failed', 1];
    fclose($pipes[0]);
    $out  = stream_get_contents($pipes[1]);
    $out .= stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    return [$out, proc_close($proc)];
}

// ── Actions ───────────────────────────────────────────────────────────────────

// DIRECT INSTALL (fast path — tries to run composer inline)
if ($action === 'direct') {
    set_time_limit(300);
    if (!file_exists($pharPath)) {
        err("composer.phar not found — run Step 1 first");
    } else {
        $composerHome = sys_get_temp_dir() . '/composer_' . md5($root);
        @mkdir($composerHome, 0755, true);

        $phpBin = '/usr/local/bin/php';
        foreach (['/usr/local/bin/php','/usr/bin/php','/opt/cpanel/ea-php82/root/usr/bin/php','/opt/cpanel/ea-php83/root/usr/bin/php'] as $c) {
            if (is_executable($c)) { $phpBin = $c; break; }
        }

        $phpFlags = '-d register_argc_argv=0 -d memory_limit=512M -d allow_url_fopen=1';
        $cmd = escapeshellarg($phpBin) . " $phpFlags " . escapeshellarg($pharPath)
             . ' install --no-dev --optimize-autoloader --no-interaction --no-ansi 2>&1';

        // Pass HOME + COMPOSER_HOME so Composer doesn't complain
        $env = array_merge(getenv() ?: [], [
            'HOME'         => sys_get_temp_dir(),
            'COMPOSER_HOME'=> $composerHome,
            'COMPOSER_CACHE_DIR' => $composerHome . '/cache',
            'PATH'         => '/usr/local/bin:/usr/bin:/bin',
        ]);

        inf("PHP: $phpBin");
        inf("COMPOSER_HOME: $composerHome");
        inf("Running composer install — please wait…");
        flush(); ob_flush();

        [$out, $code] = shellRun($cmd, $root, $env);

        foreach (explode("\n", $out) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (stripos($line,'error') !== false || stripos($line,'failed') !== false) tag("  $line", '#fca5a5');
            elseif (stripos($line,'installing') !== false || stripos($line,'generating') !== false) tag("  $line", '#86efac');
            else tag("  $line", '#93c5fd');
        }

        echo "<br>";
        if ($code === 0) {
            ok("✓ composer install completed!");
            ok("vendor/ is ready.");
            echo "<br><a class='btn' href='/login'>→ Go to /login</a>";
        } else {
            err("Exit code: $code — scroll up for details");
            inf("If it still fails, use the Cron Job method below (Steps 1–4).");
        }
    }
}

// Download composer.phar
elseif ($action === 'download') {
    if (file_exists($pharPath)) {
        ok("composer.phar already exists (" . number_format(filesize($pharPath)) . " bytes)");
    } else {
        $ctx   = stream_context_create(['http' => ['timeout' => 60, 'follow_location' => true]]);
        $bytes = @file_get_contents('https://getcomposer.org/composer-stable.phar', false, $ctx);
        if (!$bytes) {
            $bytes = @file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar', false, $ctx);
        }
        if ($bytes) {
            file_put_contents($pharPath, $bytes);
            ok("Downloaded! (" . number_format(filesize($pharPath)) . " bytes)");
        } else {
            err("Download failed — upload composer.phar manually to the project root");
        }
    }
}

// Write the wrapper install script (runs as CLI via cron)
if ($action === 'writescript') {
    $script = <<<'PHP'
<?php
// CLI wrapper — called by cPanel cron job
chdir(dirname(__DIR__, 0));  // already in project root
$root = __DIR__;
$log  = fopen($root . '/composer-output.txt', 'w');

function w($line) use ($log) { fwrite($log, $line . "\n"); echo $line . "\n"; }

w("=== Composer Install Started: " . date('Y-m-d H:i:s') . " ===");
w("Working dir: " . $root);

if (!file_exists($root . '/composer.phar')) {
    w("ERROR: composer.phar not found in $root");
    exit(1);
}

$candidates = [
    '/usr/local/bin/php',
    '/usr/bin/php',
    '/opt/cpanel/ea-php82/root/usr/bin/php',
    '/opt/cpanel/ea-php83/root/usr/bin/php',
    '/usr/local/php82/bin/php',
    PHP_BINARY,
];

$php = 'php';
foreach ($candidates as $c) {
    if (is_executable($c)) { $php = $c; break; }
}
w("PHP binary: $php");

$cmd  = escapeshellarg($php) . ' -d memory_limit=512M ' . escapeshellarg($root . '/composer.phar')
      . ' install --no-dev --optimize-autoloader --no-interaction --no-ansi 2>&1';
w("Command: $cmd");
w("---");

$proc = proc_open($cmd, [1 => ['pipe','w'], 2 => ['pipe','w']], $pipes, $root);
if (!is_resource($proc)) { w("ERROR: proc_open failed"); exit(1); }
while (!feof($pipes[1])) { $line = fgets($pipes[1]); if ($line !== false) w(rtrim($line)); }
fclose($pipes[1]);
$code = proc_close($proc);

w("---");
w("Exit code: $code");
w($code === 0 ? "SUCCESS — vendor/ installed!" : "FAILED — check output above");
w("=== Done: " . date('Y-m-d H:i:s') . " ===");
fclose($log);

// Write a flag file so the web page knows it's done
file_put_contents($root . '/composer-done.flag', $code === 0 ? 'success' : 'failed');
PHP;

    $scriptPath = $root . '/run-composer.php';
    file_put_contents($scriptPath, $script);
    ok("Script written to: $scriptPath");
}

// Read output file
if ($action === 'checkoutput') {
    if (!file_exists($outFile)) {
        inf("No output yet — cron job hasn't run yet. Wait 1–2 minutes then refresh.");
    } else {
        $content = file_get_contents($outFile);
        $done    = file_exists($doneFlag) ? trim(file_get_contents($doneFlag)) : null;
        if ($done === 'success') {
            echo "<div style='color:#86efac;font-size:15px;font-weight:bold;margin-bottom:12px'>✓ DONE — vendor/ installed successfully!</div>";
        } elseif ($done === 'failed') {
            echo "<div style='color:#fca5a5;font-size:15px;font-weight:bold;margin-bottom:12px'>✗ composer install failed — see log below</div>";
        } else {
            echo "<div style='color:#fde68a;margin-bottom:12px'>⏳ Still running or not started yet…</div>";
        }
        echo "<pre style='background:#111;padding:14px;border-radius:8px;font-size:12px;white-space:pre-wrap;color:#e8e8f0;max-height:500px;overflow-y:auto'>"
           . htmlspecialchars($content) . "</pre>";
        if ($done === 'success') {
            echo "<br><a class='btn' href='/login'>→ Go to /login</a>";
            // Clean up
            @unlink($doneFlag);
        }
    }
}

// ── Compute values for template ───────────────────────────────────────────────
$phpBin    = '/usr/local/bin/php';
foreach (['/usr/local/bin/php','/usr/bin/php','/opt/cpanel/ea-php82/root/usr/bin/php'] as $c) {
    if (is_executable($c)) { $phpBin = $c; break; }
}
$scriptPath = $root . '/run-composer.php';
$cronCmd    = "$phpBin $scriptPath";
$token      = 'wedding-setup-2025';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Composer Installer</title>
<style>
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
body  { font-family:'Segoe UI',monospace; background:#0a0a0f; color:#e8e8f0; padding:28px 20px; }
.wrap { max-width:700px; margin:0 auto; }
h1   { color:#c9a84c; font-size:20px; margin-bottom:4px; }
h3   { color:#c9a84c; font-size:14px; margin:24px 0 8px; }
p    { font-size:13px; color:#6b7280; line-height:1.7; }
.warn { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); padding:10px 16px; border-radius:8px; color:#fca5a5; font-size:13px; margin:16px 0; }
.box  { background:#1a1a2e; border:1px solid #2a2a42; border-radius:12px; padding:20px; margin-bottom:16px; }
.step { display:flex; gap:14px; align-items:flex-start; margin-bottom:18px; }
.num  { width:26px; height:26px; border-radius:50%; background:rgba(201,168,76,.15); color:#c9a84c; font-size:13px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; }
.step-body { flex:1; }
.step-title { font-size:14px; font-weight:600; color:#e8e8f0; margin-bottom:4px; }
.step-desc  { font-size:12px; color:#6b7280; line-height:1.6; }
.btn { display:inline-block; padding:9px 20px; background:#c9a84c; color:#0a0a0f; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; margin:8px 6px 0 0; }
.btn-outline { background:transparent; border:1px solid #c9a84c; color:#c9a84c; }
.code-box { background:#111; border:1px solid #2a2a42; border-radius:8px; padding:12px 16px; font-family:monospace; font-size:13px; color:#c9a84c; margin:10px 0; word-break:break-all; display:flex; align-items:center; justify-content:space-between; gap:12px; }
.copy-btn { background:rgba(201,168,76,.15); border:1px solid rgba(201,168,76,.3); color:#c9a84c; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; flex-shrink:0; }
.output-area { font-family:monospace; font-size:12px; line-height:1.8; }
</style>
</head>
<body>
<div class="wrap">
<h1>📦 Composer Installer</h1>
<p style="color:#6b7280;margin-bottom:20px">Installs vendor/ without terminal access</p>
<div class="warn">⚠️ Delete all helper scripts after done.</div>

<!-- Quick method -->
<div class="box" style="border-color:rgba(201,168,76,.4)">
<h3 style="margin-top:0">⚡ Quick Install (try this first)</h3>
<p style="margin-bottom:12px">Runs composer directly in the browser. Make sure <code>composer.phar</code> is downloaded first (Step 1 below), then click:</p>
<a class="btn" href="?token=<?=$token?>&action=download" style="margin-bottom:8px">1. Download composer.phar</a>
<a class="btn" href="?token=<?=$token?>&action=direct" style="background:#1a1a2e;border:1px solid #c9a84c;color:#c9a84c">2. Run Install Now</a>
<?php if ($action === 'direct'): ?>
<div class="output-area" style="margin-top:16px;background:#111;padding:14px;border-radius:8px;max-height:500px;overflow-y:auto"></div>
<?php endif; ?>
</div>

<p style="color:#6b7280;font-size:12px;margin:8px 0 16px;text-align:center">— If the quick method fails, use the Cron Job method below —</p>

<!-- Step 1: Download composer.phar -->
<div class="box">
<div class="step">
    <div class="num">1</div>
    <div class="step-body">
        <div class="step-title">Download composer.phar</div>
        <div class="step-desc">Fetches Composer onto your server</div>
        <a class="btn" href="?token=<?=$token?>&action=download">Download</a>
    </div>
</div>
<?php if ($action === 'download'): ?>
<div class="output-area"><?php /* output already printed above */ ?></div>
<?php endif; ?>
<?php if (file_exists($pharPath)): ?>
<div style="color:#86efac;font-size:12px;margin-top:8px">✓ composer.phar present (<?=number_format(filesize($pharPath))?> bytes)</div>
<?php endif; ?>
</div>

<!-- Step 2: Write installer script -->
<div class="box">
<div class="step">
    <div class="num">2</div>
    <div class="step-body">
        <div class="step-title">Create the install script</div>
        <div class="step-desc">Writes <code>run-composer.php</code> to your project root — this is the file cron will run</div>
        <a class="btn" href="?token=<?=$token?>&action=writescript">Write Script</a>
    </div>
</div>
<?php if ($action === 'writescript'): ?>
<div class="output-area"><?php /* output already printed above */ ?></div>
<?php endif; ?>
<?php if (file_exists($scriptPath)): ?>
<div style="color:#86efac;font-size:12px;margin-top:8px">✓ run-composer.php present</div>
<?php endif; ?>
</div>

<!-- Step 3: Cron job -->
<div class="box">
<div class="step">
    <div class="num">3</div>
    <div class="step-body">
        <div class="step-title">Add a cron job in cPanel</div>
        <div class="step-desc">
            Go to <strong>cPanel → Cron Jobs</strong> → scroll to <em>Add New Cron Job</em><br>
            Set it to run <strong>Once (every minute)</strong> and paste this command:
        </div>
        <div class="code-box">
            <span id="cronCmd"><?=htmlspecialchars($cronCmd)?></span>
            <button class="copy-btn" onclick="navigator.clipboard.writeText(document.getElementById('cronCmd').textContent);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',1500)">Copy</button>
        </div>
        <div class="step-desc" style="margin-top:8px">
            Set the timing to <strong>* * * * *</strong> (every minute) and click Add.<br>
            After it runs (~1 min), come back here and click Step 4.<br>
            Then <strong>delete the cron job</strong> immediately so it doesn't repeat.
        </div>
    </div>
</div>
</div>

<!-- Step 4: Check output -->
<div class="box">
<div class="step">
    <div class="num">4</div>
    <div class="step-body">
        <div class="step-title">Check the output</div>
        <div class="step-desc">After ~1 minute, click below to see if composer install succeeded</div>
        <a class="btn" href="?token=<?=$token?>&action=checkoutput">Check Output</a>
        <a class="btn btn-outline" href="?token=<?=$token?>&action=checkoutput" style="margin-left:4px">Refresh</a>
    </div>
</div>
<?php if ($action === 'checkoutput'): ?>
<div class="output-area" style="margin-top:12px"><?php /* output already printed above */ ?></div>
<?php endif; ?>
</div>

<hr style="border-color:#2a2a42;margin:24px 0">
<p style="color:#ef4444;font-size:13px;line-height:1.8">
    ✅ After vendor/ is installed, clean up:<br>
    Delete from your project root: <code>composer-install.php</code>, <code>run-composer.php</code>, <code>composer-output.txt</code>, <code>composer.phar</code><br>
    Delete from public/: <code>check.php</code>, <code>setup.php</code>
</p>
</div>
</body>
</html>
