<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if (!empty(Cart::items())) {
    $method = (string)($_GET['method'] ?? 'card');
    Cart::finalizeOrder($method);
    $_SESSION['stamps'] = min(5, (int)($_SESSION['stamps'] ?? 0) + 1);
}
$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: menu.php');
    exit;
}
$lang      = $_SESSION['lang'] ?? 'en';
$item      = $order['lines'][0]['item'] ?? null;
$totalSecs = isset($_GET['fast']) ? 10 : ($item['cook_time'] ?? 120);
$browseUrl = KIOSK_BASE_URL . '/menu.php?browse=1';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express</title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<style>
* { box-sizing: border-box; }
.pickup-screen {
  min-height: 100vh; width: 100%;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px;
}
.pickup-frame {
  width: calc(100vw - 36px); max-width: 1400px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 787px;
  background: #f6f4f0;
  border-radius: 22px; border: 1px solid rgba(197,160,89,0.28);
  box-shadow: 0 0 0 1px rgba(197,160,89,0.1), 0 30px 70px rgba(25,27,30,0.12);
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 5%;
  padding: 4% 6%;
  position: relative; overflow: hidden;
}

/* Logo top-left */
.pickup-logo {
  position: absolute; top: 4%; left: 4%;
}
.pickup-logo img { height: 28px; width: auto; }

/* Order number top-right */
.pickup-order-no {
  position: absolute; top: 4%; right: 4%;
  font-family: var(--font-mono); font-weight: 700; font-size: .82rem;
  letter-spacing: .06em; color: #8a7040;
  background: rgba(197,160,89,0.1); border: 1px solid rgba(197,160,89,0.25);
  border-radius: 999px; padding: 5px 16px;
}

/* Main headline */
.pickup-headline {
  font-family: var(--font-display); font-weight: 900;
  font-size: clamp(2rem, 5vw, 4rem);
  color: #2b2b2a; text-align: center; line-height: 1.15;
}
.pickup-headline .arrow { color: #c5a059; }

/* Sub line */
.pickup-sub {
  font-size: clamp(1.1rem, 2vw, 1.7rem);
  color: #6b6862; text-align: center; font-weight: 600;
}

/* QR code */
.pickup-qr {
  background: #fff; border-radius: 16px;
  border: 2px solid rgba(197,160,89,0.3);
  padding: 14px;
  box-shadow: 0 8px 28px rgba(25,27,30,0.08);
}
.pickup-qr img {
  display: block;
  width: clamp(200px, 26vw, 320px);
  height: auto;
}

/* Progress bar at very bottom */
.pickup-progress {
  position: absolute; bottom: 0; left: 0; right: 0;
  height: 4px; background: rgba(25,27,30,0.06);
}
.pickup-progress-bar {
  height: 100%; width: 0%;
  background: linear-gradient(90deg, #d9c08a, #c5a059);
  transition: width 1s linear;
}

/* Countdown overlay */
.pickup-countdown {
  position: absolute; inset: 0; z-index: 10;
  background: #f6f4f0;
  display: none; align-items: center; justify-content: center;
  font-family: var(--font-display); font-weight: 900;
  font-size: clamp(8rem, 20vw, 14rem); color: #c5a059;
}
</style>
</head>
<body>
<div class="pickup-screen">
  <div class="pickup-frame">

    <div class="pickup-logo">
      <img src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">
    </div>

    <div class="pickup-order-no">#<?= htmlspecialchars($order['number']) ?></div>

    <div class="pickup-headline">
      <?= $lang === 'zh' ? '取餐請至右側 <span class="arrow">→</span>' : 'Pick up on the right <span class="arrow">→</span>' ?>
    </div>

    <div class="pickup-sub">
      <?= $lang === 'zh'
        ? '下一位顧客：掃描下方 QR Code 搶先看菜單'
        : 'Next guest: scan the QR code below to browse the menu' ?>
    </div>

    <div class="pickup-qr">
      <img id="qrImg" alt="QR code">
    </div>

    <div class="pickup-progress">
      <div class="pickup-progress-bar" id="progressBar"></div>
    </div>

    <div class="pickup-countdown" id="countdown"></div>

  </div>
</div>

<script>
const total    = <?= (int)$totalSecs ?>;
const browseUrl = <?= json_encode($browseUrl) ?>;
let remaining  = total;

document.getElementById('qrImg').src =
  'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=2&data=' +
  encodeURIComponent(browseUrl);

const bar       = document.getElementById('progressBar');
const countdown = document.getElementById('countdown');

function tick() {
  if (remaining <= 0) { window.location.href = 'ready.php'; return; }

  bar.style.width = Math.min(100, ((total - remaining) / total) * 100) + '%';

  if (remaining <= 3) {
    countdown.style.display = 'flex';
    countdown.textContent   = remaining;
  }

  remaining--;
  setTimeout(tick, 1000);
}
tick();
</script>
</body>
</html>
