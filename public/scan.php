<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

/**
 * Kiosk-side redemption endpoint — whatever browser loads this URL
 * becomes "the machine that just scanned this order" (in a real
 * deployment that's the machine's own always-open kiosk browser,
 * reached because its built-in scanner decodes the QR from
 * pickup_qr.php and opens the URL it encodes, exactly like QrPromo's
 * existing camera-scan-opens-a-URL pattern — no custom scanner app
 * needed). It populates THIS session's $_SESSION['last_order'] in the
 * exact shape Cart::finalizeOrder() already produces, so cooking.php
 * and ready.php need zero changes to handle a pre-paid mobile order.
 */
$lang = $_SESSION['lang'] ?? 'en';
$token = (string)($_GET['order'] ?? '');
$order = $token !== '' ? OrderStore::find($token) : null;

if ($order && $order['status'] === 'awaiting_scan') {
    $order = OrderStore::markCooking($token);
    Cart::clear();
    $_SESSION['last_order'] = [
        'number'    => $order['number'],
        'method'    => $order['method'],
        'lines'     => $order['lines'],
        'total'     => $order['total'],
        'placed_at' => $order['placed_at'],
    ];
    $_SESSION['stamps'] = min(5, (int)($_SESSION['stamps'] ?? 0) + 1);
    header('Location: cooking.php');
    exit;
}
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= $lang === 'zh' ? '掃描' : 'Scan' ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="light-screen">
    <?php render_topbar(false); ?>
    <div class="light-card" style="text-align:center;">
        <?php if (!$order): ?>
            <div style="font-size:3.4rem;">⚠️</div>
            <h3 style="color:var(--danger);margin:8px 0;"><?= $lang === 'zh' ? '找不到此訂單' : 'Order Not Found' ?></h3>
            <p style="color:var(--label-secondary);"><?= $lang === 'zh' ? 'QR code 無效或已過期，請重新點餐。' : 'This QR code is invalid or expired — please place a new order.' ?></p>
        <?php else: ?>
            <div style="font-size:3.4rem;">🍜</div>
            <h3 style="margin:8px 0;"><?= $lang === 'zh' ? '此訂單已在製作中' : 'This Order Is Already Cooking' ?></h3>
            <p style="color:var(--label-secondary);">#<?= htmlspecialchars($order['number']) ?> — <?= $lang === 'zh' ? '請至該機台留意出餐畫面。' : 'Check that machine\'s screen for pickup status.' ?></p>
        <?php endif; ?>
        <a class="btn btn-primary" style="margin-top:var(--sp-4);" href="index.php"><?= $lang === 'zh' ? '回到首頁' : 'Back to Home' ?></a>
    </div>
</div>
</body>
</html>
