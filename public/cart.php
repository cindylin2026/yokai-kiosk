<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            Cart::setQty((string)$id, (int)$qty);
        }
    }
    $result = null;
    if (isset($_POST['promo_code']) && trim((string)$_POST['promo_code']) !== '') {
        $result = Cart::applyPromo((string)$_POST['promo_code']) ? 'ok' : 'fail';
    }
    header('Location: cart.php' . ($result ? '?promo_result=' . $result : ''));
    exit;
}
if (isset($_GET['remove'])) {
    Cart::remove((string)$_GET['remove']);
    header('Location: cart.php');
    exit;
}

$promoResult = $_GET['promo_result'] ?? null;
$lines = Cart::lines();
$lang = $_SESSION['lang'] ?? 'en';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= t('your_cart') ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="cart-screen">
    <?php render_topbar(); ?>
    <?php render_stepper('cart'); ?>

    <h1 class="cart-heading"><?= t('your_cart') ?></h1>

    <?php if (empty($lines)): ?>
        <div class="empty-state">
            <img class="glyph" src="<?= asset('assets/brand/mascot-shiba-chef.webp') ?>" alt="">
            <p><?= t('empty_cart') ?></p>
            <a class="btn btn-primary" href="menu.php" style="margin-top:var(--sp-3);"><?= t('browse_menu') ?></a>
        </div>
    <?php else: ?>
    <div class="cart-columns">
        <div class="cart-col-left">
            <form method="post" id="promoForm">
                <?php foreach ($lines as $line): $item = $line['item']; ?>
                    <div class="cart-item-panel">
                        <div class="thumb <?= empty($item['img']) ? 'placeholder' : '' ?>">
                            <?= item_thumb_html($item) ?>
                        </div>
                        <div class="meta">
                            <h4><?= htmlspecialchars(item_name($item)) ?></h4>
                            <div class="unit"><?= money($item['price']) ?> · <?= $lang === 'zh' ? '每份' : 'each' ?></div>
                        </div>
                        <div class="qty-stepper">
                            <button type="button" onclick="stepLine('<?= $item['id'] ?>', -1)">−</button>
                            <span class="val" id="qty-<?= $item['id'] ?>" style="min-width:28px;"><?= $line['qty'] ?></span>
                            <button type="button" onclick="stepLine('<?= $item['id'] ?>', 1)">+</button>
                        </div>
                        <input type="hidden" name="qty[<?= htmlspecialchars($item['id']) ?>]" value="<?= $line['qty'] ?>">
                        <div class="line-total"><?= money($line['lineTotal']) ?></div>
                        <a class="remove" href="cart.php?remove=<?= urlencode($item['id']) ?>">✕</a>
                    </div>
                <?php endforeach; ?>

                <div class="promo-row">
                    <input type="text" name="promo_code" id="promoInput" placeholder="<?= $lang === 'zh' ? '優惠代碼 (WELCOME10)' : 'PROMO CODE (try WELCOME10)' ?>" value="<?= htmlspecialchars(Cart::promo()['code'] ?? '') ?>">
                    <button type="submit" class="btn btn-outline"><?= $lang === 'zh' ? '套用' : 'Apply' ?></button>
                </div>
            </form>
        </div>

        <div class="cart-col-right">
            <div class="cart-summary-panel">
                <h3><?= $lang === 'zh' ? '付款摘要' : 'Payment Summary' ?></h3>
                <div class="summary-row"><span><?= t('subtotal') ?></span><span><?= money(Cart::subtotal()) ?></span></div>
                <?php if (Cart::promo()): ?>
                    <div class="summary-row"><span class="save"><?= t('promo_discount') ?> (<?= htmlspecialchars(Cart::promo()['code']) ?>)</span><span class="save">−<?= money(Cart::discount()) ?></span></div>
                <?php endif; ?>
                <div class="summary-row"><span><?= t('tax') ?></span><span><?= money(Cart::tax()) ?></span></div>

                <div class="cart-total-row">
                    <span class="lbl"><?= t('total') ?></span>
                    <span class="cart-total-big"><?= money(Cart::total()) ?></span>
                </div>

                <a class="btn btn-primary btn-block" style="margin-top:var(--sp-4);" href="payment.php"><?= t('proceed_checkout') ?></a>
                <a class="btn btn-outline btn-block" style="margin-top:var(--sp-2);" href="menu.php"><?= $lang === 'zh' ? '← 回到菜單' : '← Back to Menu' ?></a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<form id="qtyForm" method="post" style="display:none;">
    <?php foreach ($lines as $line): ?>
        <input type="hidden" name="qty[<?= htmlspecialchars($line['item']['id']) ?>]" id="hidden-<?= $line['item']['id'] ?>" value="<?= $line['qty'] ?>">
    <?php endforeach; ?>
