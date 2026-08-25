<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if (empty(Cart::items())) {
    header('Location: cart.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
$scheme = (string)($_GET['scheme'] ?? 'generic');
$schemeNames = [
    'linepay' => ['en' => 'LINE Pay', 'zh' => 'LINE Pay'],
    'applepay' => ['en' => 'Apple Pay', 'zh' => 'Apple Pay'],
    'generic' => ['en' => 'Mobile Payment', 'zh' => '行動支付'],
];
$schemeName = $schemeNames[$scheme] ?? $schemeNames['generic'];
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= htmlspecialchars($schemeName[$lang]) ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="light-screen">
    <?php render_topbar(false); ?>
    <?php render_stepper('payment'); ?>

    <div class="light-card">
        <div class="reader-cue">
            <div class="tap-icon">📱</div>
            <div class="big-text"><?= $lang === 'zh' ? "請感應{$schemeName['zh']}" : "Tap to Pay with {$schemeName['en']}" ?></div>
            <p style="color:var(--label-secondary);margin:0 0 var(--sp-4);"><?= $lang === 'zh' ? '請將手機靠近右下方感應區約 5 秒' : 'Hold your phone near the reader at the bottom-right for 5 seconds' ?></p>
            <span class="reader-arrow br">↘</span>
        </div>
        <div class="divider"></div>
        <div class="summary-row total"><span><?= t('total') ?></span><span><?= money(Cart::total()) ?></span></div>
        <div style="text-align:center;margin-top:var(--sp-4);">
            <div class="spinner-ring"></div>
            <p style="font-weight:700;color:var(--label-secondary);"><?= $lang === 'zh' ? '處理中' : 'Processing' ?>...</p>
        </div>
    </div>
</div>
<script>
setTimeout(function(){ window.location.href = 'receipt.php?method=<?= urlencode($scheme) ?>'; }, 2200);
</script>
</body>
</html>
