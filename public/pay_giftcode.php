<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if (empty(Cart::items())) {
    header('Location: cart.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — Gift Code</title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="light-screen">
    <?php render_topbar(false); ?>
    <?php render_stepper('payment'); ?>

    <div class="light-card">
        <p style="color:var(--text-on-light-soft);">Enter the code printed on your gift card.</p>
        <div class="code-input-row">
            <input type="text" id="codeInput" readonly value="" placeholder="••••••">
            <button class="icon-btn" style="background:var(--chip-bg);border-color:transparent;color:var(--accent);" onclick="toggleMask()" id="eyeBtn">👁️</button>
        </div>
        <div class="keypad">
            <?php for ($n = 1; $n <= 9; $n++): ?>
                <button type="button" onclick="press('<?= $n ?>')"><?= $n ?></button>
            <?php endfor; ?>
            <button type="button" onclick="backspace()">⌫</button>
            <button type="button" onclick="press('0')">0</button>
            <span></span>
        </div>
        <button class="btn btn-primary btn-block btn-lg" style="margin-top:var(--sp-4);" onclick="apply()">Apply</button>
        <p id="err" style="color:var(--danger);font-weight:600;display:none;margin-top:var(--sp-2);">Code not recognized — please check and try again.</p>
    </div>
</div>
<script>
let code = '';
let masked = true;
function render(){
    document.getElementById('codeInput').value = masked ? '•'.repeat(code.length) : code;
}
function press(d){ if (code.length < 12) { code += d; render(); } }
function backspace(){ code = code.slice(0, -1); render(); }
function toggleMask(){ masked = !masked; render(); }
function apply(){
    // Demo redemption: GIFT50 / GIFT25 apply a mock gift-card balance.
    const valid = { 'GIFT50': 50, 'GIFT25': 25 };
    if (valid[code]) {
        window.location.href = 'receipt.php?method=gift';
        return;
    } else {
        document.getElementById('err').style.display = 'block';
    }
}
</script>
</body>
</html>
