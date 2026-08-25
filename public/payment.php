<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if (empty(Cart::items())) {
    header('Location: cart.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
$lines = Cart::lines();
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= t('payment') ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="light-screen">
    <?php render_topbar(false); ?>
    <?php render_stepper('payment'); ?>

    <div class="payment-headline"><?= $lang === 'zh' ? '請選擇支付方式，或直接感應卡片…' : 'Choose a payment method, or tap your card directly…' ?></div>

    <div class="payment-columns">
        <div class="payment-col-left">
            <h3><?= $lang === 'zh' ? '訂單摘要' : 'Order Summary' ?></h3>
            <?php foreach ($lines as $line): ?>
                <div class="payment-order-line">
                    <span><?= htmlspecialchars(item_name($line['item'])) ?> ×<?= $line['qty'] ?></span>
                    <span><?= money($line['lineTotal']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row" style="margin-top:var(--sp-2);"><span><?= t('subtotal') ?></span><span><?= money(Cart::subtotal()) ?></span></div>
            <?php if (Cart::promo()): ?>
                <div class="summary-row"><span class="save"><?= t('promo_discount') ?></span><span class="save">−<?= money(Cart::discount()) ?></span></div>
            <?php endif; ?>
            <div class="summary-row"><span><?= t('tax') ?></span><span><?= money(Cart::tax()) ?></span></div>
            <div class="cart-total-row">
                <span class="lbl"><?= t('total') ?></span>
                <span class="cart-total-big"><?= money(Cart::total()) ?></span>
            </div>
        </div>

        <div class="payment-col-right">
            <div class="pay-grid-3">
                <a class="pay-tile" href="pay_card.php">
                    <div class="ico brand-card"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="30" height="30"><rect x="2.5" y="5.5" width="19" height="13" rx="2.2"/><path d="M2.5 9.5h19"/><path d="M6 14.5h4"/></svg></div>
                    <span><?= t('credit_card') ?></span>
                </a>
                <a class="pay-tile" href="pay_mobile.php?scheme=linepay">
                    <div class="ico brand-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="30" height="30"><path d="M12 3C6.9 3 2.8 6.4 2.8 10.6c0 3.8 3.3 7 7.7 7.6-.3 1-.9 3.3-1 3.8-.1.6.2.6.5.4.2-.1 3.4-2.3 4.8-3.2 5-.5 8.7-3.6 8.7-8.5C23.5 6.4 17.1 3 12 3Z"/></svg></div>
                    <span>LINE Pay</span>
                </a>
                <a class="pay-tile" href="pay_mobile.php?scheme=applepay">
                    <div class="ico brand-apple"><svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path d="M16.5 3c.1 1.1-.3 2.1-.9 2.9-.6.8-1.6 1.4-2.6 1.3-.1-1 .4-2.1 1-2.8.6-.8 1.7-1.4 2.5-1.4ZM19.7 17c-.5 1.1-.7 1.6-1.4 2.6-.9 1.4-2.2 3.1-3.8 3.1-1.4 0-1.8-.9-3.7-.9-1.9 0-2.4.9-3.7.9-1.6 0-2.8-1.5-3.7-2.9C1 16.6.6 12.1 2.4 9.4c1-1.5 2.6-2.4 4.1-2.4 1.5 0 2.5 1 3.7 1 1.2 0 2-1 3.7-1 1.3 0 2.7.7 3.7 2-3.3 1.8-2.8 6.4 2.1 8Z"/></svg></div>
                    <span>Apple Pay</span>
                </a>
            </div>
            <a class="giftcode-link" href="pay_giftcode.php"><?= $lang === 'zh' ? '有優惠禮品卡代碼？點此輸入' : 'Have a gift code? Enter it here' ?></a>
        </div>
    </div>
</div>
</body>
</html>
