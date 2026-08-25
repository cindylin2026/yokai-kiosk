<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: menu.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= t('meal_ready') ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="ready-screen">
    <div class="fireworks-layer" id="fireworks"></div>

    <div class="ready-content" id="stageA">
        <div class="ready-order">#<?= htmlspecialchars($order['number']) ?></div>

        <div class="door-status"><span class="dot"></span> <?= $lang === 'zh' ? '出餐口已開啟，請取餐' : 'Pickup door is open — please collect your order' ?></div>

        <div class="ready-enjoy"><?= $lang === 'zh' ? '祝您用餐愉快！' : 'Enjoy your meal!' ?></div>
    </div>

</div>

<script>
const lang = <?= json_encode($lang) ?>;

function burstFireworks(){
    const layer = document.getElementById('fireworks');
    const colors = ['#c5a059', '#d9c08a', '#4caf6d', '#2b2b2a'];
    for (let i = 0; i < 26; i++) {
        const f = document.createElement('div');
        f.className = 'firework';
        const angle = Math.random() * Math.PI * 2;
        const dist = 80 + Math.random() * 160;
        f.style.setProperty('--fx', Math.cos(angle) * dist + 'px');
        f.style.setProperty('--fy', Math.sin(angle) * dist + 'px');
        f.style.left = (30 + Math.random() * 40) + '%';
        f.style.top = (15 + Math.random() * 30) + '%';
        f.style.background = colors[i % colors.length];
        f.style.animationDelay = (Math.random() * 0.3) + 's';
        layer.appendChild(f);
    }
    setTimeout(() => { layer.innerHTML = ''; }, 1500);
}
burstFireworks();

// No confirmation button here — the guest's hands are full holding the
// hot bowl, so the "I've got my bowl" moment is inferred from a timed
// dwell instead. Goes straight to the pickup message on load now —
// the earlier dual-hand-sensing unlock intro stage was cut entirely.
setTimeout(gotIt, 4800);

function gotIt(){
    window.location.href = 'index.php';
}
</script>
</body>
</html>
