<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

$lang = $_SESSION['lang'] ?? 'en';
$items = menu_items();
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — Kiosk</title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="attract-screen">
    <div class="attract-frame" onclick="location.href='menu.php'">
        <div class="attract-top">
            <span class="word">YO-KAI</span>
            <span class="sub">express</span>
            <div class="attract-tagline"><?= $lang === 'zh' ? '現點現做，新鮮出爐' : 'Hot. Fresh. Made to order.' ?></div>
        </div>

        <div class="attract-marquee-wrap">
            <div class="attract-marquee">
                <?php foreach (array_merge($items, $items) as $item): ?>
                    <div class="attract-card">
                        <div class="photo"><?= item_thumb_html($item) ?></div>
                        <div class="name"><?= htmlspecialchars(item_name($item)) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="attract-bottom">
            <span class="f-touch"><span class="hand">✋</span> <?= $lang === 'zh' ? '點擊任意處開始點餐' : 'TOUCH ANYWHERE TO START' ?></span>
            <span class="f-note"><?= $lang === 'zh' ? '照片僅供參考' : 'Photos are for illustration only.' ?></span>
        </div>
    </div>
</div>
</body>
</html>
