<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if (empty(Cart::items())) {
    header('Location: cart.php');
    exit;
}
$simulateDecline = isset($_GET['simulate']) && $_GET['simulate'] === 'decline';
$lang = $_SESSION['lang'] ?? 'en';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= $lang === 'zh' ? '刷卡付款' : 'Credit Card' ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="light-screen">
    <?php render_topbar(false); ?>
    <?php render_stepper('payment'); ?>

    <div class="light-card">
        <?php if ($simulateDecline): ?>
            <div>
                <div style="font-size:3.4rem;">⚠️</div>
                <h3 style="color:var(--danger);margin:8px 0;font-size:var(--step-xl);font-weight:900;"><?= $lang === 'zh' ? '付款失敗' : 'Payment Declined' ?></h3>
                <p style="color:var(--label-secondary);"><?= $lang === 'zh' ? '尚未扣款，請重新嘗試或選擇其他付款方式。' : 'Your card was not charged. Please try again or choose another payment method.' ?></p>
                <div style="display:flex;gap:12px;justify-content:center;margin-top:var(--sp-4);">
                    <a class="btn btn-primary" href="pay_card.php"><?= $lang === 'zh' ? '重新嘗試' : 'Try Again' ?></a>
                    <a class="btn btn-outline" href="payment.php"><?= $lang === 'zh' ? '其他方式' : 'Other Method' ?></a>
                </div>
            </div>
        <?php else: ?>
            <div class="reader-cue">
                <div class="tap-icon">💳</div>
                <div class="big-text"><?= $lang === 'zh' ? '請感應或插入卡片' : 'Tap or Insert Card' ?></div>
                <p style="color:var(--label-secondary);margin:0 0 var(--sp-4);"><?= $lang === 'zh' ? '請在右下方讀卡機感應您的卡片' : 'Use the card reader at the bottom-right of the machine' ?></p>
                <span class="reader-arrow br">↘</span>
            </div>
            <div class="divider"></div>
            <div class="summary-row total"><span><?= t('total') ?></span><span><?= money(Cart::total()) ?></span></div>
            <div style="text-align:center;margin-top:var(--sp-4);">
                <div class="spinner-ring"></div>
                <p style="font-weight:700;color:var(--label-secondary);"><?= $lang === 'zh' ? '處理中' : 'Processing' ?>...</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php if (!$simulateDecline): ?>
<script>
setTimeout(function(){ window.location.href = 'receipt.php?method=card'; }, 2600);
</script>
<?php endif; ?>
</body>
</html>