</form>

<?php if ($promoResult === 'ok'): $promo = Cart::promo(); $rg = mascot('ramen-girl'); ?>
<div class="seal-backdrop cart-elegant-scope" id="sealModal">
    <div class="seal-card">
        <button class="close-x" onclick="closeSeal()" aria-label="Close">✕</button>
        <div class="chef-widget" style="justify-content:center;margin-bottom:var(--sp-2);">
            <div class="chef-avatar"><img src="<?= asset(mascot('shiba')['avatar']) ?>" alt=""></div>
            <div class="chef-bubble"><?= $lang === 'zh' ? '猜猜看，拉麵妹藏在哪個杯子裡？' : 'Guess which cup Ramen Girl is hiding under!' ?></div>
        </div>
        <div class="cup-game-row" id="cupRow">
            <div class="cup-prize" id="cupPrize"><img src="<?= asset($rg['avatar']) ?>" alt=""></div>
            <button type="button" class="cup-btn" data-i="0" onclick="pickCup(this)"></button>
            <button type="button" class="cup-btn" data-i="1" onclick="pickCup(this)"></button>
            <button type="button" class="cup-btn" data-i="2" onclick="pickCup(this)"></button>
        </div>
        <div class="cup-result" id="cupResult" style="display:none;">
            <div class="code-pill"><?= htmlspecialchars($promo['code']) ?></div>
            <div class="cr-amount"><?= $lang === 'zh' ? '成功折抵 ' . money(Cart::discount()) . '！' : 'You saved ' . money(Cart::discount()) . '!' ?></div>
            <button class="btn btn-primary btn-block" style="margin-top:var(--sp-3);" onclick="closeSeal()"><?= $lang === 'zh' ? '太棒了！' : 'Amazing!' ?></button>
        </div>
    </div>
</div>
<script>
function closeSeal(){
    document.getElementById('sealModal').style.display = 'none';
}
function pickCup(btn){
    document.querySelectorAll('.cup-btn').forEach(b => b.disabled = true);
    const row = document.getElementById('cupRow');
    const prize = document.getElementById('cupPrize');
    const rowRect = row.getBoundingClientRect();
    const btnRect = btn.getBoundingClientRect();
    const centerX = (btnRect.left - rowRect.left) + (btnRect.width / 2);
    prize.style.left = centerX + 'px';
    btn.classList.add('lifted');
    prize.classList.add('show');
    if (navigator.vibrate) { try { navigator.vibrate([15, 30, 15]); } catch (e) {} }
    setTimeout(() => {
        document.getElementById('cupResult').style.display = 'block';
    }, 550);
}
</script>
<?php endif; ?>

<?php if ($promoResult === 'fail'): ?>
<script>
window.addEventListener('load', function(){
    const input = document.getElementById('promoInput');
    if (input) {
        input.style.animation = 'shake .4s';
        input.style.borderColor = 'var(--danger)';
    }
});
</script>
<style>@keyframes shake { 0%,100%{transform:translateX(0);} 25%{transform:translateX(-8px);} 75%{transform:translateX(8px);} }</style>
<?php endif; ?>
<script>
function stepLine(id, delta){
    const el = document.getElementById('qty-' + id);
    let val = Math.max(0, parseInt(el.textContent, 10) + delta);
    const form = document.getElementById('qtyForm');
    const hidden = document.getElementById('hidden-' + id);
    hidden.value = val;
    form.submit();
}
</script>
</body>
</html>
